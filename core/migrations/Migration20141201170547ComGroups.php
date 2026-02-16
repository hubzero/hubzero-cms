<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding unique constraint to group membership
 *
*/
class Migration20141201170547ComGroups extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xgroups_members')) {
            $schema->addUniqueIndex('#__xgroups_members', 'idx_gidNumber_uidNumber', ['gidNumber', 'uidNumber']);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropIndex('#__xgroups_members', 'idx_gidNumber_uidNumber');
    }
}
