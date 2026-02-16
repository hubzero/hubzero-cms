<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting up databases projects plugin
 *
*/
class Migration20130813210535PlgProjectsDatabases extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__project_databases')) {
            $schema->createTable('#__project_databases')
                ->integer('id', ['autoIncrement' => true])
                ->integer('project')
                ->string('database_name', 64)
                ->string('title', 127)->default('')
                ->string('source_file', 127)
                ->string('source_dir', 127)
                ->string('source_revision', 56)
                ->text('description')->nullable()
                ->text('data_definition')->nullable()
                ->integer('revision')->nullable()
                ->datetime('created')->nullable()
                ->integer('created_by')->nullable()
                ->datetime('updated')->nullable()
                ->integer('updated_by')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__project_database_versions')) {
            $schema->createTable('#__project_database_versions')
                ->integer('id', ['autoIncrement' => true])
                ->string('database_name', 64)
                ->integer('version')->default(1)
                ->text('data_definition')->nullable()
                ->primaryKey(['id', 'database_name', 'version'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        $this->addPluginEntry('projects', 'databases', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__project_databases');
        $schema->dropTable('#__project_database_versions');

        $this->deletePluginEntry('projects', 'databases');
    }
}
