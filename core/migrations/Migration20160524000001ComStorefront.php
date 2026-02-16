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
*/
class Migration20160524000001ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__storefront_serials')) {
            $schema->createTable('#__storefront_serials')
                ->unsignedInteger('srId', ['autoIncrement' => true])
                ->string('srNumber', 32)->nullable()
                ->integer('srSId')->nullable()
                ->string('srStatus', 10)->nullable()
                ->primaryKey('srId')
                ->uniqueIndex('unique keys for a SKU', ['srNumber', 'srSId'])
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

        $schema->dropTable('#__storefront_serials');
    }
}
