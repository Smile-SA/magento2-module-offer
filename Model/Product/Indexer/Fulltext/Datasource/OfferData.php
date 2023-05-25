<?php

namespace Smile\Offer\Model\Product\Indexer\Fulltext\Datasource;

use Magento\Customer\Api\Data\GroupInterface as CustomerGroupInterface;
use Smile\ElasticsuiteCore\Api\Index\DatasourceInterface;
use Smile\Offer\Model\ResourceModel\Product\Indexer\Fulltext\Datasource\OfferData as ResourceModel;

/**
 * Datasource used to append prices data to product during indexing.
 */
class OfferData implements DatasourceInterface
{
    public function __construct(private ResourceModel $resourceModel)
    {
    }

    /**
     * @inheritdoc
     */
    public function addData($storeId, array $indexData)
    {
        $offerData = $this->resourceModel->loadOfferData(array_keys($indexData));

        foreach ($offerData as $offerDataRow) {
            $productId = (int) $offerDataRow['product_id'];
            $offerDataRow = $this->processOfferPrices($offerDataRow, $indexData[$productId]);

            $offerDataRow['offer_id'] = (int) $offerDataRow['offer_id'];
            $offerDataRow['product_id'] = (int) $offerDataRow['product_id'];
            $offerDataRow['seller_id'] = (int) $offerDataRow['seller_id'];
            $offerDataRow['is_available'] = (bool) ($offerDataRow['is_available'] ?? false);
            $indexData[$productId]['offer'][] = $offerDataRow;
        }

        return $indexData;
    }

    /**
     * Process offer prices.
     */
    private function processOfferPrices(array $offerData, array $productData): array
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
            $offerData['original_price'] = $offerData['price'] ?? $defaultPriceData['original_price'];
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
