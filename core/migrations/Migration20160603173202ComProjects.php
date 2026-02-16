<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding project description tables
**/
class Migration20160603173202ComProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Create the project_descriptions table
        if (!$schema->tableExists('#__project_descriptions')) {
            $schema->createTable('#__project_descriptions')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('project_id')
                ->string('description_key', 100)->default('')
                ->text('description_value')
                ->integer('ordering')->nullable()
                ->primaryKey('id')
                ->index('idx_user_id', 'project_id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Create the project_description_options table
        if (!$schema->tableExists('#__project_description_options')) {
            $schema->createTable('#__project_description_options')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('field_id')->default(0)
                ->string('value', 255)->default('')
                ->string('label', 255)->default('')
                ->integer('ordering')->default(0)
                ->tinyInteger('checked')->default(0)
                ->tinyText('dependents')->nullable()
                ->primaryKey('id')
                ->index('idx_field_id', 'field_id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Create the project_description_fields table
        if (!$schema->tableExists('#__project_description_fields')) {
            $schema->createTable('#__project_description_fields')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('type', 255)
                ->string('name', 255)->default('')
                ->string('label', 255)->default('')
                ->string('placeholder', 255)->nullable()
                ->mediumText('description')->nullable()
                ->integer('ordering')->default(0)
                ->integer('access')->default(0)
                ->tinyInteger('option_other')->default(0)
                ->tinyInteger('option_blank')->default(0)
                ->tinyInteger('action_create')->default(1)
                ->tinyInteger('action_update')->default(1)
                ->tinyInteger('action_edit')->default(1)
                ->primaryKey('id')
                ->index('idx_type', 'type')
                ->index('idx_access', 'access')
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

        $schema->dropTable('#__project_descriptions');
        $schema->dropTable('#__project_description_options');
        $schema->dropTable('#__project_description_fields');
    }
}
