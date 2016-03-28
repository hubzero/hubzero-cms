<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indexes to #__citations_secondary table
 **/
class Migration20160328133847ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__citations_secondary') && !$this->db->tableHasKey('#__citations_secondary', 'idx_cid'))
		{
			$query = "ALTER IGNORE TABLE `#__citations_secondary` ADD INDEX `idx_cid` (`cid`)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__citations_secondary') && !$this->db->tableHasKey('#__citations_secondary', 'idx_scope_scope_id'))
		{
			$query = "ALTER IGNORE TABLE `#__citations_secondary` ADD INDEX `idx_scope_scope_id` (`scope`, `scope_id`)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__citations_secondary') && $this->db->tableHasKey('#__citations_secondary', 'idx_cid'))
		{
			$query = "ALTER ABLE `#__citations_secondary` DROP KEY `idx_cid`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__citations_secondary') && $this->db->tableHasKey('#__citations_secondary', 'idx_scope_scope_id'))
		{
			$query = "ALTER ABLE `#__citations_secondary` DROP KEY `idx_scope_scope_id`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}