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

namespace Experius\MissingTranslations\Helper;

use Magento\Framework\DataObject;

/**
 * Class Data
 * @package Experius\MissingTranslations\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $phrases = [];

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $driverFile;

    /**
     * @var \Magento\Framework\Translate\ResourceInterface
     */
    protected $translateResource;

    /**
     * @var \Magento\Framework\App\View\Deployment\Version\StorageInterface
     */
    protected $versionStorage;

    /**
     * @var \Magento\Framework\View\Design\Theme\ThemePackageList
     */
    protected $themePackageList;

    /**
     * @var array
     */
    protected $filters = [
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
        '/magento/module-security/',
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
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\App\View\Deployment\Version\StorageInterface $versionStorage
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magento\Framework\Translate\ResourceInterface $translateResource,
        \Magento\Framework\App\View\Deployment\Version\StorageInterface $versionStorage,
        \Magento\Framework\View\Design\Theme\ThemePackageList $themePackageList
    ) {
        $this->directoryList = $directoryList;
        $this->driverFile = $driverFile;
        $this->translateResource = $translateResource;
        $this->versionStorage = $versionStorage;
        $this->themePackageList = $themePackageList;

        parent::__construct($context);
    }

    /**
     * Update js-translation.json files in static content for specific locale
     */
    public function updateJsTranslationJsonFiles($locale = null)
    {
        if (!$locale) {
            return;
        }

        $translations = $this->translateResource->getTranslationArray(null, $locale);
        $translationsJson = json_encode($translations);

        $themes = $this->themePackageList->getThemes();

        $staticVersionUpdateRequired = false;
        foreach ($themes as $relativePath => $theme) {
            $jsonFilePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::STATIC_VIEW) .
                \DIRECTORY_SEPARATOR .
                $relativePath .
                \DIRECTORY_SEPARATOR .
                $locale .
                \DIRECTORY_SEPARATOR .
                \Magento\Translation\Model\Js\Config::DICTIONARY_FILE_NAME;
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
    public function updateStaticVersionNumber()
    {
        $version = (new \DateTime())->getTimestamp();
        $this->versionStorage->save($version);
    }

    /**
     * Get language vendor from configuration for current store
     *
     * @return string
     */
    public function getLanguageVendor()
    {
        return $this->scopeConfig->getValue('general/locale/language_vendor', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get translation phrases from missing translation files (if generated)
     *
     * @return array
     */
    public function getPhrases($locale = 'en_US')
    {
        $this->phrases = [];
        $filename = $this->getFileName($locale);
        if ($filename) {
            $this->phrases = array_map('str_getcsv', file($filename));
        }
        return $this->phrases;
    }

    /**
     * Remove translation line from missing translation file
     *
     * @param bool $line
     * @param string $locale
     */
    public function removeFromFile($line = false, $locale = 'en_US')
    {
        if ($line) {
            $filename = $this->getFileName($locale);
            if ($filename) {
                $lines = file($filename);
                unset($lines[$line]);
                // write the new data to the file
                $fp = fopen($filename, 'w');
                fwrite($fp, implode('', $lines));
                fclose($fp);
            }
        }
    }

    /**
     * Get filename of missing translation file based of locale
     *
     * @param string $locale
     * @param bool $requiredExists
     * @return bool|string
     */
    public function getFileName($locale = 'en_US', $requiredExists = true)
    {
        $vendor = $this->getLanguageVendor();
        $directoryPath = $this->directoryList->getRoot() . '/app/i18n/'. $vendor . '/missing/';
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }
        $filename = $directoryPath . $locale . '.csv';

        return (file_exists($filename) || $requiredExists == false) ? $filename : false;
    }



    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }
}
