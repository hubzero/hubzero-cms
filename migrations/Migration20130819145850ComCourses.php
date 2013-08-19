<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding concept of 'attempts' to forms
 **/
class Migration20130819145850ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableHasField('#__courses_form_deployments', 'allowed_attempts') && $db->tableHasField('#__courses_form_deployments', 'user_id'))
		{
			$query = "ALTER TABLE `#__courses_form_deployments` ADD `allowed_attempts` INT(11) NOT NULL DEFAULT '1' AFTER `user_id`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__courses_form_respondents', 'attempt') && $db->tableHasField('#__courses_form_respondents', 'finished'))
		{
			$query = "ALTER TABLE `#__courses_form_respondents` ADD `attempt` INT(11) NOT NULL DEFAULT '1' AFTER `finished`;";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableHasField('#__courses_form_deployments', 'allowed_attempts'))
		{
			$query = "ALTER TABLE `#__courses_form_deployments` DROP `allowed_attempts`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__courses_form_respondents', 'attempt'))
		{
			$query = "ALTER TABLE `#__courses_form_respondents` DROP `attempt`;";
			$db->setQuery($query);
			$db->query();
		}
	}
}