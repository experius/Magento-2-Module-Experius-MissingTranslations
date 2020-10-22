<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Cron;

/**
 * Class Collect
 * @package Experius\MissingTranslations\Cron
 */
class Collect
{
    const XML_PATH_EXISTING_TRANSLATIONS_CRON = 'general/locale/cron_existing_translations';
    const XML_PATH_MISSING_TRANSLATIONS_CRON = 'general/locale/cron_missing_translations';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Experius\MissingTranslations\Model\Config\Source\Locale
     */
    protected $localeSourceModel;

    /**
     * @var \Experius\MissingTranslations\Model\TranslationCollector
     */
    protected $translationCollector;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Experius\MissingTranslations\Model\Config\Source\Locale $locale,
        \Experius\MissingTranslations\Model\TranslationCollector $translationCollector
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->localeSourceModel = $locale;
        $this->translationCollector = $translationCollector;
    }

    /**
     * Executes collect cron, inserting all translations for active locales into the database on global scope
     */
    public function existingTranslations()
    {
        if (!$this->scopeConfig->isSetFlag(self::XML_PATH_EXISTING_TRANSLATIONS_CRON)) {
            return;
        }

        /** Global scope only for now */
        $storeId = 0;
        $locales = $this->localeSourceModel->getLocaleMapping();

        foreach (array_keys($locales) as $locale) {
            $this->translationCollector->updateTranslationDatabase(
                $storeId,
                $locale,
                \Experius\MissingTranslations\Model\TranslationCollector::TRANSLATION_TYPE_EXISTING
            );
        }
    }

    /**
     * Insert all missing translations found in missing translation files
     * for active locales into the database on global scope
     */
    public function missingTranslations()
    {
        if (!$this->scopeConfig->isSetFlag(self::XML_PATH_MISSING_TRANSLATIONS_CRON)) {
            return;
        }

        /** Global scope only for now */
        $storeId = 0;
        $locales = $this->localeSourceModel->getLocaleMapping();

        foreach (array_keys($locales) as $locale) {
            $this->translationCollector->updateTranslationDatabase(
                $storeId,
                $locale,
                \Experius\MissingTranslations\Model\TranslationCollector::TRANSLATION_TYPE_MISSING
            );
        }
    }
}
