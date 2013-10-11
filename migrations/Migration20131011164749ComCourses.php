<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for changing data type of asset group description field
 **/
class Migration20131011164749ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('#__courses_asset_groups') && $db->tableHasField('#__courses_asset_groups', 'description'))
		{
			$query = "ALTER TABLE `#__courses_asset_groups` CHANGE `description` `description` TEXT  CHARACTER SET utf8  NOT NULL";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists('#__courses_asset_groups') && $db->tableHasField('#__courses_asset_groups', 'description'))
		{
			$query = "ALTER TABLE `#__courses_asset_groups` CHANGE `description` `description` VARCHAR(255)  CHARACTER SET utf8  NOT NULL  DEFAULT ''";
			$db->setQuery($query);
			$db->query();
		}
	}
}