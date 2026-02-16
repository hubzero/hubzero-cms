<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for filling in missing password hashes in tables
 *
*/
class Migration20161021135212ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // 1. users <- users_password
        if (
            $schema->tableExists('#__users')
            && $schema->tableExists('#__users_password')
            && $schema->hasColumn('#__users', 'password')
            && $schema->hasColumn('#__users_password', 'passhash')
        ) {
            $rows = $this->db->getQuery(true)
                ->select('u.id')
                ->select('up.passhash')
                ->from('#__users', 'u')
                ->join('#__users_password AS up', 'u.id', 'up.user_id')
                ->beginOrGroup()
                    ->where('u.password', '=', '')
                    ->orWhereIsNull('u.password')
                ->endAndGroup()
                ->beginAndGroup()
                    ->where('up.passhash', '!=', '')
                    ->whereIsNotNull('up.passhash')
                ->endAndGroup()
                ->loadObjectList();

            foreach ($rows as $row) {
                $this->db->getQuery(true)
                    ->update('#__users')
                    ->set(['password' => $row->passhash])
                    ->where('id', '=', $row->id)
                    ->execute();
            }
        }

        // 2. users <- xprofiles
        if (
            $schema->tableExists('#__users')
            && $schema->tableExists('#__xprofiles')
            && $schema->hasColumn('#__users', 'password')
            && $schema->hasColumn('#__xprofiles', 'userPassword')
        ) {
            $rows = $this->db->getQuery(true)
                ->select('u.id')
                ->select('xp.userPassword')
                ->from('#__users', 'u')
                ->join('#__xprofiles AS xp', 'u.id', 'xp.uidNumber')
                ->beginOrGroup()
                    ->where('u.password', '=', '')
                    ->orWhereIsNull('u.password')
                ->endAndGroup()
                ->beginAndGroup()
                    ->where('xp.userPassword', '!=', '')
                    ->whereIsNotNull('xp.userPassword')
                ->endAndGroup()
                ->loadObjectList();

            foreach ($rows as $row) {
                $this->db->getQuery(true)
                    ->update('#__users')
                    ->set(['password' => $row->userPassword])
                    ->where('id', '=', $row->id)
                    ->execute();
            }
        }

        // 3. xprofiles <- users_password
        if (
            $schema->tableExists('#__xprofiles')
            && $schema->tableExists('#__users_password')
            && $schema->hasColumn('#__xprofiles', 'userPassword')
            && $schema->hasColumn('#__users_password', 'passhash')
        ) {
            $rows = $this->db->getQuery(true)
                ->select('xp.uidNumber')
                ->select('up.passhash')
                ->from('#__xprofiles', 'xp')
                ->join('#__users_password AS up', 'xp.uidNumber', 'up.user_id')
                ->beginOrGroup()
                    ->where('xp.userPassword', '=', '')
                    ->orWhereIsNull('xp.userPassword')
                ->endAndGroup()
                ->beginAndGroup()
                    ->where('up.passhash', '!=', '')
                    ->whereIsNotNull('up.passhash')
                ->endAndGroup()
                ->loadObjectList();

            foreach ($rows as $row) {
                $this->db->getQuery(true)
                    ->update('#__xprofiles')
                    ->set(['userPassword' => $row->passhash])
                    ->where('uidNumber', '=', $row->uidNumber)
                    ->execute();
            }
        }

        // 4. xprofiles <- users
        if (
            $schema->tableExists('#__xprofiles')
            && $schema->tableExists('#__users')
            && $schema->hasColumn('#__xprofiles', 'userPassword')
            && $schema->hasColumn('#__users', 'password')
        ) {
            $rows = $this->db->getQuery(true)
                ->select('xp.uidNumber')
                ->select('u.password')
                ->from('#__xprofiles', 'xp')
                ->join('#__users AS u', 'xp.uidNumber', 'u.id')
                ->beginOrGroup()
                    ->where('xp.userPassword', '=', '')
                    ->orWhereIsNull('xp.userPassword')
                ->endAndGroup()
                ->beginAndGroup()
                    ->where('u.password', '!=', '')
                    ->whereIsNotNull('u.password')
                ->endAndGroup()
                ->loadObjectList();

            foreach ($rows as $row) {
                $this->db->getQuery(true)
                    ->update('#__xprofiles')
                    ->set(['userPassword' => $row->password])
                    ->where('uidNumber', '=', $row->uidNumber)
                    ->execute();
            }
        }

        // 5. users_password <- xprofiles
        if (
            $schema->tableExists('#__users_password')
            && $schema->tableExists('#__xprofiles')
            && $schema->hasColumn('#__users_password', 'passhash')
            && $schema->hasColumn('#__xprofiles', 'userPassword')
        ) {
            $rows = $this->db->getQuery(true)
                ->select('up.user_id')
                ->select('xp.userPassword')
                ->from('#__users_password', 'up')
                ->join('#__xprofiles AS xp', 'up.user_id', 'xp.uidNumber')
                ->beginOrGroup()
                    ->where('up.passhash', '=', '')
                    ->orWhereIsNull('up.passhash')
                ->endAndGroup()
                ->beginAndGroup()
                    ->where('xp.userPassword', '!=', '')
                    ->whereIsNotNull('xp.userPassword')
                ->endAndGroup()
                ->loadObjectList();

            foreach ($rows as $row) {
                $this->db->getQuery(true)
                    ->update('#__users_password')
                    ->set(['passhash' => $row->userPassword])
                    ->where('user_id', '=', $row->user_id)
                    ->execute();
            }
        }

        // 6. users_password <- users
        if (
            $schema->tableExists('#__users_password')
            && $schema->tableExists('#__users')
            && $schema->hasColumn('#__users_password', 'passhash')
            && $schema->hasColumn('#__users', 'password')
        ) {
            $rows = $this->db->getQuery(true)
                ->select('up.user_id')
                ->select('u.password')
                ->from('#__users_password', 'up')
                ->join('#__users AS u', 'up.user_id', 'u.id')
                ->beginOrGroup()
                    ->where('up.passhash', '=', '')
                    ->orWhereIsNull('up.passhash')
                ->endAndGroup()
                ->beginAndGroup()
                    ->where('u.password', '!=', '')
                    ->whereIsNotNull('u.password')
                ->endAndGroup()
                ->loadObjectList();

            foreach ($rows as $row) {
                $this->db->getQuery(true)
                    ->update('#__users_password')
                    ->set(['passhash' => $row->password])
                    ->where('user_id', '=', $row->user_id)
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        /* No down method. Irreversible data migration. */
    }
}
