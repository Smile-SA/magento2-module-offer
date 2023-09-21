<?php

declare(strict_types=1);

namespace Smile\Offer\Model;

use Magento\Framework\DataObject;
use Smile\Offer\Api\Data\OfferInterface;
use Smile\Offer\Api\Data\OfferInterfaceFactory as OfferFactory;
use Smile\Offer\Api\OfferManagementInterface;
use Smile\Offer\Api\OfferRepositoryInterface;
use Smile\Offer\Model\ResourceModel\Offer\Collection as OfferCollection;
use Smile\Offer\Model\ResourceModel\Offer\CollectionFactory as OfferCollectionFactory;

/**
 * Offer Management implementation.
 */
class OfferManagement implements OfferManagementInterface
{
    public function __construct(
        private OfferRepositoryInterface $offerRepository,
        private OfferFactory $offerFactory,
        private OfferCollectionFactory $offerCollectionFactory
    ) {
    }

   /**
    * @inheritdoc
    */
    public function createOffer(int $sellerId, int $productId, array $params): OfferInterface
    {
        /** @var Offer $offer */
        $offer = $this->offerFactory->create();
        $offer->setSellerId($sellerId);
        $offer->setProductId($productId);
        $offer->addData($params);

        return $this->offerRepository->save($offer);
    }

    /**
     * @inheritdoc
     */
    public function getProductOffers(int $productId): array
    {
        $offerCollection = $this->offerCollectionFactory->create();
        $offerCollection->addProductFilter($productId);

        return $offerCollection->getItems();
    }

    /**
     * @inheritdoc
     */
    public function getSellerOffers(int $sellerId): array
    {
        $offerCollection = $this->offerCollectionFactory->create();
        $offerCollection->addSellerFilter($sellerId);

        return $offerCollection->getItems();
    }

    /**
     * @inheritdoc
     */
    public function getOffer(int $productId, int $sellerId): OfferInterface|DataObject
    {
        /** @var OfferCollection $offerCollection */
        $offerCollection = $this->offerCollectionFactory->create();

        $offerCollection->addProductFilter($productId)
            ->addSellerFilter($sellerId);

        return $offerCollection->getFirstItem();
    }
}
