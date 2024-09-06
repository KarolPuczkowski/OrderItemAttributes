<?php
namespace SkiDev\OrderItemAttributes\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Add 'transfer_to_order_item' column to 'catalog_eav_attribute' table
        $installer->getConnection()->addColumn(
            $installer->getTable('catalog_eav_attribute'),
            'transfer_to_order_item',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0, // Integer instead of string
                'comment' => 'Transfer to Order Item'
            ]
        );

        // Add 'product_attributes' column to 'sales_order_item' table
        if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales_order_item'), 'product_attributes')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_item'),
                'product_attributes',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Product Attributes in JSON format'
                ]
            );
        }

        $installer->endSetup();
    }
}
