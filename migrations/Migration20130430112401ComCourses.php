<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for add watching table
 **/
class Migration20130430112401ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";

		if (!$db->tableHasField('#__courses_member_notes', 'timestamp'))
		{
			$query .= "ALTER TABLE `#__support_watching` ADD `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `state`;";
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

		if ($db->tableHasField('#__courses_member_notes', 'timestamp'))
		{
			$query .= "ALTER TABLE `#__courses_member_notes` DROP `timestamp`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}