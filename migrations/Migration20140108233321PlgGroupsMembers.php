<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140108233321PlgGroupsMembers extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";
		
		// change role to name
		if (!$db->tableHasField('#__xgroups_roles', 'name'))
		{
			$query = "ALTER TABLE `#__xgroups_roles` CHANGE `role` `name` VARCHAR(150);";
		}
		
		// add permissions field
		if (!$db->tableHasField('#__xgroups_roles', 'permissions'))
		{
			$query = "ALTER TABLE `#__xgroups_roles` ADD COLUMN `permissions` TEXT;";
		}
		
		// add role to roleid
		if (!$db->tableHasField('#__xgroups_member_roles', 'roleid'))
		{
			$query = "ALTER TABLE `#__xgroups_member_roles` CHANGE `role` `roleid` INT(11);";
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
		
		// change role to name
		if ($db->tableHasField('#__xgroups_roles', 'name'))
		{
			$query = "ALTER TABLE `#__xgroups_roles` CHANGE `name` `role` VARCHAR(150);";
		}
		
		// add permissions field
		if ($db->tableHasField('#__xgroups_roles', 'permissions'))
		{
			$query = "ALTER TABLE `#__xgroups_roles` DROP COLUMN `permissions`;";
		}
		
		// add role to roleid
		if ($db->tableHasField('#__xgroups_member_roles', 'roleid'))
		{
			$query = "ALTER TABLE `#__xgroups_member_roles` CHANGE `roleid` `role` INT(11);";
		}
		
		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}