<?php
/**
 * A Magento 2 module named Experius/MissingTranslations
 * Copyright (C) 2018 Experius
 *
 * This file is part of Experius/MissingTranslations.
 *
 * Experius/MissingTranslations is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Experius\MissingTranslations\Console\Command;

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
     * @var Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * @var Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var Experius\MissingTranslations\Helper\Data
     */
    protected $helper;

    /**
     * CollectMissingTranslationsCommand constructor.
     * @param \Magento\Store\Model\App\Emulation $emulation
     * @param \Magento\Framework\App\State $state
     * @param \Experius\MissingTranslations\Helper\Data $helper
     */
    public function __construct(
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Framework\App\State $state,
        \Experius\MissingTranslations\Helper\Data $helper
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
