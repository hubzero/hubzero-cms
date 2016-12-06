<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add `sync_group` column to projects table
 **/
class Migration20161206114718ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__projects'))
		{
			if (!$this->db->tableHasField('#__projects', 'sync_group'))
			{
				$query = "ALTER TABLE `#__projects` ADD `sync_group` tinyint(2) NOT NULL DEFAULT '1'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__projects', 'sync_group') && !$this->db->tableHasKey('#__projects', 'idx_sync_group'))
			{
				$query = "ALTER TABLE `#__projects` ADD INDEX `idx_sync_group` (`sync_group`)";
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
		if ($this->db->tableExists('#__projects'))
		{
			if ($this->db->tableHasKey('#__projects', 'idx_sync_group'))
			{
				$query = "ALTER TABLE `#__projects` DROP KEY `idx_sync_group`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__projects', 'sync_group'))
			{
				$query = "ALTER TABLE `#__projects` DROP `sync_group`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
