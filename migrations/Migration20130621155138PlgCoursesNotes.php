<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding offering_id to notes
 **/
class Migration20130621155138PlgCoursesNotes extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";

		if (!$db->tableHasField('#__courses_member_notes', 'access'))
		{
			$query = "ALTER TABLE `#__courses_member_notes` ADD `access` TINYINT(2)  NOT NULL  DEFAULT '0';";
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

		if ($db->tableHasField('#__courses_member_notes', 'access'))
		{
			$query .= "ALTER TABLE `#__courses_member_notes` DROP `access`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}