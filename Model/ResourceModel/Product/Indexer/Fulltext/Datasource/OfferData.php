<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ElasticsuiteCatalog
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\Offer\Model\ResourceModel\Product\Indexer\Fulltext\Datasource;

use Smile\ElasticsuiteCatalog\Model\ResourceModel\Eav\Indexer\Indexer;

/**
 * Prices data datasource resource model.
 *
 * @category  Smile
 * @package   Smile\ElasticsuiteCatalog
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class OfferData extends Indexer
{
    /**
     * Load prices data for a list of product ids and a given store.
     *
     * @param array $productIds Product ids list.
     *
     * @return array
     */
    public function loadOfferData($productIds)
    {
        $select = $this->getConnection()->select()
            ->from(['o' => $this->getTable('smile_offer')])
            ->where('o.product_id IN(?)', $productIds);

        return $this->getConnection()->fetchAll($select);
    }
}
