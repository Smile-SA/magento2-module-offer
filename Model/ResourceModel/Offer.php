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
 * Offer Resource Model
 *
 * @category Smile
 * @package  Smile\Offer
 * @author   Aurelien Foucret <aurelien.foucret@smile.fr>
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

    /**
     * Offer constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context        Application Context
     * @param \Magento\Framework\EntityManager\EntityManager    $entityManager  Entity Manager
     * @param \Magento\Framework\EntityManager\MetadataPool     $metadataPool   Metadata Pool
     * @param null                                              $connectionName Connection name
     */
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

    /**
     * Load an Offer by a given field's value.
     *
     * @param \Magento\Framework\Model\AbstractModel $object The offer
     * @param mixed                                  $value  The value
     * @param null                                   $field  The field, if any
     *
     * @return $this
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
     * {@inheritDoc}
     */
    public function save(AbstractModel $object)
    {
        $this->_beforeSave($object);
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
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName) Method is inherited.
     */
    protected function _construct()
    {
        $metadata = $this->metadataPool->getMetadata(OfferInterface::class);
        $this->_init($metadata->getEntityTable(), $metadata->getIdentifierField());
    }

    /**
     * Prepare offer's active "from" and "to" dates
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName) Method is inherited.
     *
     * @param \Magento\Framework\Model\AbstractModel $object The offer being saved
     *
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $this->resolveDate($object, OfferInterface::START_DATE);
        $this->resolveDate($object, OfferInterface::END_DATE);

        parent::_beforeSave($object);

        return $this;
    }

    /**
     * Retrieve Offer Id by field value
     *
     * @param \Magento\Framework\Model\AbstractModel $object The Offer
     * @param mixed                                  $value  The value
     * @param null                                   $field  The field
     *
     * @return int|false
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
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

    /**
     * Resolve date field of the object.
     *
     * @param \Magento\Framework\Model\AbstractModel $object         The offer
     * @param string                                 $dateIdentifier The date field identifier
     *
     * @return void
     */
    private function resolveDate(\Magento\Framework\Model\AbstractModel $object, $dateIdentifier)
    {
        $date = $object->getData($dateIdentifier);

        if ($date instanceof \DateTime) {
            $object->setData($dateIdentifier, $date->format('Y-m-d H:i:s'));
        } elseif (!is_string($date) || empty($date)) {
            $object->setData($dateIdentifier, null);
        }
    }
}
