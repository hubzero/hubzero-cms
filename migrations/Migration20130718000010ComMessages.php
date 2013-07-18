<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for messages table changes
 **/
class Migration20130718000010ComMessages extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__messages` ENGINE = InnoDB ;\n";
		$query .= "ALTER TABLE `#__messages_cfg` ENGINE = InnoDB ;";
		$db->setQuery($query);
		$db->query();

		if ($db->tableHasField('#__messages', 'subject'))
		{
			$query = "ALTER TABLE `#__messages` CHANGE `subject` `subject` varchar(255) NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__messages', 'state'))
		{
			$query = "ALTER TABLE `#__messages` CHANGE `state` `state` tinyint(1) NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__messages', 'priority'))
		{
			$query = "ALTER TABLE `#__messages` CHANGE `priority` `priority` tinyint(1) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__messages', 'folder_id'))
		{
			$query = "ALTER TABLE `#__messages` CHANGE `folder_id` `folder_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
	}
}