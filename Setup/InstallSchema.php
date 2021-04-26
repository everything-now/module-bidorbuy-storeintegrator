<?php

namespace Bidorbuy\StoreIntegrator\Setup;

use Bidorbuy\StoreIntegrator\Classes\BidorbuyConstants;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.Found
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        // phpcs:enable
        $installer = $setup;

        $installer->startSetup();
        $table = $installer->getTable('catalog_eav_attribute');

        $installer->getConnection()->addColumn(
            $table,
            BidorbuyConstants::ATTR_IS_USED_IN_FEED_NAME,
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'unsigned' => true,
                'default' => '0',
                'comment' => BidorbuyConstants::ATTR_IS_USED_IN_FEED_LABEL
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            BidorbuyConstants::ATTR_IS_USED_IN_PRODUCT_TITLE_NAME,
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'unsigned' => true,
                'default' => '0',
                'comment' => BidorbuyConstants::ATTR_IS_USED_IN_PRODUCT_TITLE_LABEL
            ]
        );
        $installer->endSetup();
    }
}
