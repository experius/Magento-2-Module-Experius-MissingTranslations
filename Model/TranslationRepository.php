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

namespace Experius\MissingTranslations\Model;

use Experius\MissingTranslations\Api\TranslationRepositoryInterface;
use Experius\MissingTranslations\Api\Data\TranslationSearchResultsInterfaceFactory;
use Experius\MissingTranslations\Api\Data\TranslationInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Experius\MissingTranslations\Model\ResourceModel\Translation as ResourceTranslation;
use Experius\MissingTranslations\Model\ResourceModel\Translation\CollectionFactory as TranslationCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class TranslationRepository implements TranslationRepositoryInterface
{

    protected $resource;

    protected $TranslationFactory;

    protected $TranslationCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataTranslationFactory;

    private $storeManager;


    /**
     * @param ResourceTranslation $resource
     * @param TranslationFactory $translationFactory
     * @param TranslationInterfaceFactory $dataTranslationFactory
     * @param TranslationCollectionFactory $translationCollectionFactory
     * @param TranslationSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceTranslation $resource,
        TranslationFactory $translationFactory,
        TranslationInterfaceFactory $dataTranslationFactory,
        TranslationCollectionFactory $translationCollectionFactory,
        TranslationSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->translationFactory = $translationFactory;
        $this->translationCollectionFactory = $translationCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataTranslationFactory = $dataTranslationFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Experius\MissingTranslations\Api\Data\TranslationInterface $translation
    ) {
        /* if (empty($translation->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $translation->setStoreId($storeId);
        } */
        try {
            $this->resource->save($translation);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the translation: %1',
                $exception->getMessage()
            ));
        }
        return $translation;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($translationId)
    {
        $translation = $this->translationFactory->create();
        $translation->load($translationId);
        if (!$translation->getId()) {
            throw new NoSuchEntityException(__('Translation with id "%1" does not exist.', $translationId));
        }
        return $translation;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $collection = $this->translationCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $items = [];
        
        foreach ($collection as $translationModel) {
            $translationData = $this->dataTranslationFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $translationData,
                $translationModel->getData(),
                'Experius\MissingTranslations\Api\Data\TranslationInterface'
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $translationData,
                'Experius\MissingTranslations\Api\Data\TranslationInterface'
            );
        }
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Experius\MissingTranslations\Api\Data\TranslationInterface $translation
    ) {
        try {
            $this->resource->delete($translation);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Translation: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($translationId)
    {
        return $this->delete($this->getById($translationId));
    }
}
