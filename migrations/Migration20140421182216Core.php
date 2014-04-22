<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for making sure passhash field is big enough (match uses_password table)
 **/
class Migration20140421182216Core extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('#__users_password_history') && $db->tableHasField('#__users_password_history', 'passhash'))
		{
			$query = "ALTER TABLE `#__users_password_history` CHANGE `passhash` `passhash` CHAR(127) NOT NULL  DEFAULT ''";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists('#__users_password_history') && $db->tableHasField('#__users_password_history', 'passhash'))
		{
			$query = "ALTER TABLE `#__users_password_history` CHANGE `passhash` `passhash` CHAR(32) NOT NULL  DEFAULT ''";
			$db->setQuery($query);
			$db->query();
		}
	}
}