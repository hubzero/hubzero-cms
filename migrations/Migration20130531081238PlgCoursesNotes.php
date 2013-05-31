<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding offering_id to notes
 **/
class Migration20130531081238PlgCoursesNotes extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";

		if (!$db->tableHasField('#__courses_member_notes', 'section_id'))
		{
			$query = "ALTER TABLE `#__courses_member_notes` ADD `section_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `timestamp`;";
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
		$query = "";

		if ($db->tableHasField('#__courses_member_notes', 'section_id'))
		{
			$query .= "ALTER TABLE `#__courses_member_notes` DROP `section_id`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}