<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130809152737ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableHasField('#__courses_pages', 'section_id'))
		{
			$query = "ALTER TABLE `#__courses_pages` ADD `section_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `offering_id`;";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableHasField('#__courses_pages', 'section_id'))
		{
			$query = "ALTER TABLE `#__courses_pages` DROP `section_id`;";
			$db->setQuery($query);
			$db->query();
		}
	}
}