<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add table for resource related pages
  *
**/
class Migration20160817174610PlgResourcesWindowstools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__resource_pages')) {
            $schema->createTable('#__resource_pages')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('title', 255)->default('')
                ->string('alias', 255)->default('')
                ->text('content')
                ->string('plugin', 255)->default('')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->unsignedInteger('created_by')->default(0)
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->unsignedInteger('modified_by')->default(0)
                ->tinyInteger('state')->default(0)
                ->tinyInteger('access')->default(0)
                ->integer('ordering')->default(0)
                ->integer('resource_id')->default(0)
                ->primaryKey('id')
                ->index('idx_plugin', 'plugin')
                ->index('idx_resource_id', 'resource_id')
                ->index('idx_state', 'state')
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

        $schema->dropTable('#__resource_pages');
    }
}
