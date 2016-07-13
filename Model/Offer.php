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
use Symfony\Component\Config\Definition\Exception\Exception;

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
     * Initialize offer  model data from array.
     * Convert Date Fields to proper DateTime objects.
     *
     * @param array $data The data
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function loadPost(array $data)
    {
        $validationResults = $this->validateData(new \Magento\Framework\DataObject($data));
        if ($validationResults !== true) {
            throw new \Exception(implode($validationResults));
        }

        $dateFields = [OfferInterface::START_DATE, OfferInterface::END_DATE];
        foreach ($data as $key => $value) {
            if (in_array($key, $dateFields) && $value) {
                $value = new \DateTime($value);
            }

            // @TODO : This part is messy and needs to be refactored.
            if ($key === OfferInterface::PRODUCT_ID && $value) {
                if (is_array($value)) {
                    $value = current($value);
                }
                $value = str_replace("product/", "", $value);
            }

            $this->setData($key, $value);
        }

        return $this;
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

    /**
     * Validate offer data
     *
     * @param \Magento\Framework\DataObject $dataObject The Offer
     *
     * @return bool|string[] - return true if validation passed successfully. Array with errors description otherwise
     */
    private function validateData(\Magento\Framework\DataObject $dataObject)
    {
        $result = [];

        $validateDateResult = $this->validateDateFields($dataObject);

        if (true !== $validateDateResult) {
            return $validateDateResult;
        }

        if (!$dataObject->hasData(OfferInterface::PRODUCT_ID)) {
            $result[] = __('Product is required.');
        }

        if (is_array($dataObject->getData(OfferInterface::PRODUCT_ID))) {
            $data = array_filter($dataObject->getData(OfferInterface::PRODUCT_ID));
            if (!isset($data[OfferInterface::PRODUCT_ID]) || ($data[OfferInterface::PRODUCT_ID] == null)) {
                $result[] = __('Product is required.');
            }
        }

        if (!$dataObject->hasData(OfferInterface::SELLER_ID)) {
            $result[] = __('Seller is required.');
        }

        if (empty($result)) {
            return true;
        }

        return $result;
    }

    /**
     * Validate offer date data
     *
     * @param \Magento\Framework\DataObject $dataObject The Offer
     *
     * @return bool|string[] - return true if validation passed successfully. Array with errors description otherwise
     */
    private function validateDateFields(\Magento\Framework\DataObject $dataObject)
    {
        $fromDate = $toDate = null;

        if ($dataObject->hasStartDate() && $dataObject->hasEndDate()) {
            $fromDate = $dataObject->getStartDate();
            $toDate = $dataObject->getEndDate();
        }

        if ($fromDate && $toDate) {
            $fromDate = new \DateTime($fromDate);
            $toDate = new \DateTime($toDate);

            if ($fromDate > $toDate) {
                $result[] = __('End Date must follow Start Date.');
            }
        }

        if (empty($result)) {
            return true;
        }

        return $result;
    }
}
