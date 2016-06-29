<?php

namespace Smile\Offer\Model;

use Smile\Offer\Api\Data\OfferInterface;
use Smile\Offer\Api\OfferRepositoryInterface;
use Smile\Offer\Model\ResourceModel\Offer as OfferResource;
use Smile\Offer\Api\Data\OfferInterfaceFactory as OfferFactory;
use Smile\Offer\Model\ResourceModel\Offer\CollectionFactory as OfferCollectionFactory;
use Smile\Offer\Api\Data\OfferSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;

class OfferRepository implements OfferRepositoryInterface
{

    /**
     * @var \Smile\Offer\Model\ResourceModel\Offer
     */
    private $resource;

    /**
     * @var \Smile\Offer\Api\Data\OfferInterfaceFactory
     */
    private $offerFactory;

    /**
     * @var \Smile\Offer\Model\ResourceModel\Offer\CollectionFactory
     */
    private $offerCollectionFactory;

    /**
     * @var \Smile\Offer\Api\Data\OfferSearchResultsInterface
     */
    private $searchResultsFactory;


    public function __construct(
        OfferResource $resource,
        OfferFactory $offerFactory,
        OfferCollectionFactory $offerCollectionFactory,
        OfferSearchResultsInterfaceFactory $searchResultsFactory

    ) {
        $this->resource               = $resource;
        $this->offerFactory           = $offerFactory;
        $this->offerCollectionFactory = $offerCollectionFactory;
        $this->searchResultsFactory   = $searchResultsFactory;
    }

    public function save(OfferInterface $offer)
    {
        try {
            $this->resource->save($offer);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $offer;
    }

    public function getById($offerId)
    {
        $offer = $this->offerFactory->create();

        $this->resource->load($offer, $offerId);
        if (!$offer->getId()) {
            throw new NoSuchEntityException(__('Offer with id "%1" does not exist.', $offerId));
        }

        return $offer;
    }

    public function delete(OfferInterface $offer)
    {
        try {
            $this->resource->delete($offer);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
    }

    public function deleteById($offerId)
    {
        $this->delete($this->getById($offerId));
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->getOfferCollection($criteria);

        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    private function getOfferCollection(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $collection = $this->offerCollectionFactory->create();

        $this->addFiltersToCollection($collection, $criteria)
             ->addSortOrdersToCollection($collection, $criteria);

        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        return $collection;
    }

    private function addFiltersToCollection(
        \Smile\Offer\Model\ResourceModel\Offer\Collection $collection,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        return $this;
    }

    private function addSortOrdersToCollection(
        \Smile\Offer\Model\ResourceModel\Offer\Collection $collection,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
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