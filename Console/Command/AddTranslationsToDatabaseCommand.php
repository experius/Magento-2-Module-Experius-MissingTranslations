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
     * @var Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Experius\MissingTranslations\Model\TranslationCollector
     */
    protected $translationCollector;

    /**
     * AddTranslationsToDatabaseCommand constructor.
     *
     * @param \Magento\Framework\App\State $state
     * @param \Experius\MissingTranslations\Model\TranslationCollector $translationCollector
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Experius\MissingTranslations\Model\TranslationCollector $translationCollector
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

        $includeMissing = false;
        if ($input->getOption(self::INPUT_KEY_INCLUDEMISSING)) {
            $includeMissing = true;
        }

        $output->writeln(
            'Inserting all csv translations'
            . ($includeMissing ? ', including missing translations,' : '')
            . ' into database for store id <info>' . $storeId
            . '</info> and locale <info>' . $locale . '</info>'
        );
        $output->writeln('Still working... One moment.');

        $insertionCount = $this->translationCollector->updateTranslationDatabase($storeId, $locale, $includeMissing);

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