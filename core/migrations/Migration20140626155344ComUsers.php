<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating table #__users_merge_log
**/
class Migration20140626155344ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__users_merge_log')) {
            $schema->createTable('#__users_merge_log')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('source', 150)->default('')
                ->string('destination', 150)->default('')
                ->string('table', 255)->default('')
                ->string('column', 255)->default('')
                ->string('table_pk', 255)->nullable()
                ->integer('table_id')->nullable()
                ->datetime('logged')
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

        $schema->dropTable('#__users_merge_log');
    }
}
