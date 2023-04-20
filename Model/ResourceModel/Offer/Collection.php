<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\Offer
 * @author    Aurelien Foucret <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\Offer\Model\ResourceModel\Offer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;
use Smile\Offer\Api\Data\OfferInterface;

/**
 * Offer Collection
 *
 * @category Smile
 * @package  Smile\Offer
 * @author   Aurelien Foucret <aurelien.foucret@smile.fr>
 */
class Collection extends AbstractCollection
{
    /**
     * @var MetadataPool $metadataPool
     */
    protected MetadataPool $metadataPool;

    /**
     * @var ?string $sellerEntity
     */
    protected ?string $sellerEntity = null;

    /**
     * Collection constructor.
     *
     * @param EntityFactoryInterface $entityFactory Entity Factory
     * @param LoggerInterface        $logger        Logger Interface
     * @param FetchStrategyInterface $fetchStrategy Fetch Strategy
     * @param ManagerInterface       $eventManager  Event Manager
     * @param MetadataPool           $metadataPool  Metadata Pool
     * @param ?AdapterInterface      $connection    Database Connection
     * @param ?AbstractDb            $resource      Resource Model
     * @param ?string                $sellerEntity  The seller type to filter on. If Any.
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        MetadataPool $metadataPool,
        AdapterInterface $connection = null,
        AbstractDb $resource = null,
        ?string $sellerEntity = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->metadataPool  = $metadataPool;
        $this->sellerEntity = $sellerEntity;
    }

    /**
     * Filtering the collection on a given seller type
     *
     * @param string $sellerEntity The seller entity class
     *
     * @throws \Exception
     */
    public function addSellerTypeFilter(string $sellerEntity): void
    {
        if (null !== $sellerEntity) {
            $sellerMetadata = $this->metadataPool->getMetadata($sellerEntity);
            $sellerInstance = $this->_entityFactory->create($sellerEntity);

            $sellerResource = $sellerInstance->getResource();
            $attributeSetId = $sellerResource->getAttributeSetIdByName($sellerInstance->getAttributeSetName());

            $sellerTable    = $sellerMetadata->getEntityTable();
            $sellerPkName   = $sellerMetadata->getIdentifierField();

            if (null !== $attributeSetId) {
                $this->getSelect()->joinLeft(
                    $this->getTable($sellerTable),
                    implode(' AND ', [
                        new \Zend_Db_Expr("{$sellerTable}.{$sellerPkName} = main_table." . OfferInterface::SELLER_ID),
                        new \Zend_Db_Expr("{$sellerTable}.attribute_set_id = ". (int) $attributeSetId),
                    ])
                );
            }
        }
    }

    /**
     * Filter offers by product Id
     *
     * @param int $productId The product Id
     *
     * @return $this
     */
    public function addProductFilter(int $productId): self
    {
        $this->addFieldToFilter(OfferInterface::PRODUCT_ID, $productId);

        return $this;
    }

    /**
     * Filter offers by seller Id
     *
     * @param int $sellerId The seller Id
     *
     * @return $this
     */
    public function addSellerFilter(int $sellerId): self
    {
        $this->addFieldToFilter(OfferInterface::SELLER_ID, $sellerId);

        return $this;
    }

    /**
     * Define resource model
     * @SuppressWarnings(PHPMD.CamelCaseMethodName) Method is inherited.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('Smile\Offer\Model\Offer', 'Smile\Offer\Model\ResourceModel\Offer');
        if ($this->sellerEntity !== null) {
            $this->addSellerTypeFilter($this->sellerEntity);
        }
    }
}
