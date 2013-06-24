<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130624184902PlgGroupsCalendar extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";
		
		if (!$db->tableHasField('#__events', 'ical_uid'))
		{
			$query .= "ALTER TABLE `#__events` ADD COLUMN `ical_uid` VARCHAR(255) AFTER `calendar_id`;";
		}
		
		if (!$db->tableHasField('#__events_calendars', 'url'))
		{
			$query .= "ALTER TABLE `#__events_calendars` ADD COLUMN `url` VARCHAR(255);";
		}
		
		if (!$db->tableHasField('#__events_calendars', 'readonly'))
		{
			$query .= "ALTER TABLE `#__events_calendars` ADD COLUMN `readonly` TINYINT(4) DEFAULT 0;";
		}
		
		if (!$db->tableHasField('#__events_calendars', 'last_fetched'))
		{
			$query .= "ALTER TABLE `#__events_calendars` ADD COLUMN `last_fetched` DATETIME;";
		}
		
		if (!$db->tableHasField('#__events_calendars', 'last_fetched_attempt'))
		{
			$query .= "ALTER TABLE `#__events_calendars` ADD COLUMN `last_fetched_attempt` DATETIME;";
		}
		
		if (!$db->tableHasField('#__events_calendars', 'failed_attempts'))
		{
			$query .= "ALTER TABLE `#__events_calendars` ADD COLUMN `failed_attempts` INT(11) DEFAULT 0;";
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

		if ($db->tableHasField('#__events', 'ical_uid'))
		{
			$query .= "ALTER TABLE `#__events` DROP `ical_uid`;";
		}
		
		if ($db->tableHasField('#__events_calendars', 'url'))
		{
			$query .= "ALTER TABLE `#__events_calendars` DROP `url`;";
		}
		
		if ($db->tableHasField('#__events_calendars', 'readonly'))
		{
			$query .= "ALTER TABLE `#__events_calendars` DROP `readonly`;";
		}
		
		if ($db->tableHasField('#__events_calendars', 'last_fetched'))
		{
			$query .= "ALTER TABLE `#__events_calendars` DROP `last_fetched`;";
		}
		
		if ($db->tableHasField('#__events_calendars', 'last_fetched_attempt'))
		{
			$query .= "ALTER TABLE `#__events_calendars` DROP `last_fetched_attempt`;";
		}
		
		if ($db->tableHasField('#__events_calendars', 'failed_attempts'))
		{
			$query .= "ALTER TABLE `#__events_calendars` DROP `failed_attempts`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}