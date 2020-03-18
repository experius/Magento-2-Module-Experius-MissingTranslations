<?php
/**
 * Collect missing translations in specified folder or the entire Magento 2 Root
 * Copyright (C) 2016 Experius
 *
 * This file included in Experius/MissingTranslations is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */
namespace Experius\MissingTranslations\Module\I18n\Parser\Adapter;

/**
 * Js parser adapter
 */
class Js extends AbstractAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function _parse()
    {
        $fileHandle = @fopen($this->_file, 'r');
        $lineNumber = 0;
        if ($fileHandle) {
            while (!feof($fileHandle)) {
                $lineNumber++;
                $fileRow = fgets($fileHandle, 4096);
                $results = [];
                preg_match_all('/mage\.__\(\s*([\'"])(.*?[^\\\])\1.*?[),]/', $fileRow, $results, PREG_SET_ORDER);
                for ($i = 0; $i < count($results); $i++) {
                    if (isset($results[$i][2])) {
                        $quote = $results[$i][1];
                        $this->_addPhrase($quote . $results[$i][2] . $quote, $lineNumber);
                    }
                }

                preg_match_all('/\\$t\(\s*([\'"])(.*?[^\\\])\1.*?[),]/', $fileRow, $results, PREG_SET_ORDER);
                for ($i = 0; $i < count($results); $i++) {
                    if (isset($results[$i][2])) {
                        $quote = $results[$i][1];
                        $this->_addPhrase($quote . $results[$i][2] . $quote, $lineNumber);
                    }
                }
            }
            fclose($fileHandle);
        }
    }
}
