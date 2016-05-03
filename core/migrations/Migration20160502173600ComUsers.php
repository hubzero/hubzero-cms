<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for moving homeDirectory, loginShell, and ftpShell columns to users table
 **/
class Migration20160502173600ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__users', 'homeDirectory') && $this->db->tableHasField('#__xprofiles', 'homeDirectory'))
		{
			$query = "ALTER TABLE `#__users` ADD COLUMN `homeDirectory` VARCHAR(255) NOT NULL;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__users` AS u LEFT JOIN `#__xprofiles` AS x ON u.`id`=x.`uidNumber` SET u.`homeDirectory`=x.`homeDirectory`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__users', 'loginShell') && $this->db->tableHasField('#__xprofiles', 'loginShell'))
		{
			$query = "ALTER TABLE `#__users` ADD COLUMN `loginShell` VARCHAR(255) NOT NULL;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__users` AS u LEFT JOIN `#__xprofiles` AS x ON u.`id`=x.`uidNumber` SET u.`loginShell`=x.`loginShell`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__users', 'ftpShell') && $this->db->tableHasField('#__xprofiles', 'ftpShell'))
		{
			$query = "ALTER TABLE `#__users` ADD COLUMN `ftpShell` VARCHAR(255) NOT NULL;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__users` AS u LEFT JOIN `#__xprofiles` AS x ON u.`id`=x.`uidNumber` SET u.`ftpShell`=x.`ftpShell`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__users', 'homeDirectory'))
		{
			$query = "ALTER TABLE `#__users` DROP COLUMN `homeDirectory`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__users', 'loginShell'))
		{
			$query = "ALTER TABLE `#__users` DROP COLUMN `loginShell`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__users', 'ftpShell'))
		{
			$query = "ALTER TABLE `#__users` DROP COLUMN `ftpShell`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}