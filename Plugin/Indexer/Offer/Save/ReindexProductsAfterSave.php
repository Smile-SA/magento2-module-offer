<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ElasticsuiteCatalog
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\Offer\Plugin\Indexer\Offer\Save;

use Smile\ElasticsuiteCatalog\Plugin\Indexer\AbstractIndexerPlugin;

/**
 * Plugin that proceed products reindex after category reindexing
 *
 * @category Smile
 * @package  Smile\ElasticsuiteCatalog
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class ReindexProductsAfterSave extends AbstractIndexerPlugin
{
    /**
     * Reindex category's products after reindexing the category
     *
     * @param \Magento\Catalog\Model\Category $subject The cateogry being reindexed
     * @param callable                        $proceed The parent function we are plugged on
     *                                                 : Magento\Catalog\Model\Category::reindex()
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function aroundSave(
        \Smile\Offer\Model\ResourceModel\Offer $resource,
        callable $proceed,
        \Smile\Offer\Model\Offer $subject
    ) {
        $returnValue = $proceed($subject);
        if (!empty($subject->getId())) {
            $this->processFullTextIndex($subject->getProductId());
        }

        return $returnValue;
    }
}
