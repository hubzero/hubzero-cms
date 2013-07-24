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
		$query = "INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - Guide','guide','courses',0,15,1,0,0,0,'0000-00-00 00:00:00',''
			FROM DUAL WHERE NOT EXISTS (SELECT `element` FROM `#__plugins` WHERE `element` = 'guide' AND `folder` = 'courses');";

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
		$query = "DELETE FROM `#__plugins` WHERE `element` = 'guide' AND `folder` = 'courses';";

		$db->setQuery($query);
		$db->query();
	}
}