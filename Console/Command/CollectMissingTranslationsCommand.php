<?php

namespace Experius\MissingTranslations\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\ObjectManager;
use Experius\MissingTranslations\Module\I18n\ServiceLocator;

class CollectMissingTranslationsCommand extends Command
{

    const INPUT_KEY_DIRECTORY = 'directory';
    const INPUT_KEY_OUTPUT = 'output';
    const SHORTCUT_KEY_OUTPUT = 'o';
    const INPUT_KEY_MAGENTO = 'magento';
    const SHORTCUT_KEY_MAGENTO = 'm';
    const INPUT_KEY_STORE = 'store';
    const SHORTCUT_KEY_STORE = 's';

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
        $generator = ServiceLocator::getDictionaryGenerator();
        $objectManager = ObjectManager::getInstance();
        $emulation = $objectManager->create('\Magento\Store\Model\App\Emulation');
        $appState = $objectManager->get('Magento\Framework\App\State');
        $appState->setAreaCode('frontend');
        $emulation->startEnvironmentEmulation($input->getOption(self::INPUT_KEY_STORE));
        $generator->generate(
            $directory,
            $input->getOption(self::INPUT_KEY_OUTPUT),
            $input->getOption(self::INPUT_KEY_MAGENTO)

        );
        $emulation->stopEnvironmentEmulation();
        $output->writeln('<info>Collected Missing Translations for specified store</info>');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("experius_missingtranslations:collect");
        $this->setDescription('Collect all missing translations by the language of the given store.');
        $this->setDefinition([
            new InputArgument(
                self::INPUT_KEY_DIRECTORY,
                InputArgument::OPTIONAL,
                'Directory path to parse. Not needed if --magento flag is set'
            ),
            new InputOption(
                self::INPUT_KEY_OUTPUT,
                self::SHORTCUT_KEY_OUTPUT,
                InputOption::VALUE_REQUIRED,
                'Path (including filename) to an output file. With no file specified, defaults to stdout.'
            ),
            new InputOption(
                self::INPUT_KEY_MAGENTO,
                self::SHORTCUT_KEY_MAGENTO,
                InputOption::VALUE_NONE,
                'Use the --magento parameter to parse the current Magento codebase.' .
                ' Omit the parameter if a directory is specified.'
            ),
            new InputOption(
                self::INPUT_KEY_STORE,
                self::SHORTCUT_KEY_STORE,
                InputOption::VALUE_REQUIRED,
                'Use the --store parameter to parse store.' .
                ' Omit the parameter if a directory is specified.'
            ),
        ]);
        parent::configure();
    }


}
