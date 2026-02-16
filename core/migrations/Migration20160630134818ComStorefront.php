<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add table for tracking product/access group relations
 *
*/
class Migration20160630134818ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__storefront_product_access_groups')) {
            $schema->createTable('#__storefront_product_access_groups')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('pId')->default(0)
                ->integer('agId')->default(0)
                ->primaryKey('id')
                ->index('idx_pId', 'pId')
                ->index('idx_agId', 'agId')
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

        $schema->dropTable('#__storefront_product_access_groups');
    }
}
