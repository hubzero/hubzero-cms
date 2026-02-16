<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for logging media tracking detailed usage
 *
*/
class Migration20130826150814ComResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__media_tracking_detailed')) {
            $schema->createTable('#__media_tracking_detailed')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('user_id')->nullable()
                ->string('session_id', 200)->nullable()
                ->string('ip_address', 100)->nullable()
                ->integer('object_id')->nullable()
                ->string('object_type', 100)->nullable()
                ->integer('object_duration')->nullable()
                ->integer('current_position')->nullable()
                ->integer('farthest_position')->nullable()
                ->datetime('current_position_timestamp')->nullable()
                ->datetime('farthest_position_timestamp')->nullable()
                ->integer('completed')->nullable()
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

        $schema->dropTable('#__media_tracking_detailed');
    }
}
