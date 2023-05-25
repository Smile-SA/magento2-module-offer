<?php

namespace Smile\Offer\Model\ResourceModel\Offer;

use Exception;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;
use Smile\Offer\Api\Data\OfferInterface;
use Smile\Offer\Model\Offer;
use Smile\Offer\Model\ResourceModel\Offer as OfferResource;
use Zend_Db_Expr;

/**
 * Offer Collection.
 */
class Collection extends AbstractCollection
{
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        protected MetadataPool $metadataPool,
        ?AdapterInterface $connection = null,
        ?AbstractDb $resource = null,
        protected ?string $sellerEntity = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        if ($this->sellerEntity !== null) {
            $this->addSellerTypeFilter($this->sellerEntity);
        }
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Offer::class, OfferResource::class);
    }

    /**
     * Filtering the collection on a given seller type
     *
     * @throws Exception
     */
    public function addSellerTypeFilter(string $sellerEntity): void
    {
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
                    new Zend_Db_Expr("{$sellerTable}.{$sellerPkName} = main_table." . OfferInterface::SELLER_ID),
                    new Zend_Db_Expr("{$sellerTable}.attribute_set_id = " . (int) $attributeSetId),
                ])
            );
        }
    }

    /**
     * Filter offers by product Id.
     */
    public function addProductFilter(int $productId): self
    {
        $this->addFieldToFilter(OfferInterface::PRODUCT_ID, $productId);

        return $this;
    }

    /**
     * Filter offers by seller Id.
     */
    public function addSellerFilter(int $sellerId): self
    {
        $this->addFieldToFilter(OfferInterface::SELLER_ID, $sellerId);

        return $this;
    }
}
