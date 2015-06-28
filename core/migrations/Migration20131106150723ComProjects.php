<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding projects fulltext key
 **/
class Migration20131106150723ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__projects') && !$this->db->tableHasKey('#__projects', 'idx_fulltxt_alias_title_about'))
		{
			$query = "ALTER TABLE `#__projects` ADD FULLTEXT KEY `idx_fulltxt_alias_title_about` (`alias`, `title`, `about`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__projects') && $this->db->tableHasKey('#__projects', 'idx_fulltxt_alias_title_about'))
		{
			$query = "ALTER TABLE `#__projects` DROP INDEX `idx_fulltxt_alias_title_about`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}