<?php

declare(strict_types=1);

namespace Smile\Offer\Model;

use Magento\Framework\Api\SearchResults;
use Smile\Offer\Api\Data\OfferSearchResultsInterface;

/**
 * Offer search results.
 */
class OfferSearchResults extends SearchResults implements OfferSearchResultsInterface
{
    /**
     * @inheritdoc
     */
    public function getItems(): array
    {
        return parent::getItems();
    }

    /**
     * @inheritdoc
     */
    public function setItems(array $items): self
    {
        return parent::setItems($items);
    }
}
