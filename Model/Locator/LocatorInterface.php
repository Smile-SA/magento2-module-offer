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
namespace Smile\Offer\Model\Locator;

use Smile\Offer\Api\Data\OfferInterface;

/**
 * Offer Locator Interface
 *
 * @category Smile
 * @package  Smile\Offer
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
interface LocatorInterface
{
    /**
     * @return OfferInterface
     */
    public function getOffer(): OfferInterface;
}
