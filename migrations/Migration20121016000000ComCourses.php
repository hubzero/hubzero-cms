<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20121016000000ComCourses extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = "INSERT INTO `jos_components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`)
					SELECT 'Courses', 'option=com_courses', 0, 0, 'option=com_courses', 'courses', 'com_courses', 0, 'js/ThemeOffice/component.png', 0, '', 0
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_components` WHERE name = 'Courses');";

		$db->setQuery($query);
		$db->query();
	}
}