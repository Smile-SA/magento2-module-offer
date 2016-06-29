<?php

namespace Smile\Offer\Api;

interface OfferManagementInterface
{
    public function createOffer($sellerId, $productId, $params);

    public function getProductOffers($productId, $date);

    public function getSellerOffers($sellerId, $date);

    public function getOffer($productId, $sellerId, $date);
}
