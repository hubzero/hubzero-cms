<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to move com_users notes categories to com_members
 **/
class Migration20170911141318ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__categories') && $this->db->tableHasField('#__categories', 'extension'))
		{
			$query = "UPDATE `#__categories` SET `extension`='com_members' WHERE `extension`='com_users'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__categories') && $this->db->tableHasField('#__categories', 'extension'))
		{
			$query = "UPDATE `#__categories` SET `extension`='com_users' WHERE `extension`='com_members'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
