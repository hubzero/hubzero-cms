<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding pages plugin entry
 **/
class Migration20130729130302PlgYsearchCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
					SELECT 'YSearch - Courses', 'courses', 'ysearch', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE name = 'YSearch - Courses');";
		}
		else
		{
			$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)
					SELECT 'YSearch - Courses', 'plugin', courses', 'ysearch', 0, 1, 1, 0, null, null, null, null, 0, '0000-00-00 00:00:00', 0, 0
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__extensions` WHERE name = 'YSearch - Courses');";
		}

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Down
	 **/
	protected function down($db)
	{
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "DELETE FROM `#__plugins` WHERE folder='ysearch' AND element='courses';";
		}
		else
		{
			$query = "DELETE FROM `#__extensions` WHERE type='plugin' AND folder='ysearch' AND element='courses';";
		}

		$db->setQuery($query);
		$db->query();
	}
}