<?php

namespace Smile\Offer\Model\Locator;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Smile\Offer\Api\Data\OfferInterface;

/**
 * Registry Locator for offers.
 */
class RegistryLocator implements LocatorInterface
{
    private ?OfferInterface $offer = null;

    public function __construct(private Registry $registry)
    {
    }

    /**
     * @inheritdoc
     */
    public function getOffer(): OfferInterface
    {
        if (null !== $this->offer) {
            return $this->offer;
        }

        if ($this->registry->registry('current_offer')) {
            return $this->offer = $this->registry->registry('current_offer');
        }

        throw new NotFoundException(__('Offer was not registered'));
    }
}
