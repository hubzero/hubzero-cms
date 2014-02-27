<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for getting rid of duplicate section date entries
 **/
class Migration20140225094500ComGroups extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		// get groups who dont have a created value
		$query = "SELECT * FROM `#__xgroups` WHERE `created` IS NULL ";
		$db->setQuery($query);
		$groups = $db->loadObjectList();

		// get created logs
		$query2 = "SELECT `gid`,`timestamp`,`actorid` FROM `#__xgroups_log` WHERE `action`='group_created'";
		$db->setQuery($query2);
		$logs = $db->loadAssocList('gid');

		//check each group to see if we have a created log
		foreach ($groups as $group)
		{
			if (isset($logs[$group->gidNumber]))
			{
				$log = $logs[$group->gidNumber];
				$hubzeroUserGroup = Hubzero_Group::getInstance($group->gidNumber);
				if (is_object($hubzeroUserGroup))
				{
					$hubzeroUserGroup->set('created', $log['timestamp']);
					$hubzeroUserGroup->set('created_by', $log['actorid']);
					$hubzeroUserGroup->update();
				}
			}
		}
	}
}
