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
use Smile\Offer\Api\Data\OfferInterfaceFactory as OfferFactory;
use Smile\Offer\Api\OfferRepositoryInterface;
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
     * @var \Smile\Offer\Api\Data\OfferInterfaceFactory
     */
    private $offerFactory;

    /**
     * @var \Smile\Offer\Api\OfferRepositoryInterface
     */
    private $offerRepository;

    /**
     * @var \Smile\Offer\Model\ResourceModel\Offer\CollectionFactory
     */
    private $offerCollectionFactory;

    /**
     * OfferManagement constructor.
     *
     * @param \Smile\Offer\Api\OfferRepositoryInterface   $offerRepository Offer Repository
     * @param \Smile\Offer\Api\Data\OfferInterfaceFactory $offerFactory    Offer Factory
     */
    public function __construct(OfferRepositoryInterface $offerRepository, OfferFactory $offerFactory, OfferCollectionFactory $offerCollectionFactory)
    {
        $this->offerFactory    = $offerFactory;
        $this->offerRepository = $offerRepository;
        $this->offerCollectionFactory = $offerCollectionFactory;
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
    public function getProductOffers($productId)
    {
        // TODO: Auto-generated method stub
    }

    /**
     * {@inheritDoc}
     */
    public function getSellerOffers($sellerId)
    {
        // TODO: Auto-generated method stub
    }

    /**
     * {@inheritDoc}
     */
    public function getOffer($productId, $sellerId)
    {
        /**
         * @var \Smile\Offer\Model\ResourceModel\Offer\Collection $offerCollection
         */
        $offerCollection = $this->offerCollectionFactory->create();

        $offerCollection->addProductFilter($productId)
            ->addSellerFilter($sellerId);

        return $offerCollection->getFirstItem();
    }
}
