<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Cron;

use Experius\MissingTranslations\Model\Config\Source\Locale;
use Experius\MissingTranslations\Model\TranslationCollector;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Collect
 * @package Experius\MissingTranslations\Cron
 */
class Collect
{
    const XML_PATH_EXISTING_TRANSLATIONS_CRON = 'general/locale/cron_existing_translations';
    const XML_PATH_MISSING_TRANSLATIONS_CRON = 'general/locale/cron_missing_translations';

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var Locale
     */
    protected Locale $localeSourceModel;

    /**
     * @var TranslationCollector
     */
    protected TranslationCollector $translationCollector;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Locale $locale
     * @param TranslationCollector $translationCollector
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Locale $locale,
        TranslationCollector $translationCollector
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->localeSourceModel = $locale;
        $this->translationCollector = $translationCollector;
    }

    /**
     * Executes collect cron, inserting all translations for active locales into the database on global scope
     */
    public function existingTranslations(): void
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
                TranslationCollector::TRANSLATION_TYPE_EXISTING
            );
        }
    }

    /**
     * Insert all missing translations found in missing translation files
     * for active locales into the database on global scope
     */
    public function missingTranslations(): void
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
                TranslationCollector::TRANSLATION_TYPE_MISSING
            );
        }
    }
}
