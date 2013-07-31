<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130731203037ComResources extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableHasField('#__media_tracking', 'total_viewing_time'))
		{
			$query = "ALTER TABLE `#__media_tracking` ADD COLUMN `total_viewing_time` int(11) DEFAULT 0;";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableHasField('#__media_tracking', 'total_viewing_time'))
		{
			$query = "ALTER TABLE `#__media_tracking` DROP `total_viewing_time`;";
			$db->setQuery($query);
			$db->query();
		}
	}
}