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
 * Migration script for putting group-less members into the default group
  *
**/
class Migration20140822132824ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        // Get all users that have no group set
        $ids = $this->db->getQuery(true)
            ->select('u.id')
            ->from('#__users', 'u')
            ->leftJoin('#__user_usergroup_map AS um', 'u.id', 'um.user_id')
            ->where('group_id', 'IS', new Expression('NULL'))
            ->loadColumn();

        if ($ids && count($ids) > 0) {
            // Get the default new user group
            $group_id = $this->getParams('com_users')->get('new_usertype');

            if (!isset($group_id) || !is_numeric($group_id)) {
                $this->setError(
                    'Failed to retrieve a proper new user type. Please ensure one has been set.',
                    'warning'
                );
                return;
            }

            $group_id = $this->db->quote($group_id);

            foreach ($ids as $id) {
                $id = $this->db->quote($id);
                $this->db->getQuery(true)
                    ->insert('#__user_usergroup_map')
                    ->columns(['user_id', 'group_id'])
                    ->values("{$id},{$group_id}")
                    ->execute();
            }
        }
    }
}
