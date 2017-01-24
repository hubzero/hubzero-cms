<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indexes to #__billboards_billboards table
 **/
class Migration20170124175120ComBillboards extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__billboards_billboards'))
		{
			if (!$this->db->tableHasKey('#__billboards_billboards', 'idx_collection_id'))
			{
				$query = "ALTER IGNORE TABLE `#__billboards_billboards` ADD INDEX `idx_collection_id` (`collection_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__billboards_billboards', 'idx_published'))
			{
				$query = "ALTER IGNORE TABLE `#__billboards_billboards` ADD INDEX `idx_published` (`published`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__billboards_billboards', 'idx_alias'))
			{
				$query = "ALTER IGNORE TABLE `#__billboards_billboards` ADD INDEX `idx_alias` (`alias`)";
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
		if ($this->db->tableExists('#__billboards_billboards'))
		{
			if ($this->db->tableHasKey('#__billboards_billboards', 'idx_collection_id'))
			{
				$query = "ALTER TABLE `#__billboards_billboards` DROP KEY `idx_collection_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__billboards_billboards', 'idx_published'))
			{
				$query = "ALTER TABLE `#__billboards_billboards` DROP KEY `idx_published`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__billboards_billboards', 'idx_alias'))
			{
				$query = "ALTER TABLE `#__billboards_billboards` DROP KEY `idx_alias`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}