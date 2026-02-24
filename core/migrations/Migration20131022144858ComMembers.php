<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Utility\Date;

/**
 * Migration script for moving member manager notes to user notes table
 *
*/
class Migration20131022144858ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xprofiles_manager') && $schema->tableExists('#__user_notes')) {
            // Get admin user id number (probabaly 62)
            $admin_id = (int) $this->db->getQuery(true)
                ->select('id')
                ->from('#__users')
                ->where('username', '=', 'admin')
                ->value('id');

            // Start by grabbing all xprofile_manager entries
            $results = $this->db->getQuery(true)
                ->select('*')
                ->from('#__xprofiles_manager')
                ->loadObjectList();

            if ($results && count($results) > 0) {
                foreach ($results as $r) {
                    $this->db->getQuery(true)
                        ->insert('#__user_notes')
                        ->values([
                            'user_id'         => $r->uidNumber,
                            'subject'         => $r->manager,
                            'state'           => '1',
                            'created_user_id' => $admin_id,
                            'created_time'    => Date::of('now')->toSql()
                        ])
                        ->execute();
                }
            }

            $this->db->schema()->dropTable('#__xprofiles_manager');
        }
    }
}
