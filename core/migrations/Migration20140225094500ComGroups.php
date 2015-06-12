<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for getting rid of duplicate section date entries
 **/
class Migration20140225094500ComGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// get groups who dont have a created value
		$query = "SELECT * FROM `#__xgroups` WHERE `created` IS NULL ";
		$this->db->setQuery($query);
		$groups = $this->db->loadObjectList();

		// get created logs
		$query2 = "SELECT `gidNumber`,`timestamp`,`actorid` FROM `#__xgroups_log` WHERE `action`='group_created'";
		$this->db->setQuery($query2);
		$logs = $this->db->loadAssocList('gidNumber');

		//check each group to see if we have a created log
		foreach ($groups as $group)
		{
			if (isset($logs[$group->gidNumber]))
			{
				$log = $logs[$group->gidNumber];
				$hubzeroUserGroup = \Hubzero\User\Group::getInstance($group->gidNumber);
				if (is_object($hubzeroUserGroup))
				{
					$hubzeroUserGroup->set('created', $log['timestamp']);
					$hubzeroUserGroup->set('created_by', $log['actorid']);
					$hubzeroUserGroup->update();
				}
			}
		}
	}

	/**
	 * Up
	 **/
	public function down()
	{
		// there is no down
	}
}