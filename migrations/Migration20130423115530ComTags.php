<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding tags substitutes index
 **/
class Migration20130423115530ComTags extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";

		if (!$db->tableHasKey('#__tags_substitute', 'idx_tag_id'))
		{
			$query .= "ALTER TABLE `#__tags_substitute` ADD INDEX `idx_tag_id` (`tag_id`);";
		}

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
		$query = "";

		if ($db->tableHasKey('#__tags_substitute', 'idx_tag_id'))
		{
			$query .= "DROP INDEX `idx_tag_id` ON `#__tags_substitute`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}