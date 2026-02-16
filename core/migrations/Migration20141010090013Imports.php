<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding content import tables
  *
**/
class Migration20141010090013Imports extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__imports')) {
            $schema->createTable('#__imports')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('type', 150)
                ->string('name', 150)->nullable()
                ->text('notes')->nullable()
                ->string('file', 255)->default('')
                ->unsignedInteger('count')->default(0)
                ->unsignedInteger('created_by')->default(0)
                ->datetime('created_at')->nullable()
                ->unsignedInteger('state')->default(1)
                ->string('mode', 10)->default('UPDATE')
                ->text('params')->nullable()
                ->text('hooks')->nullable()
                ->text('fields')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__import_hooks')) {
            $schema->createTable('#__import_hooks')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('event', 25)->nullable()
                ->string('type', 150)
                ->string('name', 255)->nullable()
                ->text('notes')->nullable()
                ->string('file', 100)->nullable()
                ->integer('state')->default(1)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->unsignedInteger('created_by')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__import_runs')) {
            $schema->createTable('#__import_runs')
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
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__imports')) {
            $schema->dropTable('#__imports');
        }

        if ($schema->tableExists('#__import_hooks')) {
            $schema->dropTable('#__import_hooks');
        }

        if ($schema->tableExists('#__import_runs')) {
            $schema->dropTable('#__import_runs');
        }
    }
}
