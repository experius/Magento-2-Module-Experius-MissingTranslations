<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);


namespace Experius\MissingTranslations\Module\I18n;

/**
 *  Abstract Factory
 */
class Factory
{
    /**
     * Create dictionary writer
     *
     * @param string $filename
     * @return \Experius\MissingTranslations\Module\I18n\Dictionary\WriterInterface
     */
    public function createDictionaryWriter($filename = null)
    {
        if (!$filename) {
            $writer = new Dictionary\Writer\Csv\Stdo();
        } else {
            switch (pathinfo($filename, \PATHINFO_EXTENSION)) {
                case 'csv':
                default:
                    $writer = new Dictionary\Writer\Csv($filename);
                    break;
            }
        }
        return $writer;
    }

    /**
     * Create locale
     *
     * @param string $locale
     * @return \Experius\MissingTranslations\Module\I18n\Locale
     */
    public function createLocale($locale)
    {
        return new Locale($locale);
    }

    /**
     * Create dictionary
     *
     * @return \Experius\MissingTranslations\Module\I18n\Dictionary
     */
    public function createDictionary()
    {
        return new Dictionary();
    }

    /**
     * Create Phrase
     *
     * @param array $data
     * @return \Experius\MissingTranslations\Module\I18n\Dictionary\Phrase
     */
    public function createPhrase(array $data)
    {
        if ("" != $data['phrase']) {
            return new Dictionary\Phrase(
                $data['phrase'],
                $data['translation'],
                isset($data['context_type']) ? $data['context_type'] : null,
                isset($data['context_value']) ? $data['context_value'] : null,
                isset($data['quote']) ? $data['quote'] : null
            );
        }
    }
}
