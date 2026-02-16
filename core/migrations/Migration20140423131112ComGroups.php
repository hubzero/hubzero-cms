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
 * Migration script for deleting duplicate groups and enforcing with index
 *
*/
class Migration20140423131112ComGroups extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // select all groups with duplicate cname's
        $subquery = $this->db->getQuery(true)
            ->select('cn')
            ->from('#__xgroups')
            ->group('cn')
            ->having(Expression::count(), '>', 1);

        $duplicateGroups = $this->db->getQuery(true)
            ->select(['gidNumber', 'cn', 'description'])
            ->from('#__xgroups')
            ->whereIn('cn', $subquery)
            ->order('gidNumber', 'asc')
            ->loadObjectList();

        // var to hold original groups
        $original = array();

        // loop through each group
        foreach ($duplicateGroups as $duplicateGroup) {
            // make sure to keep the original group
            if (!in_array($duplicateGroup->cn, $original)) {
                $original[] = $duplicateGroup->cn;
                continue;
            }

            // delete group
            // also deletes membership related stuff
            $hzGroup = \Hubzero\User\Group::getInstance($duplicateGroup->gidNumber);
            $hzGroup->delete();
        }

        // // Add unique index to cn column
        if ($schema->tableExists('#__xgroups')) {
            $schema->addUniqueIndex('#__xgroups', 'idx_cn', 'cn');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // // Add unique index to cn column
        if ($schema->tableExists('#__xgroups')) {
            $schema->dropIndex('#__xgroups', 'idx_cn');
        }
    }
}
