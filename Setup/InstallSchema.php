<?php

namespace Smile\Offer\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Smile\Offer\Api\Data\OfferInterface;
use Zend_Db_Exception;

/**
 * Offer schema install class.
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @inheritdoc
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->createOfferTable($setup);
        $setup->endSetup();
    }

    /**
     * Create the offer table.
     *
     * @throws Zend_Db_Exception
     */
    private function createOfferTable(SchemaSetupInterface $setup): void
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('smile_offer'))
            ->addColumn(
                OfferInterface::OFFER_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                OfferInterface::PRODUCT_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
                'Product ID'
            )
            ->addColumn(
                OfferInterface::SELLER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
                'Seller ID'
            )
            ->addColumn(
                OfferInterface::IS_AVAILABLE,
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Available'
            )
            ->addColumn(
                OfferInterface::PRICE,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Offer price'
            )
            ->addColumn(
                OfferInterface::SPECIAL_PRICE,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Offer price'
            )
            ->addForeignKey(
                $setup->getFkName('smile_offer', OfferInterface::PRODUCT_ID, 'catalog_product_entity', 'entity_id'),
                OfferInterface::PRODUCT_ID,
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName('smile_offer', OfferInterface::SELLER_ID, 'smile_seller_entity', 'entity_id'),
                OfferInterface::SELLER_ID,
                $setup->getTable('smile_seller_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            );

        $setup->getConnection()->createTable($table);
    }
}
