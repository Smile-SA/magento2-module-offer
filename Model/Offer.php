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

use Smile\Offer\Api\Data\OfferInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Offer Model
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName) properties are inherited.
 *
 * @category Smile
 * @package  Smile\Offer
 * @author   Aurelien Foucret <aurelien.foucret@smile.fr>
 */
class Offer extends AbstractModel implements OfferInterface, IdentityInterface
{
    /**
     * @var string
     */
    const CACHE_TAG = 'smile_offer';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var string
     */
    protected $_eventPrefix = 'smile_offer';

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->getData(self::OFFER_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     *
     * {@inheritDoc}
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function isAvailable()
    {
        return (bool) $this->getData(self::IS_AVAILABLE);
    }

    /**
     * {@inheritDoc}
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecialPrice()
    {
        return $this->getData(self::SPECIAL_PRICE);
    }

    /**
     * {@inheritDoc}
     */
    public function getStartDate()
    {
        return $this->getData(self::START_DATE);
    }

    /**
     * {@inheritDoc}
     */
    public function getEndDate()
    {
        return $this->getData(self::END_DATE);
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        return $this->setData(self::OFFER_ID, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * {@inheritDoc}
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * {@inheritDoc}
     */
    public function setIsAvailable($availability)
    {
        return $this->setData(self::IS_AVAILABLE, $availability);

    }

    /**
     * {@inheritDoc}
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * {@inheritDoc}
     */
    public function setSpecialPrice($price)
    {
        return $this->setData(self::SPECIAL_PRICE, $price);
    }

    /**
     * {@inheritDoc}
     */
    public function setStartDate($startDate)
    {
        return $this->setData(self::START_DATE, $startDate);
    }

    /**
     * {@inheritDoc}
     */
    public function setEndDate($endDate)
    {
        return $this->setData(self::END_DATE, $endDate);
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName) Method is inherited
     */
    protected function _construct()
    {
        $this->_init('Smile\Offer\Model\ResourceModel\Offer');
    }
}
