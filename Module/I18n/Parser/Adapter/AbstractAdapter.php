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

use Experius\MissingTranslations\Module\I18n\Dictionary\Phrase;
use Experius\MissingTranslations\Module\I18n\Parser\AdapterInterface;

/**
 * Abstract parser adapter
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * Processed file
     *
     * @var string
     */
    protected $_file;

    /**
     * Parsed phrases
     *
     * @var array
     */
    protected $_phrases = [];

    /**
     * {@inheritdoc}
     */
    public function parse($file)
    {
        $this->_phrases = [];
        $this->_file = $file;
        if (
            !strpos(strtolower($this->_file), 'adminhtml') && // exclude all adminhtml files
            !strpos(strtolower($this->_file), 'import-export/') &&
            !strpos(strtolower($this->_file), '/magento/module-backend/') &&
            !strpos(strtolower($this->_file), '/magento/module-admin-gws/') &&
            !strpos(strtolower($this->_file), '/magento/module-admin-notification/') &&
            !strpos(strtolower($this->_file), '/magento/module-translation/') &&
            !strpos(strtolower($this->_file), '/magento/module-support/') &&
            !strpos(strtolower($this->_file), '/magento/module-versions-cms/') &&
            !strpos(strtolower($this->_file), '/magento/module-visual-merchandiser/') &&
            !strpos(strtolower($this->_file), '/magento/module-webapi/') &&
            !strpos(strtolower($this->_file), '/magento/module-webapi-') &&
            !strpos(strtolower($this->_file), '/magento/module-developer/') &&
            !strpos(strtolower($this->_file), '/magento/module-cron/') &&
            !strpos(strtolower($this->_file), '/magento/module-catalog-url-rewrite/') &&
            !strpos(strtolower($this->_file), '/magento/module-catalog-url-rewrite-staging/') &&
            !strpos(strtolower($this->_file), '/magento/module-cache-invalidate/') &&
            !strpos(strtolower($this->_file), '/magento/module-encryption-key/') &&
            !strpos(strtolower($this->_file), '/magento/module-indexer/') &&
            !strpos(strtolower($this->_file), '/magento/module-message-queue/') &&
            !strpos(strtolower($this->_file), '/magento/module-new-relic-reporting/') &&
            !strpos(strtolower($this->_file), '/magento/module-resource-connections/') &&
            !strpos(strtolower($this->_file), '/magento/module-security/') &&
            !strpos(strtolower($this->_file), '/magento/module-logging/') &&
            !strpos($this->_file, '/Test/Unit/') &&
            !strpos($this->_file, '/magento/magento2-base/dev/')
        ) {
            $this->_parse();
        }
    }

    /**
     * Template method
     *
     * @return void
     */
    abstract protected function _parse();

    /**
     * {@inheritdoc}
     */
    public function getPhrases()
    {
        return array_values($this->_phrases);
    }

    /**
     * Add phrase
     *
     * @param string $phrase
     * @param string|int $line
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function _addPhrase($phrase, $line = '')
    {
        if (!$phrase) {
            return;
        }
        if (!isset($this->_phrases[$phrase])) {
            $enclosureCharacter = $this->getEnclosureCharacter($phrase);
            if (!empty($enclosureCharacter)) {
                $phrase = $this->trimEnclosure($phrase);
            }

            $this->_phrases[$phrase] = [
                'phrase' => $phrase,
                'file' => $this->_file,
                'line' => $line,
                'quote' => $enclosureCharacter,
            ];
        }
    }

    /**
     * Prepare phrase
     *
     * @param string $phrase
     * @return string
     */
    protected function _stripFirstAndLastChar($phrase)
    {
        return substr($phrase, 1, strlen($phrase) - 2);
    }

    /**
     * Check if first and last char is quote
     *
     * @param string $phrase
     * @return bool
     */
    protected function _isFirstAndLastCharIsQuote($phrase)
    {
        $firstCharacter = $phrase[0];
        $lastCharacter = $phrase[strlen($phrase) - 1];
        return $this->isQuote($firstCharacter) && $firstCharacter == $lastCharacter;
    }

    /**
     * Get enclosing character if any
     *
     * @param string $phrase
     * @return string
     */
    protected function getEnclosureCharacter($phrase)
    {
        $quote = '';
        if ($this->_isFirstAndLastCharIsQuote($phrase)) {
            $quote = $phrase[0];
        }

        return $quote;
    }

    /**
     * @param string $phrase
     * @return string
     */
    protected function trimEnclosure($phrase)
    {
        return $this->_stripFirstAndLastChar($phrase);
    }

    /**
     * @param string $char
     * @return bool
     */
    protected function isQuote($char)
    {
        return in_array($char, [Phrase::QUOTE_DOUBLE, Phrase::QUOTE_SINGLE]);
    }
}
