<?php

declare(strict_types=1);

namespace Smile\Offer\Model\ResourceModel\Offer\Grid;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\EntityFactory;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;
use Smile\Offer\Api\Data\OfferInterface;
use Smile\Seller\Api\Data\SellerInterface;
use Zend_Db_Expr;

/**
 * Offer Grid Collection.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Smile\Offer\Model\ResourceModel\Offer\Collection
{
    /**
     * An array of external entities attributes to add to the current collection.
     */
    private array $fieldAlias = [];

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        MetadataPool $metadataPool,
        private EntityFactory $eavEntityFactory,
        ?AdapterInterface $connection = null,
        ?AbstractDb $resource = null,
        ?string $sellerEntity = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $metadataPool,
            $connection,
            $resource,
            $sellerEntity
        );
    }

    /**
     * Process left join on catalog/product entity table to retrieve the SKU Field.
     *
     * Also bind "sku" column to a proper alias if given.
     */
    public function addProductSkuToSelect(?string $alias = null): self
    {
        $skuField = Product::SKU;

        if (isset($this->fieldAlias[Product::ENTITY][$skuField])) {
            return $this;
        }

        $columns = null === $alias ? [$skuField => $skuField] : [$alias => $skuField];

        $this->fieldAlias[Product::ENTITY][$skuField] = $alias ?? $skuField;

        $fromPart = $this->getSelect()->getPart('from');
        if (!isset($fromPart['catalog_product_entity'])) {
            $this->getSelect()->joinLeft(
                ["cpe" => $this->getTable("catalog_product_entity")],
                new Zend_Db_Expr("cpe.entity_id = main_table.product_id"),
                $columns
            );
        }

        return $this;
    }

    /**
     * Filter the collection for a given SKU.
     */
    public function setSkuFilter(mixed $condition): void
    {
        $this->addProductSkuToSelect();
        $field = $this->fieldAlias[Product::ENTITY][Product::SKU];
        $this->addFieldToFilter($field, $condition);
    }

    /**
     * Add Attribute of an other entity to current collection (Seller, Product).
     */
    public function addEntityAttributeToSelect(
        string $entityType,
        string $attributeCode,
        ?string $alias = null,
        ?int $storeId = null,
        ?array $join = null
    ): self {
        if (isset($this->fieldAlias[$entityType][$attributeCode])) {
            return $this;
        }

        $columns = null === $alias ? [$attributeCode => 'value'] : [$alias => 'value'];

        $metadata      = $this->metadataPool->getMetadata($entityType);
        $entity        = $this->eavEntityFactory->create()->setType($metadata->getEavEntityType());
        $attribute     = $entity->getAttribute($attributeCode);
        $linkField     = $metadata->getLinkField();
        $foreignKey    = $this->getForeignKeyByEntityType($entityType);
        $idField       = OfferInterface::OFFER_ID;

        $this->fieldAlias[$entityType][$attributeCode] = $alias;

        if ($attribute && !$attribute->isStatic()) {
            $backendTable = $attribute->getBackendTable();
            $attributeTableAlias = $this->getEntityAttributeTableAlias($entityType, $attributeCode);

            $foreignKeyCondition = "main_table.{$foreignKey}";
            if (!is_null($join)) {
                $this->getSelect()->joinLeft(
                    $join['name'],
                    $join['cond'],
                    $join['cols']
                );
                $foreignKeyCondition = $join['foreignKeyCondition'];
            }

            // Join entity attribute value table.
            $this->getSelect()->joinLeft(
                ["{$attributeTableAlias}_d" => $this->getTable($backendTable)],
                implode(
                    ' AND ',
                    [
                        new Zend_Db_Expr("{$attributeTableAlias}_d.{$linkField} = {$foreignKeyCondition}"),
                        new Zend_Db_Expr("{$attributeTableAlias}_d.attribute_id = " . $attribute->getId()),
                    ]
                ),
                $columns
            );

            $storeCondition = Store::DEFAULT_STORE_ID;

            // Apply correct store to attribute value table, if any.
            if ($storeId) {
                $joinCondition = [
                    "{$attributeTableAlias}_s.attribute_id = {$attributeTableAlias}_d.attribute_id",
                    "{$attributeTableAlias}_s.{$linkField} = {$attributeTableAlias}_d.{$linkField}",
                    $this->getConnection()->quoteInto("{$attributeTableAlias}_s.store_id = ?", $storeId),
                ];

                $this->getSelect()->joinLeft(
                    ["{$attributeTableAlias}_s" => $backendTable],
                    implode(' AND ', $joinCondition),
                    []
                );

                $storeCondition = $this->getConnection()->getIfNullSql(
                    "{$attributeTableAlias}_s.store_id",
                    Store::DEFAULT_STORE_ID
                );
            }

            $this->getSelect()->where("{$attributeTableAlias}_d.store_id = ?", $storeCondition);
            $this->getSelect()->group("main_table.{$idField}");
        }

        return $this;
    }

    /**
     * Append filter on an external entity attribute (retailer or product).
     */
    public function addEntityAttributeFilter(string $entityType, string $field, mixed $condition): void
    {
        $attributeTableAlias = $this->getEntityAttributeTableAlias($entityType, $field);
        $field = $attributeTableAlias . "_d.value";
        $this->addFieldToFilter($field, $condition);
    }

    /**
     * Retrieve proper field for binding on other entities.
     *
     * @throws NoSuchEntityException
     */
    public function getForeignKeyByEntityType(string $entity): string
    {
        $metadata   = $this->metadataPool->getMetadata($entity);
        $entityType = $metadata->getEavEntityType();

        if ($entityType == SellerInterface::ENTITY) {
            return OfferInterface::SELLER_ID;
        }

        if ($entityType == Product::ENTITY) {
            return OfferInterface::PRODUCT_ID;
        }

        throw new NoSuchEntityException(__("Unable to retrieve fetch strategy for {$entityType}"));
    }

    /**
     * Get an alias for a given couple entity/attributeCode to ensure unicity on SQL query.
     */
    private function getEntityAttributeTableAlias(string $entity, string $attributeCode): string
    {
        $metadata   = $this->metadataPool->getMetadata($entity);
        $entityType = $metadata->getEavEntityType();

        $field = $attributeCode;
        if (isset($this->fieldAlias[$entityType][$attributeCode])) {
            $field = $this->fieldAlias[$entityType][$attributeCode];
        }

        return $entityType . "_" . $field;
    }
}
