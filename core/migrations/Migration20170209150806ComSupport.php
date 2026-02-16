<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for changing #__support_tickets group (cn) field to group ID
 *
*/
class Migration20170209150806ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__support_tickets') && $schema->hasColumn('#__support_tickets', 'group')
            && $schema->tableExists('#__xgroups') && $schema->hasColumn('#__xgroups', 'cn')
        ) {
            // Update group aliases to IDs
            $this->db->getQuery(true)
                ->update('#__support_tickets', 'u')
                ->leftJoin('#__xgroups AS x', 'x.cn', 'u.group')
                ->set(['u.group' => Expression::column('x.gidNumber')])
                ->execute();

            // Remove the old index
            $schema->dropIndex('#__support_tickets', 'idx_group');

            $schema->renameColumn('#__support_tickets', 'group', 'group_id')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();

            // Add the new index
            $schema->addIndex('#__support_tickets', 'idx_group_id', 'group_id');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__support_tickets') && $schema->hasColumn('#__support_tickets', 'group')
            && $schema->tableExists('#__xgroups') && $schema->hasColumn('#__xgroups', 'cn')
        ) {
            $schema->dropIndex('#__support_tickets', 'idx_group_id');

            $schema->renameColumn('#__support_tickets', 'group_id', 'group')
                ->string(250)
                ->execute();

            $this->db->getQuery(true)
                ->update('#__support_tickets AS u')
                ->leftJoin('#__xgroups AS x', 'x.gidNumber', 'u.group')
                ->set(['u.group' => Expression::column('x.cn')])
                ->execute();

            $schema->addIndex('#__support_tickets', 'idx_group', 'group');
        }
    }
}
