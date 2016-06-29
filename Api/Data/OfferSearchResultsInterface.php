<?php

namespace Smile\Offer\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for offer search results.
 * @api
 */
interface OfferSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get blocks list.
     *
     * @return \Magento\Cms\Api\Data\BlockInterface[]
     */
    public function getItems();

    /**
     * Set blocks list.
     *
     * @param \Magento\Cms\Api\Data\BlockInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
