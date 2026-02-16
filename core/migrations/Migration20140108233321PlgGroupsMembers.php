<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for group roles permission
  *
**/
class Migration20140108233321PlgGroupsMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // change role to name
        if (!$schema->hasColumn('#__xgroups_roles', 'name')) {
            $schema->renameColumn('#__xgroups_roles', 'role', 'name')
                ->string(150)
                ->execute();
        }

        // add permissions field
        if (!$schema->hasColumn('#__xgroups_roles', 'permissions')) {
            $schema->addColumn('#__xgroups_roles', 'permissions')
                ->text()
                ->execute();
        }

        // add role to roleid
        if (!$schema->hasColumn('#__xgroups_member_roles', 'roleid')) {
            $schema->renameColumn('#__xgroups_member_roles', 'role', 'roleid')
                ->integer(11)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // change role to name
        if ($schema->hasColumn('#__xgroups_roles', 'name')) {
            $schema->renameColumn('#__xgroups_roles', 'name', 'role')
                ->string(150)
                ->execute();
        }

        // add permissions field
        if ($schema->hasColumn('#__xgroups_roles', 'permissions')) {
            $schema->dropColumn('#__xgroups_roles', 'permissions');
        }

        // add role to roleid
        if ($schema->hasColumn('#__xgroups_member_roles', 'roleid')) {
            $schema->renameColumn('#__xgroups_member_roles', 'roleid', 'role')
                ->integer(11)
                ->execute();
        }
    }
}
