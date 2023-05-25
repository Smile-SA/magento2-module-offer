<?php

namespace Smile\Offer\Api;

use Smile\Offer\Api\Data\OfferInterface;

/**
 * @api
 */
interface OfferManagementInterface
{
    /**
     * Create the offer.
     *
     * @param int   $sellerId  The seller Id
     * @param int   $productId The product Id
     * @param array $params    Offer parameters
     * @return OfferInterface
     */
    public function createOffer(int $sellerId, int $productId, array $params): OfferInterface;

    /**
     * Get Offers for a given product Id, and a given date.
     *
     * @param int $productId The product Id
     * @return OfferInterface[]
     */
    public function getProductOffers(int $productId): array;

    /**
     * Get Offers for a given seller Id, and a given date.
     *
     * @param int $sellerId The seller Id
     * @return OfferInterface[]
     */
    public function getSellerOffers(int $sellerId): array;

    /**
     * Get Offer by product, seller and date.
     *
     * @param int $productId The product Id
     * @param int $sellerId  The seller Id
     * @return OfferInterface
     */
    public function getOffer(int $productId, int $sellerId): OfferInterface;
}
