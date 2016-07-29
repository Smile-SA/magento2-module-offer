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

use Smile\Offer\Api\Data\OfferInterface;

/**
 * Offer Management Interface
 *
 * @api
 */
interface OfferManagementInterface
{
    /**
     * @param int   $sellerId  The seller Id
     * @param int   $productId The product Id
     * @param array $params    Offer parameters
     *
     * @return boolean
     */
    public function createOffer($sellerId, $productId, $params);

    /**
     * Get Offers for a given product Id, and a given date.
     *
     * @param int    $productId The product Id
     * @param string $date      A date
     *
     * @return OfferInterface[]
     */
    public function getProductOffers($productId, $date);

    /**
     * Get Offers for a given seller Id, and a given date.
     *
     * @param int    $sellerId The seller Id
     * @param string $date     A date
     *
     * @return OfferInterface[]
     */
    public function getSellerOffers($sellerId, $date);

    /**
     * Get Offer by product, seller and date.
     *
     * @param int    $productId The product Id
     * @param int    $sellerId  The seller Id
     * @param string $date      The date
     *
     * @return OfferInterface
     */
    public function getOffer($productId, $sellerId, $date);
}
