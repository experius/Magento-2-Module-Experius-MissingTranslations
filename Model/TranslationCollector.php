<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Model;

/**
 * Class TranslationCollector
 * @package Experius\MissingTranslations\Model
 */
class TranslationCollector
{
    const TRANSLATION_TYPE_EXISTING = 'existing';
    const TRANSLATION_TYPE_MISSING = 'missing';

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

    public function updateTranslationDatabase($storeId, $locale, $translationType = '')
    {
        if (!in_array($translationType, [self::TRANSLATION_TYPE_MISSING, self::TRANSLATION_TYPE_EXISTING])) {
            return false;
        }

        $this->emulation->startEnvironmentEmulation($storeId);

        if ($translationType == self::TRANSLATION_TYPE_EXISTING) {
            $translations = $this->collectTranslations($locale);
        } elseif ($translationType == self::TRANSLATION_TYPE_MISSING) {
            $translations = $this->collectMissingTranslations($locale);
        }

        $databaseTranslations = $this->translateModel->getTranslationArray($storeId, $locale);
        $translations = array_diff_key($translations, $databaseTranslations);

        $insertionCount = $this->createNewTranslations($translations, $storeId, $locale);
        
        $this->helper->updateJsTranslationJsonFiles($locale);

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
        $missingTranslations = [];
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
        foreach ($translations as $originalString => $translate) {
            /**
             * Due to Magento table limitation strings longer than 255 characters
             * are being cut off, so these are excluded for now
             */
            if (strlen($originalString) > 255 || strlen($translate) > 255) {
                continue;
            }

            /** Filter empty value's */
            if (empty($originalString)) {
                continue;
            }

            /** Make translation identical to original string if no translation was found */
            if (empty($translate)) {
                $translate = $originalString;
            }

            $different = ($translate == $originalString) ? 0 : 1;

            $data = [
                'translate' => $translate,
                'store_id' => $storeId,
                'locale' => $locale,
                'string' => $originalString,
                'crc_string' => crc32($originalString),
                'different' => $different
            ];
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
