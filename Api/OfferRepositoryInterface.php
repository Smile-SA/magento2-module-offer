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

namespace Smile\Offer\Api;

/**
 * Offer Repository Interface
 *
 * @api
 */
interface OfferRepositoryInterface
{
    /**
     * Save offer.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @param \Smile\Offer\Api\Data\OfferInterface $offer The offer

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
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria
     *
     * @return \Smile\Offer\Api\Data\OfferSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete offer.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @param \Smile\Offer\Api\Data\OfferInterface $offer The offer
     *
     * @return bool true on success
     */
    public function delete(Data\OfferInterface $offer);

    /**
     * Delete offer by ID.
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @param int $offerId The offer Id
     *
     * @return bool true on success
     */
    public function deleteById($offerId);
}
