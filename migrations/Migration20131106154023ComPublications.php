<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20131106154023ComPublications extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableHasKey('#__publication_versions', 'idx_fulltxt_title_description_abstract'))
		{
			$query = "ALTER TABLE `#__publication_versions` ADD FULLTEXT KEY `idx_fulltxt_title_description_abstract` (`title`, `description`, `abstract`);";

			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableHasKey('#__publication_versions', 'idx_fulltxt_title_description_abstract'))
		{
			$query = "ALTER TABLE `#__publication_versions` DROP INDEX `idx_fulltxt_title_description_abstract`;";

			$db->setQuery($query);
			$db->query();
		}
	}
}