<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add missing #__user_roles table
 *
*/
class Migration20160905113000Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__user_roles')) {
            $schema->createTable('#__user_roles')
                ->integer('user_id')
                ->string('role', 20)
                ->integer('group_id')->nullable()
                ->id()
                ->uniqueIndex('uidx_role_user_id_group_id', ['role', 'user_id', 'group_id'])
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
        /* This is a repair migration. A down method would be invalid */
        /* as this change should have happened in Migration20120101000001Core.php */
        /* Repair is only needed on some hubs, perhaps predating that migration.  */
    }
}
