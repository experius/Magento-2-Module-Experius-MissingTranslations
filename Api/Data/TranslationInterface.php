<?php
/**
 * Collect missing translations in specified folder or the entire Magento 2 Root
 * Copyright (C) 2016 Lewis Voncken
 * 
 * This file included in Experius/MissingTranslations is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

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
