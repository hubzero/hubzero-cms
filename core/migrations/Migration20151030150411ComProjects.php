<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding projects tables to support filesystem connections
**/
class Migration20151030150411ComProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Add connections table
        if (!$schema->tableExists('#__projects_connections')) {
            $schema->createTable('#__projects_connections')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('name', 255)->nullable()
                ->integer('project_id')
                ->integer('provider_id')
                ->text('params')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Add providers table
        if (!$schema->tableExists('#__projects_connection_providers')) {
            $schema->createTable('#__projects_connection_providers')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('alias', 255)->default('')
                ->string('name', 255)->default('')
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

        $schema->dropTable('#__projects_connections');
        $schema->dropTable('#__projects_connection_providers');
    }
}
