<?php

namespace Smile\Offer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Copy offer data from quote to order.
 */
class AddOfferDataToOrder implements ObserverInterface
{
    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        $observer->getEvent()->getOrder()->setSellerId($observer->getEvent()->getQuote()->getSellerId());
        $observer->getEvent()->getOrder()->setPickupDate($observer->getEvent()->getQuote()->getPickupDate());

        return $this;
    }
}
