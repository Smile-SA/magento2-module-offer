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

namespace Smile\Offer\Model\Product\Indexer\Fulltext\Datasource;

use Smile\ElasticsuiteCore\Api\Index\DatasourceInterface;
use Smile\Offer\Model\ResourceModel\Product\Indexer\Fulltext\Datasource\OfferData as ResourceModel;
use Magento\Catalog\Model\Product\TypeFactory as ProductTypeFactory;
use Magento\Customer\Api\Data\GroupInterface as CustomerGroupInterface;

/**
 * Datasource used to append prices data to product during indexing.
 *
 * @category Smile
 * @package  Smile\ElasticsuiteCatalog
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class OfferData implements DatasourceInterface
{
    /**
     * @var \Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Indexer\Fulltext\Datasource\OfferData
     */
    private $resourceModel;

    /**
     * Constructor.
     *
     * @param ResourceModel      $resourceModel      Resource model
     */
    public function __construct(ResourceModel $resourceModel)
    {
        $this->resourceModel = $resourceModel;
    }

    /**
     * Add price data to the index data.
     *
     * {@inheritdoc}
     */
    public function addData($storeId, array $indexData)
    {
        $offerData = $this->resourceModel->loadOfferData(array_keys($indexData));

        foreach ($offerData as $productId => $offerDataRow) {
            $productId = (int) $offerDataRow['product_id'];
            $offerDataRow = $this->processOfferPrices($offerDataRow, $indexData[$productId]);
            $indexData[$productId]['offer'][] = $offerDataRow;
        }

        return $indexData;
    }

    private function processOfferPrices($offerData, $productData)
    {
        $defaultPriceData = [];

        foreach ($productData['price'] as $currentPriceData) {
            if ($currentPriceData['customer_group_id'] == CustomerGroupInterface::NOT_LOGGED_IN_ID) {
                $defaultPriceData = $currentPriceData;
            }
        }

        $offerData = array_filter($offerData);
        $offerData['original_price'] = isset($offerData['price']) ? $offerData['price'] : $defaultPriceData['original_price'];

        if (isset($offerData['special_price'])) {
            $offerData['price'] = min($offerData['price'], $offerData['special_price']);
            unset($offerData['special_price']);
        } else {
            $offerData['price'] = $defaultPriceData['price'];
        }

        $offerData['is_discount'] = $offerData['price'] < $offerData['original_price'];


        return $offerData;
    }
}
