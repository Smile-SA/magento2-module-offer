<?php

declare(strict_types=1);

namespace Smile\Offer\Observer;

use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Smile\Offer\Model\Offer;

/**
 * Observer that process Offer Reindexing.
 */
class ReindexOffer implements ObserverInterface
{
    private IndexerInterface $indexer;

    public function __construct(private IndexerRegistry $indexerRegistry)
    {
        $this->indexer = $this->indexerRegistry->get(Fulltext::INDEXER_ID);
    }

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        /** @var Offer $offer */
        $offer = $observer->getEvent()->getEntity();

        if (!$this->indexer->isScheduled()) {
            $offer->getResource()->addCommitCallback(function () use ($offer): void {
                $this->indexer->reindexRow($offer->getProductId());
            });
        }
    }
}
