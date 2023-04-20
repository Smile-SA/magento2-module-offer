<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\Offer
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\Offer\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Smile\Offer\Api\Data\OfferInterface;
use Smile\Offer\Api\Data\OfferInterfaceFactory as OfferFactory;
use Smile\Offer\Api\Data\OfferSearchResultsInterface;
use Smile\Offer\Api\Data\OfferSearchResultsInterfaceFactory;
use Smile\Offer\Api\OfferRepositoryInterface;
use Smile\Offer\Model\ResourceModel\Offer as OfferResource;
use Smile\Offer\Model\ResourceModel\Offer\Collection;
use Smile\Offer\Model\ResourceModel\Offer\CollectionFactory as OfferCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;

/**
 * Offer Repository
 *
 * @category Smile
 * @package  Smile\Offer
 * @author   Aurelien Foucret <aurelien.foucret@smile.fr>
 */
class OfferRepository implements OfferRepositoryInterface
{
    /**
     * @var OfferResource
     */
    private OfferResource $resource;

    /**
     * @var OfferCollectionFactory
     */
    private OfferCollectionFactory $offerCollectionFactory;

    /**
     * @var OfferSearchResultsInterfaceFactory
     */
    private OfferSearchResultsInterfaceFactory $searchResultsFactory;


    /**
     * OfferRepository constructor.
     *
     * @param OfferResource                      $resource               Offer Resource Model
     * @param OfferCollectionFactory             $offerCollectionFactory Offer Collection Factory
     * @param OfferSearchResultsInterfaceFactory $searchResultsFactory   Search Results Factory
     */
    public function __construct(
        OfferResource $resource,
        OfferCollectionFactory $offerCollectionFactory,
        OfferSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource               = $resource;
        $this->offerCollectionFactory = $offerCollectionFactory;
        $this->searchResultsFactory   = $searchResultsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(OfferInterface $offer): OfferInterface
    {
        try {
            $this->resource->save($offer);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $offer;
    }

    /**
     * {@inheritdoc}
     */
    public function getById(int $offerId): OfferInterface
    {
        $offer = $this->offerCollectionFactory->create()->getNewEmptyItem();

        $this->resource->load($offer, $offerId);
        if (!$offer->getId()) {
            throw new NoSuchEntityException(__('Offer with id "%1" does not exist.', $offerId));
        }

        return $offer;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(OfferInterface $offer): void
    {
        try {
            $this->resource->delete($offer);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($offerId): void
    {
        $this->delete($this->getById($offerId));
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $criteria): OfferSearchResultsInterface
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->getOfferCollection($criteria);

        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * Retrieve Offer Collection
     *
     * @param SearchCriteriaInterface $criteria The search criteria
     *
     * @return Collection
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
     *
     * @param Collection              $collection The collection
     * @param SearchCriteriaInterface $criteria   Search criteria to apply
     *
     * @return $this
     */
    private function addFiltersToCollection(
        Collection $collection,
        SearchCriteriaInterface $criteria
    ): self {
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
     *
     * @param Collection              $collection The collection
     * @param SearchCriteriaInterface $criteria   Search criteria to apply
     *
     * @return $this
     */
    private function addSortOrdersToCollection(
        Collection $collection,
        SearchCriteriaInterface $criteria
    ): self {
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
