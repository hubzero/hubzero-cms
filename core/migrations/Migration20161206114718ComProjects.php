<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add `sync_group` column to projects table
 *
*/
class Migration20161206114718ComProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__projects')) {
            return;
        }

        if (!$schema->hasColumn('#__projects', 'sync_group')) {
            $schema->table('#__projects')->alter()
                ->addColumn('sync_group')->tinyInteger(2)->notNull()->default('1')
                ->addIndex('idx_sync_group', 'sync_group')
                ->execute();
        } else {
            $schema->addIndex('#__projects', 'idx_sync_group', 'sync_group');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__projects')) {
            return;
        }

        $schema->dropIndex('#__projects', 'idx_sync_group');

        if ($schema->hasColumn('#__projects', 'sync_group')) {
            $schema->dropColumn('#__projects', 'sync_group');
        }
    }
}
