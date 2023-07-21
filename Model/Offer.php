<?php

declare(strict_types=1);

namespace Smile\Offer\Model;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractExtensibleModel;
use Smile\Offer\Api\Data\OfferExtensionInterface;
use Smile\Offer\Api\Data\OfferInterface;

/**
 * Offer Model.
 */
class Offer extends AbstractExtensibleModel implements OfferInterface, IdentityInterface
{
    public const CACHE_TAG = 'smile_offer';

    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
    protected $_cacheTag = self::CACHE_TAG;
    protected $_eventPrefix = 'smile_offer';
    // phpcs:enable

    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel\Offer::class);
    }

    /**
     * @inheritdoc
     */
    public function getOfferId(): ?int
    {
        return (int) $this->getData(self::OFFER_ID);
    }

    /**
     * @inheritdoc
     */
    public function getProductId(): ?int
    {
        return (int) $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritdoc
     */
    public function getSellerId(): ?int
    {
        return (int) $this->getData(self::SELLER_ID);
    }

    /**
     * @inheritdoc
     */
    public function isAvailable(): ?bool
    {
        return (bool) $this->getData(self::IS_AVAILABLE);
    }

    /**
     * @inheritdoc
     */
    public function getPrice(): ?float
    {
        return (float) $this->getData(self::PRICE);
    }

    /**
     * @inheritdoc
     */
    public function getSpecialPrice(): ?float
    {
        return (float) $this->getData(self::SPECIAL_PRICE);
    }

    /**
     * @inheritdoc
     */
    public function setOfferId(int $offerId): OfferInterface
    {
        return $this->setData(self::OFFER_ID, $offerId);
    }

    /**
     * @inheritdoc
     */
    public function setProductId(int $productId): OfferInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @inheritdoc
     */
    public function setSellerId(int $sellerId): OfferInterface
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * @inheritdoc
     */
    public function setIsAvailable(bool $availability): OfferInterface
    {
        return $this->setData(self::IS_AVAILABLE, $availability);
    }

    /**
     * @inheritdoc
     */
    public function setPrice(?float $price): OfferInterface
    {
        $this->setData(self::PRICE, $price);

        if (empty($price)) {
            $this->unsetData(self::PRICE);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setSpecialPrice(?float $price): OfferInterface
    {
        $this->setData(self::SPECIAL_PRICE, $price);

        if (empty($price)) {
            $this->unsetData(self::SPECIAL_PRICE);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIdentities(): array
    {
        $identities = [self::CACHE_TAG . '_' . $this->getOfferId()];

        if (!$this->getOfferId() || $this->hasDataChanges() || $this->isDeleted()) {
            $identities[] = Product::CACHE_TAG . '_' . $this->getProductId();
        }

        if ($this->_appState->getAreaCode() == Area::AREA_FRONTEND) {
            $identities[] = self::CACHE_TAG;
        }

        return $identities;
    }

    /**
     * Initialize offer  model data from array.
     *
     * Convert Date Fields to proper DateTime objects.
     *
     * @throws LocalizedException
     */
    public function loadPost(array $data): self
    {
        $validationResults = $this->validateData(new DataObject($data));
        if ($validationResults !== true) {
            throw new LocalizedException(__(implode($validationResults)));
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
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?OfferExtensionInterface
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        // @phpstan-ignore-next-line - this if seems not necessary, TODO: test without it
        if (!$extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(OfferInterface::class);
            $this->_setExtensionAttributes($extensionAttributes);
        }

        /** @var OfferExtensionInterface $extensionAttributes */
        return $extensionAttributes;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(OfferExtensionInterface $extensionAttributes): self
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Validate offer data.
     *
     * @return bool|string[] - return true if validation passed successfully. Array with errors description otherwise
     */
    private function validateData(DataObject $dataObject): bool|array
    {
        $result = [];

        if (
            !$dataObject->hasData(OfferInterface::PRODUCT_ID)
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
