<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding help component
 **/
class Migration20130426071658ComHelp extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "INSERT INTO `#__components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`)
					SELECT 'Help', 'option=com_help', 0, 0, '', '', 'com_help', 0, '', 0, '', 1
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__components` WHERE name = 'Help');";

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
		$query = "DELETE FROM `#__components` WHERE `name` = 'Help';";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}