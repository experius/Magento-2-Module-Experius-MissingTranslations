<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);


namespace Experius\MissingTranslations\Module\I18n\Dictionary\Writer;

use Experius\MissingTranslations\Module\I18n\Dictionary\Phrase;
use Experius\MissingTranslations\Module\I18n\Dictionary\WriterInterface;

/**
 * Csv writer
 */
class Csv implements WriterInterface
{
    /**
     * File handler
     *
     * @var resource
     */
    protected $_fileHandler;

    /**
     * Writer construct
     *
     * @param string $outputFilename
     */
    public function __construct($outputFilename)
    {
        if (false === ($fileHandler = fopen($outputFilename, 'w')) || !file_exists($outputFilename)) {
            throw new \InvalidArgumentException(
                sprintf('Cannot open file for write dictionary: "%s"', $outputFilename)
            );
        }
        $this->_fileHandler = $fileHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function write(Phrase $phrase)
    {
        $fields = [$phrase->getCompiledPhrase(), $phrase->getCompiledTranslation()];
        if (($contextType = $phrase->getContextType()) && ($contextValue = $phrase->getContextValueAsString())) {
            $fields[] = $contextType;
            $fields[] = $contextValue;
        }

        fputcsv($this->_fileHandler, $fields);
    }

    /**
     * Close file handler
     *
     * @return void
     */
    public function __destructor()
    {
        fclose($this->_fileHandler);
    }
}
