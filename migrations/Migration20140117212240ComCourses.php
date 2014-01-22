<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding a column to track whether an asset should have a corresponding gradebook entry or not
 **/
class Migration20140117212240ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('#__courses_assets') && $db->tableHasField('#__courses_assets', 'course_id') && !$db->tableHasField('#__courses_assets', 'graded'))
		{
			$query = "ALTER TABLE `#__courses_assets` ADD `graded` TINYINT(2) NULL DEFAULT NULL AFTER `course_id`";
			$db->setQuery($query);
			$db->query();

			// Mark all assets of type form as graded
			$query = "UPDATE `#__courses_assets` SET `graded` = 1 WHERE `type` = 'form'";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableExists('#__courses_assets') && $db->tableHasField('#__courses_assets', 'graded') && !$db->tableHasField('#__courses_assets', 'grade_weight'))
		{
			$query = "ALTER TABLE `#__courses_assets` ADD `grade_weight` VARCHAR(255) NOT NULL DEFAULT '' AFTER `graded`;";
			$db->setQuery($query);
			$db->query();

			// Mark all assets of type form as graded
			$query = "UPDATE `#__courses_assets` SET `grade_weight` = `subtype` WHERE `type` = 'form'";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists('#__courses_assets') && $db->tableHasField('#__courses_assets', 'graded'))
		{
			$query = "ALTER TABLE `#__courses_assets` DROP COLUMN `graded`";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableExists('#__courses_assets') && $db->tableHasField('#__courses_assets', 'grade_weight'))
		{
			$query = "ALTER TABLE `#__courses_assets` DROP COLUMN `grade_weight`";
			$db->setQuery($query);
			$db->query();
		}
	}
}