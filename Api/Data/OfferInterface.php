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

namespace Smile\Offer\Api\Data;

/**
 * Data Api for Offers
 *
 * @api
 */
interface OfferInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**
     * The offer Id field
     */
    const OFFER_ID                  = 'offer_id';

    /**
     * The Product Id field
     */
    const PRODUCT_ID                = 'product_id';

    /**
     * The Seller Id field
     */
    const SELLER_ID                 = 'seller_id';

    /**
     * The availability status field
     */
    const IS_AVAILABLE              = 'is_available';

    /**
     * Price field
     */
    const PRICE                     = 'price';

    /**
     * Special Price field
     */
    const SPECIAL_PRICE             = 'special_price';

    /**
     * Get ID.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get product id.
     *
     * @return int|null
     */
    public function getProductId();

    /**
     * Get seller id.
     *
     * @return int|null
     */
    public function getSellerId();

    /**
     * Is the offer enabled.
     *
     * @return bool|null
     */
    public function isAvailable();

    /**
     * Offer price.
     *
     * @return float|null
     */
    public function getPrice();

    /**
     * Offer special price.
     *
     * @return float|null
     */
    public function getSpecialPrice();

    /**
     * Set ID.
     *
     * @param int $offerId Offer id.
     *
     * @return \Smile\Offer\Api\Data\OfferInterface
     */
    public function setId($offerId);

    /**
     * Set product id.
     *
     * @param int $productId Product id.
     *
     * @return \Smile\Offer\Api\Data\OfferInterface
     */
    public function setProductId($productId);

    /**
     * Set seller id.
     *
     * @param int $sellerId Seller id.
     *
     * @return \Smile\Offer\Api\Data\OfferInterface
     */
    public function setSellerId($sellerId);

    /**
     * Set offer availability.
     *
     * @param bool $availability Availability.
     *
     * @return \Smile\Offer\Api\Data\OfferInterface
     */
    public function setIsAvailable($availability);

    /**
     * Set offer price.
     *
     * @param float|null $price Offer price (set to null to use product catalog price).
     *
     * @return \Smile\Offer\Api\Data\OfferInterface
     */
    public function setPrice($price);

    /**
     * Set offer special price.
     *
     * @param float|null $price Offer special price (set to null to use product catalog special price).
     *
     * @return \Smile\Offer\Api\Data\OfferInterface
     */
    public function setSpecialPrice($price);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Smile\Offer\Api\Data\OfferExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Smile\Offer\Api\Data\OfferExtensionInterface $extensionAttributes The additional attributes
     *
     * @return $this
     */
    public function setExtensionAttributes(\Smile\Offer\Api\Data\OfferExtensionInterface $extensionAttributes);
}
