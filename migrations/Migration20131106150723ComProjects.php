<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20131106150723ComProjects extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__projects` ADD FULLTEXT KEY `idx_fulltxt_alias_title_about` (`alias`, `title`, `about`);";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "ALTER TABLE `#__projects` DROP INDEX `idx_fulltxt_alias_title_about`;";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}