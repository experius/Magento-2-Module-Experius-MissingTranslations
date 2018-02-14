<?php
/**
 * Add CSV translations as database translations
 * Copyright (C) 2018 Experius
 *
 * This file included in Experius/MissingTranslations is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Experius\MissingTranslations\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AddTranslationsToDatabaseCommand
 * @package Experius\MissingTranslations\Console\Command
 */
class AddTranslationsToDatabaseCommand extends Command
{
    const INPUT_KEY_LOCALE = 'locale';
    const SHORTCUT_KEY_LOCALE = 'l';
    const INPUT_KEY_STORE = 'store';
    const SHORTCUT_KEY_STORE = 's';
    const INPUT_KEY_GLOBAL = 'global';
    const SHORTCUT_KEY_GLOBAL = 'g';
    const INPUT_KEY_INCLUDEMISSING = 'include-missing';
    const SHORTCUT_KEY_INCLUDEMISSING = 'im';

    /**
     * @var Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * @var Magento\Framework\App\State
     */
    protected $state;

    /**
     * Parser
     *
     * @var \Experius\MissingTranslations\Module\I18n\Parser\Parser
     */
    protected $parser;

    /**
     * Translation
     *
     * @var \Experius\MissingTranslations\Model\TranslationFactory
     */
    protected $translationFactory;

    /**
     * Translatemodel
     *
     * @var \Magento\Translation\Model\ResourceModel\Translate
     */
    protected $translateModel;

    /**
     * Helper
     *
     * @var \Experius\MissingTranslations\Helper\Data
     */
    protected $helper;

    /**
     * CollectMissingTranslationsCommand constructor.
     * @param \Magento\Store\Model\App\Emulation $emulation
     * @param \Magento\Framework\App\State $state
     * @param \Experius\MissingTranslations\Module\I18n\Parser\Parser $parser
     * @param \Experius\MissingTranslations\Model\Translation $translation
     * @param \Magento\Translation\Model\ResourceModel\Translate $translateModel ,
     * @param \Experius\MissingTranslations\Helper\Data $helper
     */
    public function __construct(
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Framework\App\State $state,
        \Experius\MissingTranslations\Module\I18n\Parser\Parser $parser,
        \Experius\MissingTranslations\Model\TranslationFactory $translationFactory,
        \Magento\Translation\Model\ResourceModel\Translate $translateModel,
        \Experius\MissingTranslations\Helper\Data $helper
    ) {
        $this->emulation = $emulation;
        $this->state = $state;
        $this->parser = $parser;
        $this->translationFactory = $translationFactory;
        $this->translateModel = $translateModel;
        $this->helper = $helper;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode('frontend');
        $this->emulation->startEnvironmentEmulation($input->getOption(self::INPUT_KEY_STORE));

        if (!$input->getOption(self::INPUT_KEY_LOCALE)) {
            throw new \InvalidArgumentException('Locale is not set. Please use --locale to set locale');
        }

        $locale = $input->getOption(self::INPUT_KEY_LOCALE);

        $global = false;
        if ($input->getOption(self::INPUT_KEY_GLOBAL)) {
            $global = true;
            if ($input->getOption(self::INPUT_KEY_STORE)) {
                throw new \InvalidArgumentException('Store is not needed when --global flag is set.');
            }
        } elseif (!$input->getOption(self::INPUT_KEY_STORE)) {
            throw new \InvalidArgumentException('Store is needed when --global flag is not set.');
        }

        $store = $global ? '0' : $input->getOption(self::INPUT_KEY_STORE);

        $includeMissing = false;
        if ($input->getOption(self::INPUT_KEY_INCLUDEMISSING)) {
            $includeMissing = true;
        }

        $output->writeln(
            'Inserting all csv translations'
            . ($includeMissing ? ', including missing translations,' : '')
            . ' into database for store id <info>' . $store
            . '</info> and locale <info>' . $locale . '</info>'
        );
        $output->writeln('Still working... One moment.');

        $this->parser->loadTranslations($locale);
        $translations = $this->parser->getTranslations();

        if ($includeMissing) {
            $missingPhrases = $this->helper->getPhrases($locale);
            $missingTranslations = array();
            foreach ($missingPhrases as $phrase) {
                $missingTranslations[$phrase[0]] = $phrase[0];
            }
            if (!empty($missingTranslations)) {
                $translations = array_merge($translations, $missingTranslations);
            }
        }

        $existingTranslation = $this->translateModel->getTranslationArray($store, $locale);
        $translations = array_diff_key($translations, $existingTranslation);

        $insertionCount = 0;
        foreach ($translations as $key => $value) {
            /**
             * Due to Magento table limitation strings longer than 255 characters are being cut off, so these are excluded for now
             */
            if (strlen($key) > 255 || strlen($value) > 255) {
                continue;
            }

            $different = ($value == $key) ? 0 : 1;

            $data = array(
                'translate' => $value,
                'store_id' => $store,
                'locale' => $locale,
                'string' => $key,
                'crc_string' => crc32($key),
                'different' => $different
            );
            $translation = $this->translationFactory->create();
            $translation->setData($data);
            try {
                $translation->save();
                $insertionCount++;
            } catch (Exception $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
            }
        }

        $this->emulation->stopEnvironmentEmulation();
        if ($insertionCount > 0) {
            $output->writeln('Insertion was successful, <info>' . $insertionCount . '</info> translations added');
        } else {
            $output->writeln('All translations were already present for this store and locale. Nothing was inserted.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("experius_missingtranslations:addtodatabase");
        $this->setDescription('Add all csv translations to database for easy editing');
        $this->setDefinition([
            new InputOption(
                self::INPUT_KEY_LOCALE,
                self::SHORTCUT_KEY_LOCALE,
                InputOption::VALUE_REQUIRED,
                'Use the --locale parameter to parse specific language.'
            ),
            new InputOption(
                self::INPUT_KEY_GLOBAL,
                self::SHORTCUT_KEY_GLOBAL,
                InputOption::VALUE_NONE,
                'Use the --global parameter to add translations as global.' .
                ' Omit the parameter if a store is specified.'
            ),
            new InputOption(
                self::INPUT_KEY_INCLUDEMISSING,
                self::SHORTCUT_KEY_INCLUDEMISSING,
                InputOption::VALUE_NONE,
                'Use the --include-missing parameter to also add missing translations.' .
                'Please run the collect action first to collect the missing translations.' .
                'Omit the parameter to only add translated strings.'
            ),
            new InputOption(
                self::INPUT_KEY_STORE,
                self::SHORTCUT_KEY_STORE,
                InputArgument::OPTIONAL,
                'Use the --store parameter to parse store. (for DB translation check)'
            ),
        ]);
        parent::configure();
    }
}