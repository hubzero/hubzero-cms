<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating category items from com_users to com_members
 **/
// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
class Migration20190505000000ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if ($this->db->tableExists('#__categories')) {
            $query = "UPDATE `#__categories` SET `extension`='com_members' WHERE `extension`='com_users'";
            $this->db->setQuery($query);
            if ($this->db->query()) {
                $this->log('Updated categories extension from `com_users` to `com_members`');
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if ($this->db->tableExists('#__categories')) {
            $query = "UPDATE `#__categories` SET `extension`='com_users' WHERE `extension`='com_members'";
            $this->db->setQuery($query);
            if ($this->db->query()) {
                $this->log('Updated categories extension from `com_members` to `com_users`');
            }
        }
    }
}
