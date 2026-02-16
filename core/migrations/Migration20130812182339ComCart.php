<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding cart tables
 *
*/
class Migration20130812182339ComCart extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Table structure for table #__cart
        if (!$schema->tableExists('#__cart')) {
            $schema->createTable('#__cart')
                ->integer('id', ['autoIncrement' => true])
                ->integer('uid')->default(0)
                ->integer('itemid')->default(0)
                ->string('type', 20)->nullable()
                ->integer('quantity')->default(0)
                ->datetime('added')->default('0000-00-00 00:00:00')
                ->text('selections')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table #__cart_cart_items
        if (!$schema->tableExists('#__cart_cart_items')) {
            $schema->createTable('#__cart_cart_items')
                ->integer('crtId')
                ->integer('sId')
                ->integer('crtiQty')->nullable()
                ->integer('crtiOldQty')->nullable()
                ->decimal('crtiPrice', 10, 2)->nullable()
                ->decimal('crtiOldPrice', 10, 2)->nullable()
                ->string('crtiName', 255)->nullable()
                ->tinyInteger('crtiAvailable')->default(1)
                ->primaryKey(['crtId', 'sId'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table #__cart_carts
        if (!$schema->tableExists('#__cart_carts')) {
            $schema->createTable('#__cart_carts')
                ->integer('crtId', ['autoIncrement' => true])
                ->datetime('crtCreated')->nullable()
                ->datetime('crtLastUpdated')->nullable()
                ->integer('uidNumber')->nullable()
                ->primaryKey('crtId')
                ->uniqueIndex('uidNumber', 'uidNumber')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table #__cart_coupons
        if (!$schema->tableExists('#__cart_coupons')) {
            $schema->createTable('#__cart_coupons')
                ->integer('crtId')
                ->integer('cnId')
                ->datetime('crtCnAdded')->nullable()
                ->char('crtCnStatus', 15)->nullable()
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table #__cart_memberships
        if (!$schema->tableExists('#__cart_memberships')) {
            $schema->createTable('#__cart_memberships')
                ->integer('crtmId', ['autoIncrement' => true])
                ->integer('pId')->nullable()
                ->integer('crtId')->nullable()
                ->datetime('crtmExpires')->nullable()
                ->primaryKey('crtmId')
                ->uniqueIndex('pId', ['pId', 'crtId'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table #__cart_saved_addresses
        if (!$schema->tableExists('#__cart_saved_addresses')) {
            $schema->createTable('#__cart_saved_addresses')
                ->integer('saId', ['autoIncrement' => true])
                ->integer('uidNumber')
                ->char('saToFirst', 100)
                ->char('saToLast', 100)
                ->char('saAddress', 255)
                ->char('saCity', 25)
                ->char('saState', 2)
                ->char('saZip', 10)
                ->primaryKey('saId')
                ->uniqueIndex('uidNumber', ['uidNumber', 'saToFirst', 'saToLast', 'saAddress', 'saZip'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table #__cart_transaction_info
        if (!$schema->tableExists('#__cart_transaction_info')) {
            $schema->createTable('#__cart_transaction_info')
                ->integer('tId')
                ->char('tiShippingToFirst', 100)->nullable()
                ->char('tiShippingToLast', 100)->nullable()
                ->char('tiShippingAddress', 255)->nullable()
                ->char('tiShippingCity', 25)->nullable()
                ->char('tiShippingState', 2)->nullable()
                ->char('tiShippingZip', 10)->nullable()
                ->decimal('tiTotal', 10, 2)->nullable()
                ->decimal('tiSubtotal', 10, 2)->nullable()
                ->decimal('tiTax', 10, 2)->nullable()
                ->decimal('tiShipping', 10, 2)->nullable()
                ->decimal('tiShippingDiscount', 10, 2)->nullable()
                ->decimal('tiDiscounts', 10, 2)->nullable()
                ->text('tiItems')->nullable()
                ->text('tiPerks')->nullable()
                ->text('tiMeta')->nullable()
                ->char('tiCustomerStatus', 15)->default('unconfirmed')
                ->primaryKey('tId')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table #__cart_transaction_items
        if (!$schema->tableExists('#__cart_transaction_items')) {
            $schema->createTable('#__cart_transaction_items')
                ->integer('tId')
                ->integer('sId')
                ->integer('tiQty')->nullable()
                ->decimal('tiPrice', 10, 2)->nullable()
                ->primaryKey(['tId', 'sId'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table #__cart_transaction_steps
        if (!$schema->tableExists('#__cart_transaction_steps')) {
            $schema->createTable('#__cart_transaction_steps')
                ->integer('tsId', ['autoIncrement' => true])
                ->integer('tId')
                ->char('tsStep', 16)
                ->tinyInteger('tsStatus')->default(0)
                ->primaryKey('tsId')
                ->uniqueIndex('tId', ['tId', 'tsStep'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Table structure for table #__cart_transactions
        if (!$schema->tableExists('#__cart_transactions')) {
            $schema->createTable('#__cart_transactions')
                ->integer('tId', ['autoIncrement' => true])
                ->integer('crtId')->nullable()
                ->datetime('tCreated')->nullable()
                ->datetime('tLastUpdated')->nullable()
                ->char('tStatus', 32)->nullable()
                ->primaryKey('tId')
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

        $schema->dropTable('#__cart');
        $schema->dropTable('#__cart_cart_items');
        $schema->dropTable('#__cart_carts');
        $schema->dropTable('#__cart_coupons');
        $schema->dropTable('#__cart_memberships');
        $schema->dropTable('#__cart_saved_addresses');
        $schema->dropTable('#__cart_transaction_info');
        $schema->dropTable('#__cart_transaction_items');
        $schema->dropTable('#__cart_transaction_steps');
        $schema->dropTable('#__cart_transactions');
    }
}
