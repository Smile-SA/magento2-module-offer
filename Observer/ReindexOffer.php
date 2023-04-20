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

use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Smile\Offer\Model\Offer;

/**
 * Observer that process Offer Reindexing
 *
 * @category Smile
 * @package  Smile\Offer
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class ReindexOffer implements ObserverInterface
{
    /**
     * @var IndexerRegistry
     */
    private IndexerRegistry $indexerRegistry;

    /**
     * @var IndexerInterface
     */
    private IndexerInterface $indexer;

    /**
     * ReindexOffer constructor.
     *
     * @param IndexerRegistry $indexerRegistry Indexer Registry
     */
    public function __construct(
        IndexerRegistry $indexerRegistry
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->indexer = $this->indexerRegistry->get(Fulltext::INDEXER_ID);
    }

    /**
     * @param Observer $observer The observer
     *
     * @event smile_offer_api_data_offerinterface_save_after
     * @event smile_offer_api_data_offerinterface_delete_after
     *
     * @return void
     */
    public function execute(Observer $observer): void
    {
        /** @var Offer $offer */
        $offer = $observer->getEvent()->getEntity();

        if (!$this->indexer->isScheduled()) {
            $offer->getResource()->addCommitCallback(function () use ($offer) {
                $this->indexer->reindexRow($offer->getProductId());
            });
        }
    }
}
