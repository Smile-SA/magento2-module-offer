<?php

declare(strict_types=1);

namespace Smile\Offer\Model;

use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Smile\Offer\Api\Data\OfferInterface;
use Smile\Offer\Api\Data\OfferSearchResultsInterface;
use Smile\Offer\Api\Data\OfferSearchResultsInterfaceFactory;
use Smile\Offer\Api\OfferRepositoryInterface;
use Smile\Offer\Model\ResourceModel\Offer as OfferResource;
use Smile\Offer\Model\ResourceModel\Offer\Collection;
use Smile\Offer\Model\ResourceModel\Offer\CollectionFactory as OfferCollectionFactory;

/**
 * Offer Repository implementation.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OfferRepository implements OfferRepositoryInterface
{
    public function __construct(
        private OfferResource $resource,
        private OfferCollectionFactory $offerCollectionFactory,
        private OfferSearchResultsInterfaceFactory $searchResultsFactory
    ) {
    }

    /**
     * @inheritdoc
     */
    public function save(OfferInterface $offer): OfferInterface
    {
        try {
            /** @var Offer $offer */
            $this->resource->save($offer);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $offer;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $offerId): OfferInterface|DataObject
    {
        /** @var Offer $offer */
        $offer = $this->offerCollectionFactory->create()->getNewEmptyItem();

        $this->resource->load($offer, $offerId);
        if (!$offer->getOfferId()) {
            throw new NoSuchEntityException(__('Offer with id "%1" does not exist.', $offerId));
        }

        return $offer;
    }

    /**
     * @inheritdoc
     */
    public function delete(OfferInterface $offer): void
    {
        try {
            /** @var Offer $offer */
            $this->resource->delete($offer);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
    }

    /**
     * @inheritdoc
     */
    public function deleteById($offerId): void
    {
        $this->delete($this->getById($offerId));
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $criteria): OfferSearchResultsInterface
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->getOfferCollection($criteria);

        $searchResults->setTotalCount($collection->getSize());
        /** @var OfferInterface[] $items */
        $items = $collection->getItems();
        $searchResults->setItems($items);

        return $searchResults;
    }

    /**
     * Retrieve Offer Collection.
     */
    private function getOfferCollection(SearchCriteriaInterface $criteria): Collection
    {
        $collection = $this->offerCollectionFactory->create();

        $this->addFiltersToCollection($collection, $criteria)
             ->addSortOrdersToCollection($collection, $criteria);

        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        return $collection;
    }

    /**
     * Apply filters to offer collection.
     */
    private function addFiltersToCollection(Collection $collection, SearchCriteriaInterface $criteria): self
    {
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        return $this;
    }

    /**
     * Apply sort order to offer collection.
     */
    private function addSortOrdersToCollection(Collection $collection, SearchCriteriaInterface $criteria): self
    {
        $sortOrders = $criteria->getSortOrders();

        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $sortField = $sortOrder->getField();
                $sortOrder = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'ASC' : 'DESC';
                $collection->addOrder($sortField, $sortOrder);
            }
        }

        return $this;
    }
}
