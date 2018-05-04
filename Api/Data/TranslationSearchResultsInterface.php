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

interface TranslationSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Translation list.
     * @return \Experius\MissingTranslations\Api\Data\TranslationInterface[]
     */
    
    public function getItems();

    /**
     * Set key_id list.
     * @param \Experius\MissingTranslations\Api\Data\TranslationInterface[] $items
     * @return $this
     */
    
    public function setItems(array $items);
}
