<?php

declare(strict_types=1);

namespace Smile\Offer\Model\ResourceModel\Product\Indexer\Fulltext\Datasource;

use Smile\ElasticsuiteCatalog\Model\ResourceModel\Eav\Indexer\Indexer;

/**
 * Prices data datasource resource model.
 */
class OfferData extends Indexer
{
    /**
     * Load prices data for a list of product ids and a given store.
     */
    public function loadOfferData(array $productIds): array
    {
        $select = $this->getConnection()->select()
            ->from(['o' => $this->getTable('smile_offer')])
            ->where('o.product_id IN(?)', $productIds);

        return $this->getConnection()->fetchAll($select);
    }
}
