<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating default member roles if none exist
**/
class Migration20130423204715ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->getQuery(true)->select('id')->from('#__courses_roles')->doesntExist()) {
            $roles = [
                ['offering_id' => 0, 'alias' => 'instructor', 'title' => 'Instructor', 'permissions' => ''],
                ['offering_id' => 0, 'alias' => 'manager',    'title' => 'Manager',    'permissions' => ''],
                ['offering_id' => 0, 'alias' => 'student',    'title' => 'Student',    'permissions' => '']
            ];

            foreach ($roles as $role) {
                $this->db->getQuery(true)
                    ->insertOrIgnore('#__courses_roles')
                    ->set($role)
                    ->execute();
            }
        }
    }
}
