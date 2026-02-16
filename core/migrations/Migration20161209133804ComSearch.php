<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for replacing the indexqueue table
 *
*/
class Migration20161209133804ComSearch extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Remove the old table if it exists
        if ($schema->tableExists('#__search_indexqueue')) {
            $schema->dropTable('#__search_indexqueue');
        }

        if (!$schema->tableExists('#__search_queue')) {
            // Build the new table
            $schema->createTable('#__search_queue')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('type', 20)->default('')
                ->integer('type_id')
                ->integer('status')->default(0)
                ->string('action', 20)
                ->integer('created_by')->nullable()
                ->timestamp('created')->nullable()
                ->timestamp('modified')->nullable()
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

        // Remove the newer table if it exists
        if ($schema->tableExists('#__search_queue')) {
            $schema->dropTable('#__search_queue');
        }

        if (!$schema->tableExists('#__search_indexqueue')) {
            // Build the older table
            $schema->createTable('#__search_indexqueue')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('hubtype', 12)->default('')
                ->string('action', 12)->nullable()
                ->integer('start')->default(0)
                ->tinyInteger('lock')->default(0)
                ->tinyInteger('complete')->default(0)
                ->timestamp('created')->nullable()
                ->integer('created_by')->nullable()
                ->timestamp('modified')->nullable()
                ->primaryKey('id')
                ->engine('InnoDB')
                ->charset('utf8')
                ->execute();
        }
    }
}
