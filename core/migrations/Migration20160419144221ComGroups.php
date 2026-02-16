<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding table for tracking recently visited groups
**/
class Migration20160419144221ComGroups extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__xgroups_recents')) {
            $schema->createTable('#__xgroups_recents')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('user_id')->default(0)
                ->unsignedInteger('group_id')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->primaryKey('id')
                ->index('idx_user_id', 'user_id')
                ->index('idx_group_id', 'group_id')
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

        if ($schema->tableExists('#__xgroups_recents')) {
            $schema->dropTable('#__xgroups_recents');
        }
    }
}
