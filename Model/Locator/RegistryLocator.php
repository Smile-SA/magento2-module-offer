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

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Smile\Offer\Api\Data\OfferInterface;

/**
 * Registry Locator for offers
 *
 * @category Smile
 * @package  Smile\Offer
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class RegistryLocator implements LocatorInterface
{
    /**
     * @var Registry
     */
    private Registry $registry;

    /**
     * @var OfferInterface
     */
    private OfferInterface $offer;

    /**
     * @param Registry $registry The application registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotFoundException
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
