<?php

declare(strict_types=1);

namespace Smile\Offer\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DataObject;

/**
 * @api
 */
interface OfferRepositoryInterface
{
    /**
     * Save offer.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @param \Smile\Offer\Api\Data\OfferInterface $offer The offer
     * @return \Smile\Offer\Api\Data\OfferInterface
     */
    public function save(Data\OfferInterface $offer): Data\OfferInterface;

    /**
     * Retrieve offer by id
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @param int $offerId Offer id.
     * @return \Smile\Offer\Api\Data\OfferInterface|DataObject
     */
    public function getById(int $offerId): Data\OfferInterface|DataObject;

    /**
     * Retrieve offers matching the specified criteria.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria
     * @return \Smile\Offer\Api\Data\OfferSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): Data\OfferSearchResultsInterface;

    /**
     * Delete offer.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @param \Smile\Offer\Api\Data\OfferInterface $offer The offer
     * @return void - bool true on success
     */
    public function delete(Data\OfferInterface $offer): void;

    /**
     * Delete offer by ID.
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @param int $offerId The offer Id
     * @return void - bool true on success
     */
    public function deleteById(int $offerId): void;
}
