<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for dropping feature history table
 *
*/
class Migration20140416082740ComFeatures extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $this->deleteComponentEntry('features');

        $schema->dropTable('#__feature_history');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $this->addComponentEntry('features');

        if (!$schema->tableExists('#__feature_history')) {
            $schema->createTable('#__feature_history')
                ->integer('id', ['autoIncrement' => true])
                ->integer('objectid')->nullable()
                ->datetime('featured')->default('0000-00-00 00:00:00')
                ->string('tbl', 255)->nullable()
                ->string('note', 255)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
