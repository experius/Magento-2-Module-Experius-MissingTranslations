<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Helper;

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
        $directoryPath = $this->directoryList->getRoot() . '/app/i18n/' . $vendor . '/missing/';
        if (!is_dir($directoryPath)) {
            @mkdir($directoryPath, 0777, true);
        }
        // Fallback for e.g., Magento Cloud, where /app directory has no write access
        if (!is_dir($directoryPath)) {
            $directoryPath = $this->directoryList->getRoot() . '/var/i18n/' . $vendor . '/missing/';
            if (!is_dir($directoryPath)) {
                @mkdir($directoryPath, 0777, true);
            }
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
