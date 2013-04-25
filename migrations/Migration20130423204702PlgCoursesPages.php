<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding pages plugin entry
 **/
class Migration20130423204702PlgCoursesPages extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
					SELECT 'Courses - Pages', 'pages', 'courses', 0, 4, 1, 0, 0, 0, '0000-00-00 00:00:00', ''
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE name = 'Courses - Pages');";

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Down
	 **/
	protected function down($db)
	{
		$query = "DELETE FROM `#__plugins` WHERE folder='courses' AND element='pages';";

		$db->setQuery($query);
		$db->query();
	}
}