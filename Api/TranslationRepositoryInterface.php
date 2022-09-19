<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Api;

use Experius\MissingTranslations\Api\Data\TranslationInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

interface TranslationRepositoryInterface
{


    /**
     * Save Translation
     * @param \Experius\MissingTranslations\Api\Data\TranslationInterface $translation
     * @return \Experius\MissingTranslations\Api\Data\TranslationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Experius\MissingTranslations\Api\Data\TranslationInterface $translation);

    /**
     * Retrieve Translation
     * @param int $translationId
     * @return \Experius\MissingTranslations\Api\Data\TranslationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $translationId): TranslationInterface;

    /**
     * Retrieve Translation matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Experius\MissingTranslations\Api\Data\TranslationSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Translation
     * @param \Experius\MissingTranslations\Api\Data\TranslationInterface $translation
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Experius\MissingTranslations\Api\Data\TranslationInterface $translation): bool;

    /**
     * Delete Translation by ID
     * @param int $translationId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $translationId): bool;
}
