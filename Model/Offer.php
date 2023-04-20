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

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Smile\Offer\Api\Data\OfferInterface;

/**
 * Offer Model
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName) properties are inherited.
 *
 * @category Smile
 * @package  Smile\Offer
 * @author   Aurelien Foucret <aurelien.foucret@smile.fr>
 */
class Offer extends AbstractExtensibleModel implements OfferInterface, IdentityInterface
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
    public function getId(): int|null
    {
        return $this->getData(self::OFFER_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function getProductId(): int|null
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     *
     * {@inheritDoc}
     */
    public function getSellerId(): int|null
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function isAvailable(): bool|null
    {
        return (bool) $this->getData(self::IS_AVAILABLE);
    }

    /**
     * {@inheritDoc}
     */
    public function getPrice(): float|null
    {
        return $this->getData(self::PRICE);
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecialPrice(): float|null
    {
        return $this->getData(self::SPECIAL_PRICE);
    }

    /**
     * {@inheritDoc}
     */
    public function setId($offerId): OfferInterface
    {
        return $this->setData(self::OFFER_ID, $offerId);
    }

    /**
     * {@inheritDoc}
     */
    public function setProductId(int $productId): OfferInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * {@inheritDoc}
     */
    public function setSellerId(int $sellerId): OfferInterface
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * {@inheritDoc}
     */
    public function setIsAvailable(bool $availability): OfferInterface
    {
        return $this->setData(self::IS_AVAILABLE, $availability);
    }

    /**
     * {@inheritDoc}
     */
    public function setPrice(float $price): OfferInterface
    {
        $this->setData(self::PRICE, $price);

        if (empty($price)) {
            $this->unsetData(self::PRICE);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setSpecialPrice(float $price): OfferInterface
    {
        $this->setData(self::SPECIAL_PRICE, $price);

        if (empty($price)) {
            $this->unsetData(self::SPECIAL_PRICE);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentities(): array
    {
        $identities = [self::CACHE_TAG . '_' . $this->getId()];

        if (!$this->getId() || $this->hasDataChanges() || $this->isDeleted()) {
            $identities[] = \Magento\Catalog\Model\Product::CACHE_TAG . '_' . $this->getProductId();
        }

        if ($this->_appState->getAreaCode() == \Magento\Framework\App\Area::AREA_FRONTEND) {
            $identities[] = self::CACHE_TAG;
        }

        return $identities;
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
    public function loadPost(array $data): self
    {
        $validationResults = $this->validateData(new \Magento\Framework\DataObject($data));
        if ($validationResults !== true) {
            throw new \Exception(implode($validationResults));
        }

        foreach ($data as $key => $value) {
            if ($key === OfferInterface::PRODUCT_ID && $value) {
                $value = str_replace("product/", "", $value);
            }

            $this->setData($key, $value);

            if (in_array($key, [self::PRICE, self::SPECIAL_PRICE]) && empty($value)) {
                $this->unsetData($key);
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtensionAttributes(): \Smile\Offer\Api\Data\OfferExtensionInterface|null
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory
                ->create('Smile\Offer\Api\Data\OfferInterface');
            $this->_setExtensionAttributes($extensionAttributes);
        }

        return $extensionAttributes;
    }

    /**
     * {@inheritDoc}
     */
    public function setExtensionAttributes(\Smile\Offer\Api\Data\OfferExtensionInterface $extensionAttributes): self
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName) Method is inherited
     */
    protected function _construct(): void
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
    private function validateData(\Magento\Framework\DataObject $dataObject): bool|array
    {
        $result = [];

        if (!$dataObject->hasData(OfferInterface::PRODUCT_ID)
            || ("" == $dataObject->getData(OfferInterface::PRODUCT_ID) )
        ) {
            $result[] = __('Product is required.');
        }

        if (!$dataObject->hasData(OfferInterface::SELLER_ID)) {
            $result[] = __('Seller is required.');
        }

        if (empty($result)) {
            return true;
        }

        return $result;
    }
}
