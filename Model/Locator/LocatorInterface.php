<?php

namespace Smile\Offer\Model\Locator;

use Magento\Framework\Exception\NotFoundException;
use Smile\Offer\Api\Data\OfferInterface;

/**
 * Offer Locator Interface.
 */
interface LocatorInterface
{
    /**
     * Get the offer.
     *
     * @throws NotFoundException
     */
    public function getOffer(): OfferInterface;
}
