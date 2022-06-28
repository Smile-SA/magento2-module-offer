<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\Offer
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\Offer\Model\Product\Indexer\Fulltext\Datasource;

use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\App\ObjectManager;
use Smile\ElasticsuiteCore\Api\Index\DatasourceInterface;
use Smile\Offer\Model\ResourceModel\Product\Indexer\Fulltext\Datasource\OfferData as ResourceModel;
use Magento\Customer\Api\Data\GroupInterface as CustomerGroupInterface;
use Magento\Framework\Indexer\CacheContext;

/**
 * Datasource used to append prices data to product during indexing.
 *
 * @category Smile
 * @package  Smile\Offer
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class OfferData implements DatasourceInterface
{
    /**
     * @var ResourceModel
     */
    private $resourceModel;

    /**
     * @var CacheContext
     */
    private $cacheContext;

    /**
     * Constructor.
     *
     * @param ResourceModel     $resourceModel Resource model
     * @param CacheContext|null $cacheContext  Indexer cache context
     */
    public function __construct(
        ResourceModel $resourceModel,
        CacheContext $cacheContext = null
    ) {
        $this->resourceModel = $resourceModel;
        $this->cacheContext = $cacheContext ?: ObjectManager::getInstance()->get(CacheContext::class);
    }

    /**
     * Add price data to the index data.
     *
     * {@inheritdoc}
     */
    public function addData($storeId, array $indexData)
    {
        $entitiesIds = [];
        $offerData = $this->resourceModel->loadOfferData(array_keys($indexData));

        foreach ($offerData as $offerDataRow) {
            $productId = (int) $offerDataRow['product_id'];
            $offerDataRow = $this->processOfferPrices($offerDataRow, $indexData[$productId]);

            $offerDataRow['offer_id']     = (int) $offerDataRow['offer_id'];
            $offerDataRow['product_id']   = (int) $offerDataRow['product_id'];
            $offerDataRow['seller_id']    = (int) $offerDataRow['seller_id'];
            $offerDataRow['is_available'] = (bool) ($offerDataRow['is_available'] ?? false);

            $entitiesIds[] = $productId;
            $indexData[$productId]['offer'][] = $offerDataRow;
        }

        if ($entitiesIds) {
            $this->cacheContext->registerEntities(ProductModel::CACHE_TAG, $entitiesIds);
        }

        return $indexData;
    }

    /**
     * Process offer prices
     *
     * @param array $offerData   Offer Data
     * @param array $productData Product Data
     *
     * @return array
     */
    private function processOfferPrices($offerData, $productData)
    {
        $defaultPriceData = [];
        if (isset($productData['price'])) {
            foreach ($productData['price'] as $currentPriceData) {
                if ($currentPriceData['customer_group_id'] == CustomerGroupInterface::NOT_LOGGED_IN_ID) {
                    $defaultPriceData = $currentPriceData;
                    break;
                }
            }

            $offerData = array_filter($offerData);
            $offerData['original_price'] = isset($offerData['price']) ? $offerData['price'] : $defaultPriceData['original_price'];
            $offerData['price'] = $offerData['original_price'];
            if (isset($offerData['special_price'])) {
                $offerData['price'] = min($offerData['price'], $offerData['special_price']);
                unset($offerData['special_price']);
            }

            $offerData['is_discount'] = $offerData['price'] < $offerData['original_price'];
        }

        return $offerData;
    }
}
