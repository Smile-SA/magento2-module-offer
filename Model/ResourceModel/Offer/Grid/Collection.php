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
namespace Smile\Offer\Model\ResourceModel\Offer\Grid;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Psr\Log\LoggerInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\EntityFactory;
use Smile\Offer\Api\Data\OfferInterface;
use Smile\Seller\Api\Data\SellerInterfaceFactory;

/**
 * Offer Grid Collection
 *
 * @category Smile
 * @package  Smile\Offer
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class Collection extends \Smile\Offer\Model\ResourceModel\Offer\Collection
{
    /**
     * @var EntityFactory
     */
    private EntityFactory $eavEntityFactory;

    /**
     * @var array an array of external entities attributes to add to the current collection.
     */
    private array $fieldAlias = [];

    /**
     * Collection constructor.
     *
     * @param EntityFactoryInterface $entityFactory    Entity Factory
     * @param LoggerInterface        $logger           Logger Interface
     * @param FetchStrategyInterface $fetchStrategy    Fetch Strategy
     * @param ManagerInterface       $eventManager     Event Manager
     * @param MetadataPool           $metadataPool     Metadata Pool
     * @param EntityFactory          $eavEntityFactory EAV ENtity Factory
     * @param AdapterInterface|null  $connection       Database Connection
     * @param AbstractDb|null        $resource         Resource Model
     * @param string|null            $sellerEntity     The seller type to filter on. If Any.
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        MetadataPool $metadataPool,
        EntityFactory $eavEntityFactory,
        $connection = null,
        $resource = null,
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

        $this->eavEntityFactory = $eavEntityFactory;
    }

    /**
     * Process left join on catalog/product entity table to retrieve the SKU Field.
     * Also bind "sku" column to a proper alias if given
     *
     * @param ?string $alias The alias for SKU field, if any.
     *
     * @return $this;
     */
    public function addProductSkuToSelect(string $alias = null): self
    {
        $skuField = Product::SKU;

        if (isset($this->fieldAlias[Product::ENTITY][$skuField])) {
            return $this;
        }

        $columns = (null === $alias) ? [$skuField => $skuField] : [$alias => $skuField];

        $this->fieldAlias[Product::ENTITY][$skuField] = (null === $alias) ? $skuField : $alias;

        $fromPart = $this->getSelect()->getPart('from');
        if(!isset($fromPart['catalog_product_entity'])) {
            $this->getSelect()->joinLeft(
                ["cpe" => $this->getTable("catalog_product_entity")],
                new \Zend_Db_Expr("cpe.entity_id = main_table.product_id"),
                $columns
            );
        }

        return $this;
    }

    /**
     * Filter the collection for a given SKU
     *
     * @param array $condition A SQL Condition
     */
    public function setSkuFilter(array $condition): void
    {
        $this->addProductSkuToSelect();
        $field = $this->fieldAlias[Product::ENTITY][Product::SKU];
        $this->addFieldToFilter($field, $condition);
    }

    /**
     * Add Attribute of an other entity to current collection (Seller, Product).
     *
     * @param string  $entityType    The Entity Type
     * @param string  $attributeCode The Attribute code
     * @param ?string $alias         The field alias, if any
     * @param ?int    $storeId       The current store scope for seller attributes retrieval
     * @param ?array  $join          Join table for Magento EE
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return $this
     */
    public function addEntityAttributeToSelect(
        string $entityType,
        string $attributeCode,
        string $alias = null,
        int $storeId = null,
        array $join = null
    ): self {
        if (isset($this->fieldAlias[$entityType][$attributeCode])) {
            return $this;
        }

        $columns = (null === $alias) ? [$attributeCode => 'value'] : [$alias => 'value'];

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
                        new \Zend_Db_Expr("{$attributeTableAlias}_d.{$linkField} = {$foreignKeyCondition}"),
                        new \Zend_Db_Expr("{$attributeTableAlias}_d.attribute_id = ".$attribute->getId()),
                    ]
                ),
                $columns
            );

            $storeCondition = \Magento\Store\Model\Store::DEFAULT_STORE_ID;

            // Apply correct store to attribute value table, if any.
            if ($storeId) {
                $joinCondition = [
                    "{$attributeTableAlias}_s.attribute_id = {$attributeTableAlias}_d.attribute_id",
                    "{$attributeTableAlias}_s.{$linkField} = {$attributeTableAlias}_d.{$linkField}",
                    $this->getConnection()->quoteInto("{$attributeTableAlias}_s.store_id = ?", $storeId),
                ];

                $this->getSelect()->joinLeft(["{$attributeTableAlias}_s" => $backendTable], implode(' AND ', $joinCondition), []);
                $storeCondition = $this->getConnection()->getIfNullSql(
                    "{$attributeTableAlias}_s.store_id",
                    \Magento\Store\Model\Store::DEFAULT_STORE_ID
                );
            }

            $this->getSelect()->where("{$attributeTableAlias}_d.store_id = ?", $storeCondition);
            $this->getSelect()->group("main_table.{$idField}");
        }

        return $this;
    }

    /**
     * Append filter on an external entity attribute (retailer or product).
     *
     * @param string $entityType The entity type
     * @param string $field      The field
     * @param array  $condition  The SQL Condition
     */
    public function addEntityAttributeFilter(string $entityType, string $field, array $condition): void
    {
        $attributeTableAlias = $this->getEntityAttributeTableAlias($entityType, $field);
        $field = $attributeTableAlias . "_d.value";
        $this->addFieldToFilter($field, $condition);
    }

    /**
     * Retrieve proper field for binding on other entities.
     *
     * @param string $entity The entity
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getForeignKeyByEntityType(string $entity): string
    {
        $metadata   = $this->metadataPool->getMetadata($entity);
        $entityType = $metadata->getEavEntityType();

        if ($entityType == \Smile\Seller\Api\Data\SellerInterface::ENTITY) {
            return \Smile\Offer\Api\Data\OfferInterface::SELLER_ID;
        }

        if ($entityType == Product::ENTITY) {
            return \Smile\Offer\Api\Data\OfferInterface::PRODUCT_ID;
        }

        throw new NoSuchEntityException(__("Unable to retrieve fetch strategy for {$entityType}"));
    }

    /**
     * Get an alias for a given couple entity/attributeCode to ensure unicity on SQL query.
     *
     * @param string $entity        The entity
     * @param string $attributeCode The attribute code
     *
     * @return string
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
