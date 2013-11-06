<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20131106152023ComCollections extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__collections_items` ADD FULLTEXT KEY `idx_fulltxt_title_description` (`title`, `description`);";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}

		$query = "ALTER TABLE `#__collections_posts` ADD FULLTEXT KEY `idx_fulltxt_description` (`description`);";

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
		$query = "ALTER TABLE `#__collections_items` DROP INDEX `idx_fulltxt_title_description`;";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}

		$query = "ALTER TABLE `#__collections_posts` DROP INDEX `idx_fulltxt_description`;";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}