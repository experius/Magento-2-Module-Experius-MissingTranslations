<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Model;

use Experius\MissingTranslations\Helper\Data;
use Experius\MissingTranslations\Module\I18n\Parser\Parser;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\App\Emulation;
use Magento\Translation\Model\ResourceModel\Translate;
use Psr\Log\LoggerInterface;

/**
 * Class TranslationCollector
 * @package Experius\MissingTranslations\Model
 */
class TranslationCollector
{
    const TRANSLATION_TYPE_EXISTING = 'existing';
    const TRANSLATION_TYPE_MISSING = 'missing';

    /**
     * @var Emulation
     */
    protected Emulation $emulation;

    /**
     * @var Parser
     */
    protected Parser $parser;

    /**
     * @var TranslationFactory
     */
    protected TranslationFactory $translationFactory;

    /**
     * @var Translate
     */
    protected Translate $translateModel;

    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * @var TranslationRepository
     */
    protected TranslationRepository $translationRepository;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;
    /**
     * @param Emulation $emulation
     * @param Parser $parser
     * @param TranslationFactory $translationFactory
     * @param Translate $translateModel
     * @param Data $helper
     * @param TranslationRepository $translationRepository
     */
    public function __construct(
        Emulation          $emulation,
        Parser             $parser,
        TranslationFactory $translationFactory,
        Translate          $translateModel,
        Data               $helper,
        TranslationRepository $translationRepository,
        LoggerInterface $logger
    ) {
        $this->emulation = $emulation;
        $this->parser = $parser;
        $this->translationFactory = $translationFactory;
        $this->translateModel = $translateModel;
        $this->helper = $helper;
        $this->translationRepository = $translationRepository;
        $this->logger = $logger;
    }

    /**
     * @param int $storeId
     * @param string $locale
     * @param string $translationType
     * @return int
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function updateTranslationDatabase(int $storeId, string $locale, string $translationType = ''): int
    {
        if (!in_array($translationType, [self::TRANSLATION_TYPE_MISSING, self::TRANSLATION_TYPE_EXISTING])) {
            return 0;
        }

        $this->emulation->startEnvironmentEmulation($storeId);

        if ($translationType == self::TRANSLATION_TYPE_EXISTING) {
            $translations = $this->collectTranslations($locale);
        } else {
            $translations = $this->collectMissingTranslations($locale);
        }

        $databaseTranslations = $this->translateModel->getTranslationArray($storeId, $locale);
        $translations = array_diff_key($translations, $databaseTranslations);

        $insertionCount = $this->createNewTranslations($translations, $storeId, $locale);

        $this->emulation->stopEnvironmentEmulation();
        return $insertionCount;
    }

    /**
     * Collect translations based on locale
     *
     * @param $locale
     * @return array
     */
    public function collectTranslations($locale): array
    {
        $this->parser->loadTranslations($locale);
        return $this->parser->getTranslations();
    }

    /**
     * Collect missing translations as array based on locale
     *
     * @param $locale
     * @return array
     */
    public function collectMissingTranslations($locale): array
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
     * @param $storeId
     * @param $locale
     * @return int
     */
    public function createNewTranslations($translations, $storeId, $locale): int
    {
        $insertionCount = 0;
        foreach ($translations as $originalString => $translate) {
            /**
             * Explicitly cast to string since community engineering translations can contain phrases
             * that Magento parsing returns as integer
             */
            $originalString = (string) $originalString;
            $translate = (string) $translate;
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
                $this->translationRepository->save($translation);
                $insertionCount++;
            } catch (LocalizedException $e) {
                $this->logger->info(
                    __("Could not save translation for '%1' - %2",
                        [$originalString, $e->getMessage()]
                    )
                );
            }
        }
        return $insertionCount;
    }
}
