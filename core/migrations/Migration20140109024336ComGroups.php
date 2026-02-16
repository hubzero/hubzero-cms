<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for tracking group pages checkouts
**/
class Migration20140109024336ComGroups extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__xgroups_pages_checkout')) {
            $schema->createTable('#__xgroups_pages_checkout')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('pageid')->nullable()
                ->integer('userid')->nullable()
                ->datetime('when')->nullable()
                ->primaryKey('id')
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

        $schema->dropTable('#__xgroups_pages_checkout');
    }
}
