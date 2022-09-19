<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Console\Command;

use Experius\MissingTranslations\Model\TranslationCollector;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MissingTranslationsToDatabase
 *
 * @package Experius\MissingTranslations\Console\Command
 */
class MissingTranslationsToDatabase extends Command
{
    const INPUT_KEY_LOCALE = 'locale';
    const SHORTCUT_KEY_LOCALE = 'l';
    const INPUT_KEY_STORE = 'store';
    const SHORTCUT_KEY_STORE = 's';
    const INPUT_KEY_GLOBAL = 'global';
    const SHORTCUT_KEY_GLOBAL = 'g';

    /**
     * @var State
     */
    protected State $state;

    /**
     * @var TranslationCollector
     */
    protected TranslationCollector $translationCollector;

    /**
     * AddTranslationsToDatabaseCommand constructor.
     *
     * @param State $state
     * @param TranslationCollector $translationCollector
     */
    public function __construct(
        State $state,
        TranslationCollector $translationCollector
    ) {
        $this->state = $state;
        $this->translationCollector = $translationCollector;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode('frontend');

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

        $storeId = $global ? '0' : $input->getOption(self::INPUT_KEY_STORE);

        $output->writeln(
            'Inserting all missing translations into database for store id <info>' . $storeId
            . '</info> and locale <info>' . $locale . '</info>'
        );
        $output->writeln('Still working... One moment.');

        $insertionCount = $this->translationCollector->updateTranslationDatabase(
            (int)$storeId,
            $locale,
            TranslationCollector::TRANSLATION_TYPE_MISSING
        );

        if ($insertionCount > 0) {
            $output->writeln('Insertion was successful, <info>'
                . $insertionCount . '</info> translations added to database');
        } else {
            $output->writeln('Nothing was inserted. All found missing translations already present in database.');
        }

        return Cli::RETURN_SUCCESS;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('experius_missingtranslations:missing-translations-to-database');
        $this->setDescription('Add all missing translations to database for easy editing');
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
                self::INPUT_KEY_STORE,
                self::SHORTCUT_KEY_STORE,
                InputArgument::OPTIONAL,
                'Use the --store parameter to parse store. (for DB translation check)'
            )
        ]);
        parent::configure();
    }
}
