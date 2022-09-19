<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Model;

use Experius\MissingTranslations\Api\Data\TranslationInterface;
use Experius\MissingTranslations\Api\Data\TranslationSearchResultsInterface;
use Experius\MissingTranslations\Api\TranslationRepositoryInterface;
use Experius\MissingTranslations\Api\Data\TranslationSearchResultsInterfaceFactory;
use Experius\MissingTranslations\Api\Data\TranslationInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Experius\MissingTranslations\Model\ResourceModel\Translation as ResourceTranslation;
use Experius\MissingTranslations\Model\ResourceModel\Translation\CollectionFactory as TranslationCollectionFactory;

class TranslationRepository implements TranslationRepositoryInterface
{

    /**
     * @var ResourceTranslation
     */
    protected ResourceTranslation $resource;

    /**
     * @var TranslationFactory
     */
    protected TranslationFactory $translationFactory;

    /**
     * @var TranslationInterfaceFactory
     */
    protected TranslationInterfaceFactory $dataTranslationFactory;

    /**
     * @var TranslationCollectionFactory
     */
    protected TranslationCollectionFactory $translationCollectionFactory;

    /**
     * @var TranslationSearchResultsInterfaceFactory
     */
    protected TranslationSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected DataObjectHelper $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected DataObjectProcessor $dataObjectProcessor;

    /**
     * @param ResourceTranslation $resource
     * @param TranslationFactory $translationFactory
     * @param TranslationInterfaceFactory $dataTranslationFactory
     * @param TranslationCollectionFactory $translationCollectionFactory
     * @param TranslationSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        ResourceTranslation $resource,
        TranslationFactory $translationFactory,
        TranslationInterfaceFactory $dataTranslationFactory,
        TranslationCollectionFactory $translationCollectionFactory,
        TranslationSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->translationFactory = $translationFactory;
        $this->dataTranslationFactory = $dataTranslationFactory;
        $this->translationCollectionFactory = $translationCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(TranslationInterface $translation): TranslationInterface {
        try {
            /** @var $translation Translation */
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
    public function getById(int $translationId): TranslationInterface
    {
        $translation = $this->translationFactory->create();
        $this->resource->load($translation, $translationId);
        if (!$translation->getId()) {
            throw new NoSuchEntityException(__('Translation with id "%1" does not exist.', $translationId));
        }
        return $translation;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    ): TranslationSearchResultsInterface
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->translationCollectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
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
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $items = [];

        foreach ($collection as $translationModel) {
            $translationData = $this->dataTranslationFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $translationData,
                $translationModel->getData(),
                TranslationInterface::class
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $translationData,
                TranslationInterface::class
            );
        }
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(TranslationInterface $translation): bool
    {
        try {
            /** @var $translation Translation */
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
    public function deleteById($translationId): bool
    {
        return $this->delete($this->getById($translationId));
    }
}
