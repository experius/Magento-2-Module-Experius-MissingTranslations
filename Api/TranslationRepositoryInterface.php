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

namespace Experius\MissingTranslations\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface TranslationRepositoryInterface
{


    /**
     * Save Translation
     * @param \Experius\MissingTranslations\Api\Data\TranslationInterface $translation
     * @return \Experius\MissingTranslations\Api\Data\TranslationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function save(
        \Experius\MissingTranslations\Api\Data\TranslationInterface $translation
    );

    /**
     * Retrieve Translation
     * @param string $translationId
     * @return \Experius\MissingTranslations\Api\Data\TranslationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getById($translationId);

    /**
     * Retrieve Translation matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Experius\MissingTranslations\Api\Data\TranslationSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Translation
     * @param \Experius\MissingTranslations\Api\Data\TranslationInterface $translation
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function delete(
        \Experius\MissingTranslations\Api\Data\TranslationInterface $translation
    );

    /**
     * Delete Translation by ID
     * @param string $translationId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function deleteById($translationId);
}
