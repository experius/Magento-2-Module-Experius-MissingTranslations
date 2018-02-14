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

namespace Experius\MissingTranslations\Cron;

/**
 * Class Collect
 * @package Experius\MissingTranslations\Cron
 */
class Collect
{
    /**
     * @var \Experius\MissingTranslations\Model\Config\Source\Locale
     */
    protected $localeSourceModel;

    /**
     * @var \Experius\MissingTranslations\Model\TranslationCollector
     */
    protected $translationCollector;

    public function __construct(
        \Experius\MissingTranslations\Model\Config\Source\Locale $locale,
        \Experius\MissingTranslations\Model\TranslationCollector $translationCollector
    ) {
        $this->localeSourceModel = $locale;
        $this->translationCollector = $translationCollector;
    }

    /**
     * Executes collect cron, inserting all translations for active locales into the database on global scope
     */
    public function execute()
    {
        /** Global scope only for now, including missing translations */
        $storeId = 0;
        $includeMissing = true;
        $locales = $this->localeSourceModel->getLocaleMapping();

        foreach ($locales as $locale => $localeOptionArray) {
            $this->translationCollector->updateTranslationDatabase($storeId, $locale, $includeMissing);
        }
    }
}