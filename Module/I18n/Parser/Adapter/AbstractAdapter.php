<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Module\I18n\Parser\Adapter;

use Experius\MissingTranslations\Module\I18n\Dictionary\Phrase;
use Experius\MissingTranslations\Module\I18n\Parser\AdapterInterface;
use Magento\Framework\App\ObjectManager;

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
     * @var \Experius\MissingTranslations\Helper\Data
     */
    private $helper;

    /**
     * {@inheritdoc}
     */
    public function parse($file)
    {
        $this->_phrases = [];
        $this->_file = $file;
        $parse = true;
        foreach ($this->getHelper()->getFilters() as $filter) {
            if (strpos(strtolower($this->_file), strtolower($filter)) !== false) {
                $parse = false;
                break;
            }
        }
        if ($parse) {
            $this->_parse();
        }
    }

    /**
     * @return \Experius\MissingTranslations\Helper\Data|mixed
     */
    public function getHelper()
    {
        if (!$this->helper) {
            $objectManager = ObjectManager::getInstance();
            $this->helper = $objectManager->get(\Experius\MissingTranslations\Helper\Data::class);
        }
        return $this->helper;
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
