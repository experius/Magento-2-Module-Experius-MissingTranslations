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
        $this->phrases = array();
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
     * @return bool|string
     */
    public function getFileName($locale = 'en_US')
    {
        $vendor = $this->getLanguageVendor();
        $filename = $this->directory_list->getRoot() . '/app/i18n/'. $vendor . '/missing/' . $locale . '.csv';

        return (file_exists($filename)) ? $filename : false;
    }
}