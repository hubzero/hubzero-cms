<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding a few need primary keys
 *
*/
class Migration20140623144900Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__xgroups_member_roles')
            && !$schema->hasColumn('#__xgroups_member_roles', 'id')
        ) {
            $schema->addAutoIncrementPrimaryKey('#__xgroups_member_roles', 'id', true);
        }

        if (
            $schema->tableExists('#__xmessage_seen')
            && !$schema->hasColumn('#__xmessage_seen', 'id')
        ) {
            $schema->addAutoIncrementPrimaryKey('#__xmessage_seen', 'id', true);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__xgroups_member_roles')
            && $schema->hasColumn('#__xgroups_member_roles', 'id')
        ) {
            $schema->dropColumn('#__xgroups_member_roles', 'id');
        }

        if (
            $schema->tableExists('#__xmessage_seen')
            && $schema->hasColumn('#__xmessage_seen', 'id')
        ) {
            $schema->dropColumn('#__xmessage_seen', 'id');
        }
    }
}
