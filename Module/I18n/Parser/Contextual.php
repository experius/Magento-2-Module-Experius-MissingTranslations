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

use Experius\MissingTranslations\Module\I18n;

/**
 * Contextual Parser
 */
class Contextual extends AbstractParser
{
    /**
     * Context
     *
     * @var \Experius\MissingTranslations\Module\I18n\Context
     */
    protected $_context;

    /**
     * Parser construct
     *
     * @param I18n\FilesCollector $filesCollector
     * @param I18n\Factory $factory
     * @param I18n\Context $context
     */
    public function __construct(I18n\FilesCollector $filesCollector, I18n\Factory $factory, I18n\Context $context)
    {
        $this->_context = $context;

        parent::__construct($filesCollector, $factory);
    }

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

            list($contextType, $contextValue) = $this->_context->getContextByPath($file);

            foreach ($adapter->getPhrases() as $phraseData) {
                $this->_addPhrase($phraseData, $contextType, $contextValue);
            }
        }
    }

    /**
     * Add phrase with context
     *
     * @param array $phraseData
     * @param string $contextType
     * @param string $contextValue
     * @return void
     */
    protected function _addPhrase($phraseData, $contextType, $contextValue)
    {
        $phraseKey = $contextType . $contextValue. stripslashes($phraseData['phrase']);

        if (isset($this->_phrases[$phraseKey])) {
            /** @var \Experius\MissingTranslations\Module\I18n\Dictionary\Phrase $phrase */
            $phrase = $this->_phrases[$phraseKey];
            $phrase->addContextValue($contextValue);
        } else {
            $foundTranslation = $this->_translatePhrase($phraseData['phrase']);
            if ($foundTranslation == false) {
                $this->_phrases[$phraseKey] = $this->_factory->createPhrase(
                    [
                        'phrase' => $phraseData['phrase'],
                        'translation' => $foundTranslation,
                        'context_type' => $contextType,
                        'context_value' => [$contextValue],
                        'quote' => $phraseData['quote'],
                    ]
                );
            }
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
