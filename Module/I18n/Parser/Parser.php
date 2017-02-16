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
            if ($foundTranslation) {
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
		if(array_key_exists($phrase, $translations)){
	        return $translations[$phrase];
		}else{
			return false;
		}
	}
}
