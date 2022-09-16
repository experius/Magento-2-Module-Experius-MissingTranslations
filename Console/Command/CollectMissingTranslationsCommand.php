<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Console\Command;

use Experius\MissingTranslations\Helper\Data;
use Magento\Framework\App\State;
use Magento\Store\Model\App\Emulation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Experius\MissingTranslations\Module\I18n\ServiceLocator;

/**
 * Class CollectMissingTranslationsCommand
 * @package Experius\MissingTranslations\Console\Command
 */
class CollectMissingTranslationsCommand extends Command
{
    const INPUT_KEY_DIRECTORY = 'directory';
    const INPUT_KEY_MAGENTO = 'magento';
    const SHORTCUT_KEY_MAGENTO = 'm';
    const INPUT_KEY_LOCALE = 'locale';
    const SHORTCUT_KEY_LOCALE = 'l';
    const INPUT_KEY_STORE = 'store';
    const SHORTCUT_KEY_STORE = 's';

    /**
     * @var Emulation
     */
    protected Emulation $emulation;

    /**
     * @var State
     */
    protected State $state;

    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * CollectMissingTranslationsCommand constructor.
     * @param Emulation $emulation
     * @param State $state
     * @param Data $helper
     */
    public function __construct(
        Emulation $emulation,
        State $state,
        Data $helper
    ) {
        $this->emulation = $emulation;
        $this->state = $state;
        $this->helper = $helper;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument(self::INPUT_KEY_DIRECTORY);
        if ($input->getOption(self::INPUT_KEY_MAGENTO)) {
            $directory = BP;
            if ($input->getArgument(self::INPUT_KEY_DIRECTORY)) {
                throw new \InvalidArgumentException('Directory path is not needed when --magento flag is set.');
            }
        } elseif (!$input->getArgument(self::INPUT_KEY_DIRECTORY)) {
            throw new \InvalidArgumentException('Directory path is needed when --magento flag is not set.');
        }
        if (!$input->getOption(self::INPUT_KEY_LOCALE)) {
            throw new \InvalidArgumentException('Locale is not set. Please use --locale to set locale');
        }
        $generator = ServiceLocator::getDictionaryGenerator();
        $this->state->setAreaCode('frontend');
        $this->emulation->startEnvironmentEmulation($input->getOption(self::INPUT_KEY_STORE));

        $fileName = $this->helper->getFileName($input->getOption(self::INPUT_KEY_LOCALE), false);
        $generator->generate(
            $directory,
            $fileName,
            $input->getOption(self::INPUT_KEY_MAGENTO),
            $input->getOption(self::INPUT_KEY_LOCALE)
        );
        $this->emulation->stopEnvironmentEmulation();
        $output->writeln('<info>Collected Missing Translations for specified store and stored in ' . $fileName . ' </info>');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("experius_missingtranslations:collect");
        $this->setDescription('Collect all missing translations by the language code.');
        $this->setDefinition([
            new InputArgument(
                self::INPUT_KEY_DIRECTORY,
                InputArgument::OPTIONAL,
                'Directory path to parse. Not needed if --magento flag is set'
            ),
            new InputOption(
                self::INPUT_KEY_MAGENTO,
                self::SHORTCUT_KEY_MAGENTO,
                InputOption::VALUE_NONE,
                'Use the --magento parameter to parse the current Magento codebase.' .
                ' Omit the parameter if a directory is specified.'
            ),
            new InputOption(
                self::INPUT_KEY_LOCALE,
                self::SHORTCUT_KEY_LOCALE,
                InputOption::VALUE_REQUIRED,
                'Use the --locale parameter to parse specific language.'
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
