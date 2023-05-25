<?php

namespace Smile\Offer\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;
use Smile\Offer\Api\Data\OfferExtensionInterface;

/**
 * Data Api for Offers
 *
 * @api
 */
interface OfferInterface extends CustomAttributesDataInterface
{
    public const OFFER_ID = 'offer_id';
    public const PRODUCT_ID = 'product_id';
    public const SELLER_ID = 'seller_id';
    public const IS_AVAILABLE = 'is_available';
    public const PRICE = 'price';
    public const SPECIAL_PRICE = 'special_price';

    /**
     * Get ID.
     */
    public function getId(): ?int;

    /**
     * Get product id.
     */
    public function getProductId(): ?int;

    /**
     * Get seller id.
     */
    public function getSellerId(): ?int;

    /**
     * Is the offer enabled.
     */
    public function isAvailable(): ?bool;

    /**
     * Offer price.
     */
    public function getPrice(): ?float;

    /**
     * Offer special price.
     */
    public function getSpecialPrice(): ?float;

    /**
     * Set ID.
     */
    public function setId($offerId): OfferInterface;

    /**
     * Set product id.
     */
    public function setProductId(int $productId): OfferInterface;

    /**
     * Set seller id.
     */
    public function setSellerId(int $sellerId): OfferInterface;

    /**
     * Set offer availability.
     */
    public function setIsAvailable(bool $availability): OfferInterface;

    /**
     * Set offer price (set to null to use product catalog price).
     */
    public function setPrice(?float $price): OfferInterface;

    /**
     * Set offer special price (set to null to use product catalog special price).
     */
    public function setSpecialPrice(?float $price): OfferInterface;

    /**
     * Retrieve existing extension attributes object or create a new one.
     */
    public function getExtensionAttributes(): ?OfferExtensionInterface;

    /**
     * Set an extension attributes object.
     */
    public function setExtensionAttributes(OfferExtensionInterface $extensionAttributes): self;
}
