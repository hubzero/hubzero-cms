<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing up members badges table
 **/
class Migration20130814201250ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableHasField('#__courses_member_badges', 'claimed') && !$db->tableHasField('#__courses_member_badges', 'action'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` CHANGE `claimed` `action` VARCHAR(255) NULL DEFAULT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__courses_member_badges', 'claimed_on') && !$db->tableHasField('#__courses_member_badges', 'action_on'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` CHANGE `claimed_on` `action_on` DATETIME NULL  DEFAULT NULL;";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if (!$db->tableHasField('#__courses_member_badges', 'claimed') && $db->tableHasField('#__courses_member_badges', 'action'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` CHANGE `action` `claimed` INT(1) NULL DEFAULT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__courses_member_badges', 'claimed_on') && $db->tableHasField('#__courses_member_badges', 'action_on'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` CHANGE `action_on` `claimed_on` DATETIME NULL  DEFAULT NULL;";
			$db->setQuery($query);
			$db->query();
		}
	}
}