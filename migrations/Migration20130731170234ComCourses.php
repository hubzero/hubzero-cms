<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding an override field to courses grade book
 **/
class Migration20130731170234ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableHasField('#__courses_grade_book', 'override') && $db->tableHasField('#__courses_grade_book', 'scope_id'))
		{
			$query = "ALTER TABLE `#__courses_grade_book` ADD `override` DECIMAL(5,2)  NULL  DEFAULT NULL  AFTER `scope_id`;";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableHasField('#__courses_grade_book', 'override'))
		{
			$query = "ALTER TABLE `#__courses_grade_book` DROP `override`;";
			$db->setQuery($query);
			$db->query();
		}
	}
}