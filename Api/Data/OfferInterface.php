<?php

declare(strict_types=1);

namespace Smile\Offer\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * Data Api for Offers
 *
 * @api
 * @method mixed getData(...$key)
 * @method mixed setData(...$data)
 * @phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName
 * @phpcs:disable Generic.Files.LineLength.TooLong
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
     *
     * @return ?int
     */
    public function getOfferId(): ?int;

    /**
     * Get product id.
     *
     * @return ?int
     */
    public function getProductId(): ?int;

    /**
     * Get seller id.
     *
     * @return ?int
     */
    public function getSellerId(): ?int;

    /**
     * Is the offer enabled.
     *
     * @return ?bool
     */
    public function isAvailable(): ?bool;

    /**
     * Offer price.
     *
     * @return ?float
     */
    public function getPrice(): ?float;

    /**
     * Offer special price.
     *
     * @return ?float
     */
    public function getSpecialPrice(): ?float;

    /**
     * Set ID.
     *
     * @return OfferInterface
     */
    public function setOfferId(int $offerId): OfferInterface;

    /**
     * Set product id.
     *
     * @return OfferInterface
     */
    public function setProductId(int $productId): OfferInterface;

    /**
     * Set seller id.
     *
     * @return OfferInterface
     */
    public function setSellerId(int $sellerId): OfferInterface;

    /**
     * Set offer availability.
     *
     * @return OfferInterface
     */
    public function setIsAvailable(bool $availability): OfferInterface;

    /**
     * Set offer price (set to null to use product catalog price).
     *
     * @return OfferInterface
     */
    public function setPrice(?float $price): OfferInterface;

    /**
     * Set offer special price (set to null to use product catalog special price).
     *
     * @return OfferInterface
     */
    public function setSpecialPrice(?float $price): OfferInterface;

    /**
     * Retrieve existing extension attributes object or create a new one. - need concrete type declaration to generate OfferExtensionInterface
     *
     * @return ?\Smile\Offer\Api\Data\OfferExtensionInterface
     */
    public function getExtensionAttributes(): ?OfferExtensionInterface;

    /**
     * Set an extension attributes object.
     *
     * @param \Smile\Offer\Api\Data\OfferExtensionInterface $extensionAttributes The additional attributes - need concrete type declaration
     * @return $this
     */
    public function setExtensionAttributes(\Smile\Offer\Api\Data\OfferExtensionInterface $extensionAttributes): self;
}
