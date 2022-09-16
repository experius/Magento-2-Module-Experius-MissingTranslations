<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

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
