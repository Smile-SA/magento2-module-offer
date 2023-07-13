<?php

declare(strict_types=1);

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
    public function getItems(): array;

    /**
     * Set offers list.
     *
     * @param \Smile\Offer\Api\Data\OfferInterface[] $items The items
     * @return $this
     */
    public function setItems(array $items): self;
}
