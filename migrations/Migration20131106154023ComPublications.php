<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20131106154023ComPublications extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__publication_versions` ADD FULLTEXT KEY `idx_fulltxt_title_description_abstract` (`title`, `description`, `abstract`);";

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
		$query = "ALTER TABLE `#__publication_versions` DROP INDEX `idx_fulltxt_title_description_abstract`;";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}