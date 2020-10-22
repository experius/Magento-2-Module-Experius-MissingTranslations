<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Module\I18n\Parser;

use \Magento\Framework\Translate;

/**
 * Parser
 */
class Parser extends AbstractParser
{
    
    /**
     * Parse one type
     *
     * @param array $options
     * @return void
     */
    protected function _parseByTypeOptions($options)
    {
        foreach ($this->_getFiles($options) as $file) {
            $adapter = $this->_adapters[$options['type']];
            $adapter->parse($file);

            foreach ($adapter->getPhrases() as $phraseData) {
                $this->_addPhrase($phraseData);
            }
        }
    }

    /**
     * Add phrase
     *
     * @param array $phraseData
     * @return void
     */
    protected function _addPhrase($phraseData)
    {
        try {
            $foundTranslation = $this->_translatePhrase($phraseData['phrase']);
            if ($foundTranslation == false) {
                $phrase = $this->_factory->createPhrase([
                    'phrase' => $phraseData['phrase'],
                    'translation' => $foundTranslation,
                    'quote' => $phraseData['quote'],
                ]);
                $this->_phrases[$phrase->getCompiledPhrase()] = $phrase;
            }
        } catch (\DomainException $e) {
            throw new \DomainException(
                "{$e->getMessage()} in {$phraseData['file']}:{$phraseData['line']}",
                $e->getCode(),
                $e
            );
        }
    }

    protected function _translatePhrase($phrase)
    {
        $translations = $this->getTranslations();
        if (array_key_exists($phrase, $translations)) {
            return $translations[$phrase];
        } else {
            return false;
        }
    }
}
