<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding more details to asset views table
 **/
class Migration20130725184342ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableHasField('#__courses_asset_views', 'course_id') && $db->tableHasField('#__courses_asset_views', 'asset_id'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` ADD `course_id` INT(11)  NULL  DEFAULT NULL  AFTER `asset_id`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__courses_asset_views', 'ip') && $db->tableHasField('#__courses_asset_views', 'viewed_by'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` ADD `ip` VARCHAR(15)  CHARACTER SET utf8  NULL  DEFAULT NULL  AFTER `viewed_by`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__courses_asset_views', 'url') && $db->tableHasField('#__courses_asset_views', 'ip'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` ADD `url` VARCHAR(255)  CHARACTER SET utf8  NULL  DEFAULT NULL  AFTER `ip`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__courses_asset_views', 'referrer') && $db->tableHasField('#__courses_asset_views', 'url'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` ADD `referrer` VARCHAR(255)  CHARACTER SET utf8  NULL  DEFAULT NULL  AFTER `url`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__courses_asset_views', 'user_agent_string') && $db->tableHasField('#__courses_asset_views', 'referrer'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` ADD `user_agent_string` VARCHAR(255)  CHARACTER SET utf8  NULL  DEFAULT NULL  AFTER `referrer`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__courses_asset_views', 'session_id') && $db->tableHasField('#__courses_asset_views', 'user_agent_string'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` ADD `session_id` VARCHAR(200)  CHARACTER SET utf8  NULL  DEFAULT NULL  AFTER `user_agent_string`;";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableHasField('#__courses_asset_views', 'course_id'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` DROP `course_id`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__courses_asset_views', 'ip'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` DROP `ip`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__courses_asset_views', 'url'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` DROP `url`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__courses_asset_views', 'referrer'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` DROP `referrer`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__courses_asset_views', 'user_agent_string'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` DROP `user_agent_string`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__courses_asset_views', 'session_id'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` DROP `session_id`;";
			$db->setQuery($query);
			$db->query();
		}
	}
}