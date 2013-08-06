<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding guide plugin
 **/
class Migration20130723171332ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
				SELECT 'Courses - Guide','guide','courses',0,15,1,0,0,0,'0000-00-00 00:00:00',''
				FROM DUAL WHERE NOT EXISTS (SELECT `element` FROM `#__plugins` WHERE `element` = 'guide' AND `folder` = 'courses');";
		}
		else
		{
			$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)
					SELECT 'plg_courses_guide', 'plugin', 'guide', 'courses', 0, 1, 1, 0, null, null, null, null, 0, '0000-00-00 00:00:00', 0, 0
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__extensions` WHERE name = 'plg_courses_guide');";
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
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "DELETE FROM `#__plugins` WHERE `element` = 'guide' AND `folder` = 'courses';";
		}
		else
		{
			$query = "DELETE FROM `#__extensions` WHERE type='plugin' AND folder='courses' AND element='guide';";
		}

		$db->setQuery($query);
		$db->query();
	}
}