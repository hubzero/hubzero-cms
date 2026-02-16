<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for getting rid of duplicate section date entries
**/
class Migration20140225094500ComGroups extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        // get groups who dont have a created value
        // get groups who dont have a created value
        $groups = $this->db->getQuery(true)
            ->select('*')
            ->from('#__xgroups')
            ->whereIsNull('created')
            ->loadObjectList();

        // get created logs
        // get created logs
        $logs = $this->db->getQuery(true)
            ->select(['gidNumber', 'timestamp', 'actorid'])
            ->from('#__xgroups_log')
            ->where('action', '=', 'group_created')
            ->loadAssocList('gidNumber');

        //check each group to see if we have a created log
        foreach ($groups as $group) {
            if (isset($logs[$group->gidNumber])) {
                $log = $logs[$group->gidNumber];
                $hubzeroUserGroup = \Hubzero\User\Group::getInstance($group->gidNumber);
                if (is_object($hubzeroUserGroup)) {
                    $hubzeroUserGroup->set('created', $log['timestamp']);
                    $hubzeroUserGroup->set('created_by', $log['actorid']);
                    $hubzeroUserGroup->update();
                }
            }
        }
    }

    /**
     * Up
     **/
    public function down()
    {
        // there is no down
    }
}
