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
use Magento\Framework\App\ObjectManager;

/**
 * Abstract parser
 */
abstract class AbstractParser implements I18n\ParserInterface
{
    /**
     * All Frontend Translations
     *
     * @var array
     */
    protected $_translations = [];

    /**
     * Files collector
     *
     * @var \Experius\MissingTranslations\Module\I18n\FilesCollector
     */
    protected $_filesCollector = [];

    /**
     * Domain abstract factory
     *
     * @var \Experius\MissingTranslations\Module\I18n\Factory
     */
    protected $_factory;

    /**
     * Adapters
     *
     * @var \Experius\MissingTranslations\Module\I18n\Parser\AdapterInterface[]
     */
    protected $_adapters = [];

    /**
     * Parsed phrases
     *
     * @var array
     */
    protected $_phrases = [];

    /**
     * Parser construct
     *
     * @param I18n\FilesCollector $filesCollector
     * @param I18n\Factory $factory
     */
    public function __construct(I18n\FilesCollector $filesCollector, I18n\Factory $factory)
    {
        $this->_filesCollector = $filesCollector;
        $this->_factory = $factory;
    }

    /**
     * Add parser
     *
     * @param string $type
     * @param AdapterInterface $adapter
     * @return void
     */
    public function addAdapter($type, AdapterInterface $adapter)
    {
        $this->_adapters[$type] = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(array $parseOptions)
    {
        $this->_validateOptions($parseOptions);

        foreach ($parseOptions as $typeOptions) {
            $this->_parseByTypeOptions($typeOptions);
        }
        return $this->_phrases;
    }

    /**
     * Parse one type
     *
     * @param array $options
     * @return void
     */
    abstract protected function _parseByTypeOptions($options);

    /**
     * Validate options
     *
     * @param array $parseOptions
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function _validateOptions($parseOptions)
    {
        foreach ($parseOptions as $parserOptions) {
            if (empty($parserOptions['type'])) {
                throw new \InvalidArgumentException('Missed "type" in parser options.');
            }
            if (!isset($this->_adapters[$parserOptions['type']])) {
                throw new \InvalidArgumentException(
                    sprintf('Adapter is not set for type "%s".', $parserOptions['type'])
                );
            }
            if (!isset($parserOptions['paths']) || !is_array($parserOptions['paths'])) {
                throw new \InvalidArgumentException('"paths" in parser options must be array.');
            }
        }
    }

    /**
     * Get files for parsing
     *
     * @param array $options
     * @return array
     */
    protected function _getFiles($options)
    {
        $fileMask = isset($options['fileMask']) ? $options['fileMask'] : '';

        return $this->_filesCollector->getFiles($options['paths'], $fileMask);
    }

    /**
     * {@inheritdoc}
     */
    public function getPhrases()
    {
        return $this->_phrases;
    }

    /**
     * Load all Frontend Translations
     */
    public function loadTranslations($locale = 'en_US')
    {
        $objectManager = ObjectManager::getInstance();
        $resolver = $objectManager->get('Magento\Framework\Locale\Resolver');

        $translator = $objectManager->get('Magento\Framework\Phrase\Renderer\Translate');

        $translatorInterface = $objectManager->get('Magento\Framework\TranslateInterface');
        $translatorInterface->setLocale($locale);
        $translatorInterface->loadData('frontend',true);
        $this->_translations = $translatorInterface->getData();
    }

    /**
     * Return all Frontend Translations
     *
     * @return array
     */
    public function getTranslations()
    {
        return $this->_translations;
    }
}
