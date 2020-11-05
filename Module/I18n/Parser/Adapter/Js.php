<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

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
        $fileHandle = file_exists($this->_file) ? fopen($this->_file, 'r') : false;
        $lineNumber = 0;
        if ($fileHandle) {
            while (!feof($fileHandle)) {
                $lineNumber++;
                $fileRow = fgets($fileHandle, 4096);
                if ($fileRow) {
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
            }
            fclose($fileHandle);
        }
    }
}
