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

use Smile\Offer\Api\OfferManagementInterface;
use Smile\Offer\Api\Data\OfferInterface;
use Smile\Offer\Api\Data\OfferInterfaceFactory as OfferFactory;
use Smile\Offer\Api\OfferRepositoryInterface;
use Smile\Offer\Model\ResourceModel\Offer\Collection as OfferCollection;
use Smile\Offer\Model\ResourceModel\Offer\CollectionFactory as OfferCollectionFactory;

/**
 * Offer Management
 *
 * @category Smile
 * @package  Smile\Offer
 * @author   Aurelien Foucret <aurelien.foucret@smile.fr>
 */
class OfferManagement implements OfferManagementInterface
{
    /**
     * @var OfferFactory
     */
    private OfferFactory $offerFactory;

    /**
     * @var OfferRepositoryInterface
     */
    private OfferRepositoryInterface $offerRepository;

    /**
     * @var OfferCollectionFactory
     */
    private OfferCollectionFactory $offerCollectionFactory;

    /**
     * OfferManagement constructor.
     *
     * @param OfferRepositoryInterface $offerRepository        Offer Repository
     * @param OfferFactory             $offerFactory           Offer Factory
     * @param OfferCollectionFactory   $offerCollectionFactory Offer Collection Factory
     */
    public function __construct(
        OfferRepositoryInterface $offerRepository,
        OfferFactory $offerFactory,
        OfferCollectionFactory $offerCollectionFactory
    ) {
        $this->offerFactory    = $offerFactory;
        $this->offerRepository = $offerRepository;
        $this->offerCollectionFactory = $offerCollectionFactory;
    }

   /**
    * {@inheritDoc}
    */
    public function createOffer(int $sellerId, int $productId, array $params): bool
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
    public function getProductOffers(int $productId): array
    {
        $offerCollection = $this->offerCollectionFactory->create();
        $offerCollection->addProductFilter($productId);

        return $offerCollection->getItems();
    }

    /**
     * {@inheritDoc}
     */
    public function getSellerOffers(int $sellerId): array
    {
        $offerCollection = $this->offerCollectionFactory->create();
        $offerCollection->addSellerFilter($sellerId);

        return $offerCollection->getItems();
    }

    /**
     * {@inheritDoc}
     */
    public function getOffer(int $productId, int $sellerId): OfferInterface
    {
        /**
         * @var OfferCollection $offerCollection
         */
        $offerCollection = $this->offerCollectionFactory->create();

        $offerCollection->addProductFilter($productId)
            ->addSellerFilter($sellerId);

        return $offerCollection->getFirstItem();
    }
}
