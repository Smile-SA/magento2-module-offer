<?php

namespace Smile\Offer\Model\ResourceModel;

use Exception;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Smile\Offer\Api\Data\OfferInterface;

/**
 * Offer Resource Model.
 */
class Offer extends AbstractDb
{
    public function __construct(
        Context $context,
        protected EntityManager $entityManager,
        protected MetadataPool $metadataPool,
        ?string $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $metadata = $this->metadataPool->getMetadata(OfferInterface::class);
        $this->_init($metadata->getEntityTable(), $metadata->getIdentifierField());
    }

    /**
     * @inheritdoc
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $offerId = $this->getOfferId($object, $value, $field);
        if ($offerId) {
            $this->entityManager->load($object, $offerId);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function save(AbstractModel $object)
    {
        $this->_beforeSave($object);
        $this->entityManager->save($object);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(OfferInterface::class)->getEntityConnection();
    }

    /**
     * Retrieve Offer Id by field value.
     *
     * @throws Exception
     * @throws LocalizedException
     */
    private function getOfferId(AbstractModel $object, mixed $value, ?string $field = null): int|false
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
}
