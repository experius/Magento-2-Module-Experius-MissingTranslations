<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);


namespace Experius\MissingTranslations\Module\I18n\Dictionary;

use Experius\MissingTranslations\Module\I18n\Factory;
use Experius\MissingTranslations\Module\I18n\ParserInterface;

/**
 * Dictionary generator
 */
class Generator
{
    /**
     * Parser
     *
     * @var \Experius\MissingTranslations\Module\I18n\ParserInterface
     */
    protected $parser;

    /**
     * Contextual parser
     *
     * @var \Experius\MissingTranslations\Module\I18n\ParserInterface
     */
    protected $contextualParser;

    /**
     * Domain abstract factory
     *
     * @var \Experius\MissingTranslations\Module\I18n\Factory
     */
    protected $factory;

    /**
     * Generator options resolver
     *
     * @var Options\ResolverFactory
     */
    protected $optionResolverFactory;

    /**
     * @var WriterInterface
     */
    protected $writer;

    /**
     * Generator construct
     *
     * @param ParserInterface $parser
     * @param ParserInterface $contextualParser
     * @param Factory $factory
     * @param Options\ResolverFactory $optionsResolver
     */
    public function __construct(
        ParserInterface $parser,
        ParserInterface $contextualParser,
        Factory $factory,
        Options\ResolverFactory $optionsResolver
    )
    {
        $this->parser = $parser;
        $this->contextualParser = $contextualParser;
        $this->factory = $factory;
        $this->optionResolverFactory = $optionsResolver;
    }

    /**
     * Generate dictionary
     *
     * @param string $directory
     * @param string $outputFilename
     * @param bool $withContext
     * @param string $locale
     * @param string $delimiter
     * @param string $enclosure
     * @return void
     */
    public function generate($directory, $outputFilename, $withContext = false, $locale = 'en_US', $delimiter = ',', $enclosure = '"')
    {
        $optionResolver = $this->optionResolverFactory->create($directory, $withContext);

        $parser = $this->getActualParser($withContext);
        $parser->loadTranslations($locale);
        $parser->parse($optionResolver->getOptions());

        $phraseList = $parser->getPhrases();
        if (!count($phraseList)) {
            throw new \UnexpectedValueException('No phrases found in the specified dictionary file.');
        }
        foreach ($phraseList as $phrase) {
            if ($phrase instanceof Phrase) {
                $this->getDictionaryWriter($outputFilename, $delimiter, $enclosure)->write($phrase);
            }
        }
        $this->writer = null;
    }

    /**
     * @param string $outputFilename
     * @return WriterInterface
     */
    protected function getDictionaryWriter($outputFilename)
    {
        if (null === $this->writer) {
            $this->writer = $this->factory->createDictionaryWriter($outputFilename);
        }
        return $this->writer;
    }

    /**
     * Get actual parser
     *
     * @param bool $withContext
     * @return \Experius\MissingTranslations\Module\I18n\ParserInterface
     */
    protected function getActualParser($withContext)
    {
        return $withContext ? $this->contextualParser : $this->parser;
    }
}
