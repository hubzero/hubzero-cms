<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add `parent` column to activity_logs and `starred` to activity_recipients
 **/
class Migration20161128165818Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__activity_logs'))
		{
			if (!$this->db->tableHasField('#__activity_logs', 'parent'))
			{
				$query = "ALTER TABLE `#__activity_logs` ADD `parent` int(11) NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__activity_logs', 'parent') && !$this->db->tableHasKey('#__activity_logs', 'idx_parent'))
			{
				$query = "ALTER TABLE `#__activity_logs` ADD INDEX `idx_parent` (`parent`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__activity_logs', 'description'))
			{
				$query = "ALTER TABLE `#__activity_logs` CHANGE `description` `description` text DEFAULT NULL";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__activity_recipients'))
		{
			if (!$this->db->tableHasField('#__activity_recipients', 'starred'))
			{
				$query = "ALTER TABLE `#__activity_recipients` ADD `starred` tinyint(2) NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__activity_recipients', 'starred') && !$this->db->tableHasKey('#__activity_recipients', 'idx_starred'))
			{
				$query = "ALTER TABLE `#__activity_recipients` ADD INDEX `idx_starred` (`starred`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__activity_logs'))
		{
			if ($this->db->tableHasKey('#__activity_logs', 'idx_parent'))
			{
				$query = "ALTER TABLE `#__activity_logs` DROP KEY `idx_parent`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__activity_logs', 'parent'))
			{
				$query = "ALTER TABLE `#__activity_logs` DROP `parent`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__activity_logs', 'description'))
			{
				$query = "ALTER TABLE `#__activity_logs` CHANGE `description` `description` varchar(250) DEFAULT NULL";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__activity_recipients'))
		{
			if ($this->db->tableHasKey('#__activity_recipients', 'idx_starred'))
			{
				$query = "ALTER TABLE `#__activity_recipients` DROP KEY `idx_starred`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__activity_recipients', 'starred'))
			{
				$query = "ALTER TABLE `#__activity_recipients` DROP `starred`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
