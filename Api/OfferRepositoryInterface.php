<?php

namespace Smile\Offer\Api;

interface OfferRepositoryInterface
{
    /**
     * Save offer.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @param \Smile\Offer\Api\Data\OfferInterface $offer

     * @return \Smile\Offer\Api\Data\OfferInterface
     */
    public function save(Data\OfferInterface $offer);

    /**
     * Retrieve offer by id
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @param int $offerId Offer id.
     *
     * @return \Smile\Offer\Api\Data\OfferInterface
     */
    public function getById($offerId);

    /**
     * Retrieve offers matching the specified criteria.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Cms\Api\Data\OfferSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete offer.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @param \Smile\Offer\Api\Data\OfferInterface $offer
     *
     * @return bool true on success
     *
     */
    public function delete(Data\OfferInterface $offer);

    /**
     * Delete offer by ID.
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @param int $offerId
     *
     * @return bool true on success
     */
    public function deleteById($offerId);
}
