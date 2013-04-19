<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for inserting courses outline plugin
 **/
class Migration20130416193151PlgCoursesOutline extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
					SELECT 'Courses - Outline', 'outline', 'courses', 0, 2, 1, 0, 0, 0, '0000-00-00 00:00:00', ''
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE `name` = 'Courses - Outline');";

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
		$query = "DELETE FROM `#__plugins` WHERE `element` = 'outline' AND `folder`='courses'";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}