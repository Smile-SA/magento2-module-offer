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

namespace Smile\Offer\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Smile\Offer\Api\Data\OfferInterface;

/**
 *
 * @todo : Store id ????
 *
 */
class Offer extends AbstractDb
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    public function __construct(
        Context $context,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        parent::__construct($context, $connectionName);
    }

    private function getOfferId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(OfferInterface::class);

        if ($field === null) {
            $field = OfferInterface::OFFER_ID;
        }

        $entityId = $value;

        if ($field != $entityMetadata->getIdentifierField()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);

            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }

        return $entityId;
    }


    public function load(AbstractModel $object, $value, $field = null)
    {
        $offerId = $this->getOfferId($object, $value, $field);
        if ($offerId) {
            $this->entityManager->load($object, $offerId);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(OfferInterface::class)->getEntityConnection();
    }

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $metadata = $this->metadataPool->getMetadata(OfferInterface::class);
        $this->_init($metadata->getEntityTable(), $metadata->getIdentifierField());
    }
}