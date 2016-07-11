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

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;
use Smile\Seller\Api\Data\SellerInterface;

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
    private $metadataPool;

    /**
     * Collection constructor.
     *
     * @param EntityFactoryInterface $entityFactory Entity Factory
     * @param LoggerInterface        $logger        Logger Interface
     * @param FetchStrategyInterface $fetchStrategy Fetch Strategy
     * @param ManagerInterface       $eventManager  Event Manager
     * @param MetadataPool           $metadataPool  Metadata Pool
     * @param AdapterInterface|null  $connection    Database Connection
     * @param AbstractDb|null        $resource      Resource Model
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        MetadataPool $metadataPool,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->metadataPool = $metadataPool;
    }

    /**
     * Filtering the collection on a given seller type
     *
     * @param string $sellerType The seller Type
     *
     * @throws \Exception
     */
    public function addSellerTypeFilter($sellerType)
    {
        $sellerMetadata = $this->metadataPool->getMetadata(SellerInterface::ENTITY);
    }

    /**
     * Define resource model
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName) Method is inherited.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Smile\Offer\Model\Offer', 'Smile\Offer\Model\ResourceModel\Offer');
    }

}
