<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating category items from com_users to com_members
 **/
class Migration20190505000000ComMembers extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        if ($this->db->tableExists('#__categories')) {
            $query = "UPDATE `#__categories` SET `extension`='com_members' WHERE `extension`='com_users'";
            $this->db->setQuery($query);
            $this->db->query();

            $this->log('Updated categories extension from `com_users` to `com_members`');
        }
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        if ($this->db->tableExists('#__categories')) {
            $query = "UPDATE `#__categories` SET `extension`='com_users' WHERE `extension`='com_members'";
            $this->db->setQuery($query);
            $this->db->query();

            $this->log('Updated categories extension from `com_members` to `com_users`');
        }
    }
}
