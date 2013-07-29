<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding more details to asset views table
 **/
class Migration20130729084642ComForum extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableHasField('#__forum_sections', 'ordering'))
		{
			$query = "ALTER TABLE `#__forum_sections` ADD `ordering` INT(11)  NOT NULL  DEFAULT '0'  AFTER `object_id`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__forum_categories', 'ordering'))
		{
			$query = "ALTER TABLE `#__forum_categories` ADD `ordering` INT(11)  NOT NULL  DEFAULT '0'  AFTER `object_id`;";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableHasField('#__forum_sections', 'ordering'))
		{
			$query = "ALTER TABLE `#__forum_sections` DROP `ordering`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__forum_categories', 'ordering'))
		{
			$query = "ALTER TABLE `#__forum_categories` DROP `ordering`;";
			$db->setQuery($query);
			$db->query();
		}
	}
}