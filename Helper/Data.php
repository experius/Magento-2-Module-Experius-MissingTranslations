<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\View\Deployment\Version\StorageInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Translate\ResourceInterface;
use Magento\Framework\View\Design\Theme\ThemePackageList;
use Magento\Translation\Model\Js\Config;
use Psr\Log\LoggerInterface;

/**
 * Class Data
 * @package Experius\MissingTranslations\Helper
 */
class Data extends AbstractHelper
{
    const CONFIG_PATH_LANGUAGE_VENDOR = 'general/locale/language_vendor';

    /**
     * @var array
     */
    protected array $phrases = [];

    /**
     * @var DirectoryList
     */
    protected DirectoryList $directoryList;

    /**
     * @var File
     */
    protected File $driverFile;

    /**
     * @var ResourceInterface
     */
    protected ResourceInterface $translateResource;

    /**
     * @var StorageInterface
     */
    protected StorageInterface $versionStorage;

    /**
     * @var ThemePackageList
     */
    protected ThemePackageList $themePackageList;

    /**
     * @var IoFile
     */
    protected IoFile $file;

    /**
     * @var Json
     */
    protected Json $json;

    /**
     * @var Csv
     */
    protected Csv $csvProcessor;

    /**
     * @var DateTime
     */
    protected DateTime $dateTime;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var array
     */
    protected array $filters = [
        'url-rewrite',
        'admin',
        'cron',
        'import-export/',
        '/magento/module-deploy/',
        '/magento/module-backend/',
        '/magento/module-translation/',
        '/magento/module-support/',
        '/magento/module-versions-cms/',
        '/magento/module-visual-merchandiser/',
        '/magento/module-webapi/',
        '/magento/module-webapi-',
        '/magento/module-developer/',
        '/magento/module-cache-invalidate/',
        '/magento/module-encryption-key/',
        '/magento/module-indexer/',
        '/magento/module-message-queue/',
        '/magento/module-new-relic-reporting/',
        '/magento/module-resource-connections/',
        '/magento/module-logging/',
        '/test/unit/',
        '/magento/magento2-base/dev/',
        'etc/module.xml',
        'etc/acl.xml',
        'etc/widget.xml',
        'etc/indexer.xml',
        'import',
        'export',
        'experius/emailcatcher'
    ];

    /**
     * Data constructor.
     * @param Context $context
     * @param DirectoryList $directoryList
     * @param File $driverFile
     * @param ResourceInterface $translateResource
     * @param StorageInterface $versionStorage
     * @param ThemePackageList $themePackageList
     * @param IoFile $file
     * @param Json $json
     * @param Csv $csvProcessor
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        DirectoryList $directoryList,
        File $driverFile,
        ResourceInterface $translateResource,
        StorageInterface $versionStorage,
        ThemePackageList $themePackageList,
        IoFile $file,
        Json $json,
        Csv $csvProcessor,
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->directoryList = $directoryList;
        $this->driverFile = $driverFile;
        $this->translateResource = $translateResource;
        $this->versionStorage = $versionStorage;
        $this->themePackageList = $themePackageList;
        $this->file = $file;
        $this->json = $json;
        $this->csvProcessor = $csvProcessor;
        $this->dateTime = $dateTime;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * Update js-translation.json files in static content for specific locale
     * @param string|null $locale
     * @throws FileSystemException
     */
    public function updateJsTranslationJsonFiles(?string $locale = null): void
    {
        if (!$locale) {
            return;
        }

        $translations = $this->translateResource->getTranslationArray(null, $locale);
        $translationsJson = $this->json->serialize($translations);

        $themes = $this->themePackageList->getThemes();

        $staticVersionUpdateRequired = false;
        foreach (array_keys($themes) as $relativePath) {
            $jsonFilePath = $this->directoryList->getPath(DirectoryList::STATIC_VIEW) .
                DIRECTORY_SEPARATOR .
                $relativePath .
                DIRECTORY_SEPARATOR .
                $locale .
                DIRECTORY_SEPARATOR .
                Config::DICTIONARY_FILE_NAME;
            if ($this->driverFile->isExists($jsonFilePath)) {
                $this->driverFile->filePutContents($jsonFilePath, $translationsJson);
                $staticVersionUpdateRequired = true;
            }
        }

        if ($staticVersionUpdateRequired) {
            $this->updateStaticVersionNumber();
        }
    }

    /**
     * Updated static content version number
     */
    public function updateStaticVersionNumber(): void
    {
        $this->versionStorage->save($this->dateTime->timestamp());
    }

    /**
     * Get language vendor from configuration for current store
     *
     * @return string
     */
    public function getLanguageVendor(): string
    {
        return $this->scopeConfig->getValue(static::CONFIG_PATH_LANGUAGE_VENDOR);
    }

    /**
     * Get translation phrases from missing translation files (if generated)
     *
     * @param string $locale
     * @return array
     */
    public function getPhrases(string $locale = 'en_US'): array
    {
        try {
            if (empty($this->phrases[$locale]) && $filename = $this->getFileName($locale)) {
                $this->phrases[$locale] = $this->csvProcessor->getData($filename);
            } else {
                return [];
            }
        } catch (\Exception $e) {
            // Should not occur, since $this->getFileName() will already validate file existence
            return [];
        }
        return $this->phrases[$locale];
    }

    /**
     * Remove translation line from missing translation file
     *
     * @param string $string
     * @param string $locale
     */
    public function removeFromFile(string $string, string $locale = 'en_US'): void
    {
        $filename = $this->getFileName($locale);
        try {
            if ($filename) {
                $lines = $this->csvProcessor->getData($filename);
                foreach ($lines as $n => $line) {
                    if ($line[0] === $string) {
                        unset($lines[$n]);
                    }
                }
                $this->csvProcessor->appendData($filename, $lines);
            }
        } catch (\Exception $e) {
            // Should not occur, since $this->getFileName() will already validate file existence
        }
    }

    /**
     * Get filename of missing translation file based of locale
     *
     * @param string $locale
     * @param bool $requiredExists
     * @return string|null
     */
    public function getFileName(string $locale = 'en_US', bool $requiredExists = true): ?string
    {
        $directoryPath = $this->getTranslationDirectory();
        if ($directoryPath) {
            $filename = $directoryPath . $locale . '.csv';
            return (!$requiredExists || $this->file->fileExists($filename)) ? $filename : null;
        }
        return null;
    }

    protected function getTranslationDirectory(): ?string
    {
        $vendor = $this->getLanguageVendor();
        $directoryPath = $this->directoryList->getRoot() . '/app/i18n/' . $vendor . '/missing/';
        try {
            $result = $this->file->checkAndCreateFolder($directoryPath);
        } catch (LocalizedException $e) {
            /**
             * Magento will throw exception when app directory is not writable for Magento Cloud,
             * so use var instead
             */
            $directoryPath = $this->directoryList->getRoot() . '/var/i18n/' . $vendor . '/missing/';
            try {
                $result = $this->file->checkAndCreateFolder($directoryPath);
            } catch (LocalizedException $e) {
                $this->logger->error(
                    __(
                        'Failed to create translation directory at %1 - %2',
                        [
                            $directoryPath,
                            $e->getMessage()
                        ]
                    )
                );
                return null;
            }
        }
        return $result ? $directoryPath : null;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
}
