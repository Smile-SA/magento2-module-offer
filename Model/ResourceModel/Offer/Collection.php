<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Smile\Offer\Model\ResourceModel\Offer;


use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Smile\Seller\Api\Data\SellerInterface;

class Collection extends AbstractCollection
{
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool $metadataPool
     */
    private $metadataPool;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->metadataPool = $metadataPool;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Smile\Offer\Model\Offer', 'Smile\Offer\Model\ResourceModel\Offer');
    }

    public function addSellerTypeFilter($sellerType)
    {
        $sellerMetadata = $this->metadataPool->getMetadata(SellerInterface::ENTITY);
        var_dump($sellerMetadata);
    }
}
