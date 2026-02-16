<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for resource import tables
 *
*/
class Migration20140324161600ComResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // imports table
        if (!$schema->tableExists('#__resource_imports')) {
            $schema->createTable('#__resource_imports')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('name', 150)->nullable()
                ->text('notes')->nullable()
                ->string('file', 255)->default('')
                ->integer('count')->nullable()
                ->integer('created_by')->nullable()
                ->datetime('created_at')->nullable()
                ->integer('state')->default(1)
                ->string('mode', 10)->default('UPDATE')
                ->text('params')->nullable()
                ->text('hooks')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // runs table
        if (!$schema->tableExists('#__resource_import_runs')) {
            $schema->createTable('#__resource_import_runs')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('import_id')->nullable()
                ->integer('processed')->nullable()
                ->integer('count')->nullable()
                ->integer('ran_by')->nullable()
                ->datetime('ran_at')->nullable()
                ->integer('dry_run')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // hooks table
        if (!$schema->tableExists('#__resource_import_hooks')) {
            $schema->createTable('#__resource_import_hooks')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('type', 25)->nullable()
                ->string('name', 255)->nullable()
                ->text('notes')->nullable()
                ->string('file', 100)->nullable()
                ->integer('state')->default(1)
                ->datetime('created')->nullable()
                ->integer('created_by')->nullable()
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

        $schema->dropTable('#__resource_imports');
        $schema->dropTable('#__resource_import_runs');
        $schema->dropTable('#__resource_import_hooks');
    }
}
