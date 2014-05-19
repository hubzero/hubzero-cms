<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
		$query = "ALTER TABLE `#__projects` ADD FULLTEXT KEY `idx_fulltxt_alias_title_about` (`alias`, `title`, `about`);";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "ALTER TABLE `#__projects` DROP INDEX `idx_fulltxt_alias_title_about`;";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}