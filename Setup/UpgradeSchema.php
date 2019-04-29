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

namespace Smile\Offer\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Update quote and the order tables to append the seller_id and the pickup date field
 *
 * @category Smile
 * @package  Smile\Offer
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->addColumns($setup);
        }
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->addForeignKey($setup);
        }

        $setup->endSetup();
    }

    /**
     * Add 'seller_id' and 'pickup_date' columns on quote and order tables.
     *
     * @param SchemaSetupInterface $setup Module Setup.
     */
    protected function addColumns(SchemaSetupInterface $setup)
    {
        $tables = [$setup->getTable('quote'), $setup->getTable('sales_order')];

        foreach ($tables as $currentTable) {
            $setup->getConnection()->addColumn(
                $currentTable,
                'seller_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Seller ID',
                ]
            );

            $setup->getConnection()->addColumn(
                $currentTable,
                'pickup_date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Pickup date',
                ]
            );
        }
    }

    /**
     * Add foreign key on quote and order tables for column 'seller_id'.
     *
     * @param SchemaSetupInterface $setup Module Setup.
     */
    protected function addForeignKey(SchemaSetupInterface $setup)
    {
        $tables = [$setup->getTable('quote'), $setup->getTable('sales_order')];

        foreach ($tables as $currentTable) {
            $setup->getConnection()->addForeignKey(
                $setup->getFkName($currentTable, 'seller_id', 'smile_seller_entity', 'entity_id'),
                $currentTable,
                'seller_id',
                $setup->getTable('smile_seller_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            );
        }
    }
}
