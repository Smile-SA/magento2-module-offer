<?php

namespace Smile\Offer\Model;

use Smile\Offer\Api\OfferManagementInterface;
use Smile\Offer\Api\Data\OfferInterfaceFactory as OfferFactory;
use Smile\Offer\Api\OfferRepositoryInterface as OfferRepository;

class OfferManagement implements OfferManagementInterface
{
    /**
     * @var \Smile\Offer\Api\Data\OfferInterfaceFactory
     */
    private $offerFactory;

    /**
     * @var \Smile\Offer\Api\OfferRepositoryInterface
     */
    private $offerRepository;

    public function __construct(OfferRepository $offerRepository, OfferFactory $offerFactory)
    {
        $this->offerFactory    = $offerFactory;
        $this->offerRepository = $offerRepository;
    }


   /**
    * {@inheritDoc}
    */
    public function createOffer($sellerId, $productId, $params)
    {
        $offer = $this->offerFactory->create();
        $offer->setSellerId($sellerId);
        $offer->setProductId($productId);
        $offer->addData($params);

        return $this->offerRepository->save($offer);
    }

    /**
     * {@inheritDoc}
     */
    public function getProductOffers($productId, $date) {
    // TODO: Auto-generated method stub

    }

    /**
     * {@inheritDoc}
     */
    public function getSellerOffers($sellerId, $date) {
    // TODO: Auto-generated method stub

    }

    /**
     * {@inheritDoc}
     */
    public function getOffer($productId, $sellerId, $date) {
    // TODO: Auto-generated method stub

    }

}