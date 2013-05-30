<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding twitter authentication plugin
 **/
class Migration20130530153638ComCron extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";

		if (!$db->tableHasField('#__cron_jobs', 'params'))
		{
			$query = "ALTER TABLE `#__cron_jobs` ADD `params` TEXT  NOT NULL  AFTER `ordering`;";
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

		if ($db->tableHasField('#__cron_jobs', 'params'))
		{
			$query .= "ALTER TABLE `#__cron_jobs` DROP `params`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}