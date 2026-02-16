<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding storefront tables
 *
*/
class Migration20130812201731ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Table structure for table `#__storefront_collections`
        if (!$schema->tableExists('#__storefront_collections')) {
            $schema->createTable('#__storefront_collections')
                ->char('cId', 50)
                ->string('cName', 64)->nullable()
                ->integer('cParent')->nullable()
                ->tinyInteger('cActive')->nullable()
                ->char('cType', 10)->nullable()
                ->primaryKey('cId')
                ->index('cActive', 'cActive')
                ->index('cParent', 'cParent')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table `#__storefront_coupon_actions`
        if (!$schema->tableExists('#__storefront_coupon_actions')) {
            $schema->createTable('#__storefront_coupon_actions')
                ->integer('cnId')
                ->char('cnaAction', 25)->nullable()
                ->char('cnaVal', 255)->nullable()
                ->uniqueIndex('cnId', ['cnId', 'cnaAction'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table `#__storefront_coupon_conditions`
        if (!$schema->tableExists('#__storefront_coupon_conditions')) {
            $schema->createTable('#__storefront_coupon_conditions')
                ->integer('cnId')
                ->char('cncRule', 100)->nullable()
                ->char('cncVal', 255)->nullable()
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table `#__storefront_coupon_objects`
        if (!$schema->tableExists('#__storefront_coupon_objects')) {
            $schema->createTable('#__storefront_coupon_objects')
                ->integer('cnId')
                ->integer('cnoObjectId')->nullable()
                ->integer('cnoObjectsLimit')->default(0)
                ->uniqueIndex('cnId', ['cnId', 'cnoObjectId'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table `#__storefront_coupons`
        if (!$schema->tableExists('#__storefront_coupons')) {
            $schema->createTable('#__storefront_coupons')
                ->integer('cnId', ['autoIncrement' => true])
                ->char('cnCode', 25)->nullable()
                ->char('cnDescription', 255)->nullable()
                ->date('cnExpires')->nullable()
                ->unsignedInteger('cnUseLimit')->nullable()
                ->char('cnObject', 15)
                ->tinyInteger('cnActive')->default(1)
                ->primaryKey('cnId')
                ->uniqueIndex('Unique code', 'cnCode')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table `#__storefront_option_groups`
        if (!$schema->tableExists('#__storefront_option_groups')) {
            $schema->createTable('#__storefront_option_groups')
                ->integer('ogId', ['autoIncrement' => true])
                ->char('ogName', 16)->nullable()
                ->primaryKey('ogId')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table `#__storefront_options`
        if (!$schema->tableExists('#__storefront_options')) {
            $schema->createTable('#__storefront_options')
                ->integer('oId', ['autoIncrement' => true])
                ->integer('ogId')->nullable()
                ->char('oName', 255)->nullable()
                ->primaryKey('oId')
                ->uniqueIndex('ogId', ['ogId', 'oName'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table `#__storefront_product_collections`
        if (!$schema->tableExists('#__storefront_product_collections')) {
            $schema->createTable('#__storefront_product_collections')
                ->integer('cllId', ['autoIncrement' => true])
                ->integer('pId')
                ->char('cId', 50)
                ->primaryKey(['cllId', 'pId', 'cId'])
                ->uniqueIndex('pId', ['pId', 'cId'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table `#__storefront_product_option_groups`
        if (!$schema->tableExists('#__storefront_product_option_groups')) {
            $schema->createTable('#__storefront_product_option_groups')
                ->integer('pId')
                ->integer('ogId')
                ->primaryKey(['pId', 'ogId'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table `#__storefront_product_types`
        if (!$schema->tableExists('#__storefront_product_types')) {
            $schema->createTable('#__storefront_product_types')
                ->integer('ptId', ['autoIncrement' => true])
                ->char('ptName', 128)->nullable()
                ->char('ptModel', 25)->default('normal')
                ->primaryKey('ptId')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table `#__storefront_products`
        if (!$schema->tableExists('#__storefront_products')) {
            $schema->createTable('#__storefront_products')
                ->integer('pId', ['autoIncrement' => true])
                ->integer('ptId')
                ->char('pName', 128)->nullable()
                ->tinyText('pTagline')->nullable()
                ->text('pDescription')->nullable()
                ->text('pFeatures')->nullable()
                ->tinyInteger('pActive')->default(1)
                ->primaryKey('pId')
                ->index('pActive', 'pActive')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table `#__storefront_sku_meta`
        if (!$schema->tableExists('#__storefront_sku_meta')) {
            $schema->createTable('#__storefront_sku_meta')
                ->integer('smId', ['autoIncrement' => true])
                ->integer('sId')
                ->string('smKey', 100)->nullable()
                ->string('smValue', 100)->nullable()
                ->primaryKey('smId')
                ->uniqueIndex('sId', ['sId', 'smKey'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table `#__storefront_sku_options`
        if (!$schema->tableExists('#__storefront_sku_options')) {
            $schema->createTable('#__storefront_sku_options')
                ->integer('sId')
                ->integer('oId')
                ->primaryKey(['sId', 'oId'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table `#__storefront_skus`
        if (!$schema->tableExists('#__storefront_skus')) {
            $schema->createTable('#__storefront_skus')
                ->integer('sId', ['autoIncrement' => true])
                ->integer('pId')->nullable()
                ->char('sSku', 16)->nullable()
                ->decimal('sWeight', 10, 2)->nullable()
                ->decimal('sPrice', 10, 2)->nullable()
                ->text('sDescriprtion')->nullable()
                ->text('sFeatures')->nullable()
                ->tinyInteger('sTrackInventory')->default(0)
                ->integer('sInventory')->default(0)
                ->tinyInteger('sEnumerable')->default(1)
                ->tinyInteger('sAllowMultiple')->default(1)
                ->tinyInteger('sActive')->default(1)
                ->primaryKey('sId')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__storefront_collections');
        $schema->dropTable('#__storefront_coupon_actions');
        $schema->dropTable('#__storefront_coupon_conditions');
        $schema->dropTable('#__storefront_coupon_objects');
        $schema->dropTable('#__storefront_coupons');
        $schema->dropTable('#__storefront_option_groups');
        $schema->dropTable('#__storefront_options');
        $schema->dropTable('#__storefront_product_collections');
        $schema->dropTable('#__storefront_product_option_groups');
        $schema->dropTable('#__storefront_product_types');
        $schema->dropTable('#__storefront_products');
        $schema->dropTable('#__storefront_sku_meta');
        $schema->dropTable('#__storefront_sku_options');
        $schema->dropTable('#__storefront_skus');
    }
}
