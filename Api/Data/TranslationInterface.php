<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Api\Data;

interface TranslationInterface
{

    const KEY_ID = 'key_id';
    const TRANSLATION_ID = 'translation_id';


    /**
     * Get translation_id
     * @return string|null
     */
    
    public function getTranslationId();

    /**
     * Set translation_id
     * @param string $translation_id
     * @return Experius\MissingTranslations\Api\Data\TranslationInterface
     */
    
    public function setTranslationId($translationId);

    /**
     * Get key_id
     * @return string|null
     */
    
    public function getKeyId();

    /**
     * Set key_id
     * @param string $key_id
     * @return Experius\MissingTranslations\Api\Data\TranslationInterface
     */
    
    public function setKeyId($key_id);
}
