<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\Offer
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\Offer\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Copy offer data from quote to order
 *
 * @category Smile
 * @package  Smile\Offer
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class AddOfferDataToOrder implements ObserverInterface
{
    /**
     * Set offer data to order from quote
     *
     * @param \Magento\Framework\Event\Observer $observer The observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $observer->getEvent()->getOrder()->setSellerId($observer->getEvent()->getQuote()->getSellerId());
        $observer->getEvent()->getOrder()->setPickupDate($observer->getEvent()->getQuote()->getPickupDate());

        return $this;
    }
}
