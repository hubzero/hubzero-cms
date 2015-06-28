<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding a few need primary keys
 **/
class Migration20140623144900Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xgroups_member_roles') && !$this->db->tableHasField('#__xgroups_member_roles', 'id'))
		{
			$query = "ALTER TABLE `#__xgroups_member_roles` ADD `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xmessage_seen') && !$this->db->tableHasField('#__xmessage_seen', 'id'))
		{
			$query = "ALTER TABLE `#__xmessage_seen` ADD `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__xgroups_member_roles') && $this->db->tableHasField('#__xgroups_member_roles', 'id'))
		{
			$query = "ALTER TABLE `#__xgroups_member_roles` DROP `id`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xmessage_seen') && $this->db->tableHasField('#__xmessage_seen', 'id'))
		{
			$query = "ALTER TABLE `#__xmessage_seen` DROP `id`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}