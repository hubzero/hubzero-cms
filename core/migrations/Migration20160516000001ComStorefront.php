<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add SKU restrictions by users
  *
**/
class Migration20160516000001ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__storefront_skus')
            && !$schema->hasColumn('#__storefront_skus', 'sRestricted')
        ) {
            $schema->addColumn('#__storefront_skus', 'sRestricted')->tinyInteger()->default(0)->execute();
        }
        if (!$schema->tableExists('#__storefront_permissions')) {
            $schema->createTable('#__storefront_permissions')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('scope', 15)->nullable()
                ->integer('scope_id')->nullable()
                ->integer('uId')->nullable()
                ->primaryKey('id')
                ->uniqueIndex('single entry per item', ['scope', 'scope_id', 'uId'])
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

        if (
            $schema->tableExists('#__storefront_skus')
            && $schema->hasColumn('#__storefront_skus', 'sRestricted')
        ) {
            $schema->dropColumn('#__storefront_skus', 'sRestricted');
        }
        $schema->dropTable('#__storefront_permissions');
    }
}
