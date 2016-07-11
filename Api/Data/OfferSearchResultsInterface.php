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

namespace Smile\Offer\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for offer search results.
 *
 * @api
 */
interface OfferSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get offers list.
     *
     * @return \Smile\Offer\Api\Data\OfferInterface[]
     */
    public function getItems();

    /**
     * Set offers list.
     *
     * @param \Smile\Offer\Api\Data\OfferInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
