<?php
/**
 * DISCLAIMER
 *
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

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Psr\Log\LoggerInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\EntityFactory;

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
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var EntityFactory
     */
    private $eavEntityFactory;

    /**
     * Collection constructor.
     *
     * @param EntityFactoryInterface $entityFactory    Entity Factory
     * @param LoggerInterface        $logger           Logger Interface
     * @param FetchStrategyInterface $fetchStrategy    Fetch Strategy
     * @param ManagerInterface       $eventManager     Event Manager
     * @param MetadataPool           $metadataPool     Metadata Pool
     * @param Config                 $eavConfig        EAV Configuration
     * @param EntityFactory          $eavEntityFactory EAV ENtity Factory
     * @param AdapterInterface|null  $connection       Database Connection
     * @param AbstractDb|null        $resource         Resource Model
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        MetadataPool $metadataPool,
        Config $eavConfig,
        EntityFactory $eavEntityFactory,
        $connection = null,
        $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $metadataPool, $connection, $resource);

        $this->eavConfig  = $eavConfig;
        $this->eavEntityFactory = $eavEntityFactory;
    }

    /**
     * Process left join on catalog/product entity table to retrieve the SKU Field.
     * Also bind "sku" column to a proper alias if given
     *
     * @param string $alias The alias for SKU field, if any.
     *
     * @return $this;
     */
    public function addProductSkuToSelect($alias = null)
    {
        $columns = (null === $alias) ? ["sku"] : [$alias => "sku"];

        $this->getSelect()->joinLeft(
            ["cpe" => $this->getTable("catalog_product_entity")],
            new \Zend_Db_Expr("cpe.entity_id = main_table.product_id"),
            $columns
        );

        return $this;
    }

    /**
     * Add Seller Attribute to current collection.
     *
     * @param string $attributeCode The Attribute code
     * @param string $alias         The field alias, if any
     * @param int    $storeId       The current store scope for seller attributes retrieval
     *
     * @return $this
     */
    public function addSellerAttributeToSelect($attributeCode, $alias = null, $storeId = null)
    {
        $columns = (null === $alias) ? [$attributeCode => 'value'] : [$alias => 'value'];

        $attribute     = $this->eavConfig->getAttribute(\Smile\Seller\Api\Data\SellerInterface::ENTITY, $attributeCode);
        $entity        = $this->eavEntityFactory->create()->setType(\Smile\Seller\Api\Data\SellerInterface::ENTITY);
        $entityIdField = $entity->getEntityIdField();
        $primaryKey    = \Smile\Offer\Api\Data\OfferInterface::SELLER_ID;

        if ($attribute && !$attribute->isStatic()) {
            $backendTable = $attribute->getBackendTable();

            $this->getSelect()->joinLeft(
                ["t_d" => $this->getTable($backendTable)],
                new \Zend_Db_Expr("t_d.{$entityIdField} = main_table.{$primaryKey}"),
                $columns
            );

            $storeCondition = \Magento\Store\Model\Store::DEFAULT_STORE_ID;

            if ($storeId) {
                $joinCondition = [
                    't_s.attribute_id = t_d.attribute_id',
                    "t_s.{$entityIdField} = t_d.{$entityIdField}",
                    $this->getConnection()->quoteInto('t_s.store_id = ?', $storeId),
                ];

                $this->getSelect()->joinLeft(['t_s' => $backendTable], implode(' AND ', $joinCondition), []);
                $storeCondition = $this->getConnection()->getIfNullSql('t_s.store_id', \Magento\Store\Model\Store::DEFAULT_STORE_ID);
            }

            $this->getSelect()->where('t_d.store_id = ?', $storeCondition);
        }
    }
}
