<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Experius\MissingTranslations\Helper;

use Magento\Framework\DataObject;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directory_list;

    /**
     * @var array
     */
    protected $phrases = [];

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directory_list
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list
    )
    {
        $this->directory_list = $directory_list;

        parent::__construct($context);
    }

    public function getLanguageVendor()
    {
        return $this->scopeConfig->getValue("general/locale/language_vendor", \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getPhrases($locale = 'en_US')
    {
        $this->phrases = array();
        $filename = $this->getFileName($locale);
        if ($filename) {
            $this->phrases = array_map('str_getcsv', file($filename));
        }
        return $this->phrases;
    }

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


    public function getFileName($locale = 'en_US')
    {
        $vendor = $this->getLanguageVendor();
        $filename = $this->directory_list->getRoot() . '/app/i18n/'. $vendor . '/' . strtolower($locale) . '/' . $locale . '-missing.csv';

        return (file_exists($filename)) ? $filename : false;
    }
}