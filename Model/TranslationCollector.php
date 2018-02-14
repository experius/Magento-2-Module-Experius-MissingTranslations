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

namespace Experius\MissingTranslations\Model;

/**
 * Class TranslationCollector
 * @package Experius\MissingTranslations\Model
 */
class TranslationCollector
{
    /**
     * @var Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * @var \Experius\MissingTranslations\Module\I18n\Parser\Parser
     */
    protected $parser;

    /**
     * @var \Experius\MissingTranslations\Model\TranslationFactory
     */
    protected $translationFactory;

    /**
     * @var \Magento\Translation\Model\ResourceModel\Translate
     */
    protected $translateModel;

    /**
     * @var \Experius\MissingTranslations\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Magento\Store\Model\App\Emulation $emulation,
        \Experius\MissingTranslations\Module\I18n\Parser\Parser $parser,
        \Experius\MissingTranslations\Model\TranslationFactory $translationFactory,
        \Magento\Translation\Model\ResourceModel\Translate $translateModel,
        \Experius\MissingTranslations\Helper\Data $helper
    ) {
        $this->emulation = $emulation;
        $this->parser = $parser;
        $this->translationFactory = $translationFactory;
        $this->translateModel = $translateModel;
        $this->helper = $helper;
    }

    public function updateTranslationDatabase($storeId, $locale, $includeMissing = true)
    {
        $this->emulation->startEnvironmentEmulation($storeId);

        $translations = $this->collectTranslations($locale);

        if ($includeMissing) {
            $missingTranslations = $this->collectMissingTranslations($locale);
            if (!empty($missingTranslations)) {
                $translations = array_merge($translations, $missingTranslations);
            }
        }

        $existingTranslation = $this->translateModel->getTranslationArray($storeId, $locale);
        $translations = array_diff_key($translations, $existingTranslation);

        $insertionCount = $this->createNewTranslations($translations, $storeId, $locale);

        $this->emulation->stopEnvironmentEmulation();
        return $insertionCount;
    }

    /**
     * Collect translations based on locale
     *
     * @param $locale
     * @return mixed
     */
    public function collectTranslations($locale)
    {
        $this->parser->loadTranslations($locale);
        $translations = $this->parser->getTranslations();
        return $translations;
    }

    /**
     * Collect missing translations as array based on locale
     *
     * @param $locale
     * @return array
     */
    public function collectMissingTranslations($locale)
    {
        $missingPhrases = $this->helper->getPhrases($locale);
        $missingTranslations = array();
        foreach ($missingPhrases as $phrase) {
            $missingTranslations[$phrase[0]] = $phrase[0];
        }
        return $missingTranslations;
    }

    /**
     * Create new translations
     *
     * @param $translations
     * @return int
     */
    public function createNewTranslations($translations, $storeId, $locale)
    {
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
                'store_id' => $storeId,
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
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
        return $insertionCount;
    }

}