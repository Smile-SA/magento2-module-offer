<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ElasticsuiteCatalog
 * @author    Sébastien Le Guellec <sebastien.leguellec@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\Offer\Plugin\Indexer\Offer\Save;

use Smile\ElasticsuiteCatalog\Plugin\Indexer\AbstractIndexerPlugin;

/**
 * Plugin that proceed products reindex after offer saving
 *
 * @category Smile
 * @package  Smile\ElasticsuiteCatalog
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class ReindexProductsAfterSave extends AbstractIndexerPlugin
{
    /**
     * Reindex products after saving offer
     *
     * @param \Smile\Offer\Model\ResourceModel\Offer $resource The offer resource being saved
     * @param callable                               $proceed  The parent function we are plugged on
     *                                                 : Magento\Catalog\Model\Category::reindex()
     * @param \Smile\Offer\Model\Offer               $subject  The offer being saved
     *
     * @return \Smile\Offer\Model\Offer
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
