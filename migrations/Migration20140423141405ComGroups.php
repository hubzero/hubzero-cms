<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140423141405ComGroups extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		// select all groups with duplicate cname's
		$query = "SELECT gidNumber, cn, description 
				  FROM `#__xgroups` WHERE cn IN (
					  SELECT cn FROM `#__xgroups` GROUP BY cn HAVING COUNT(*) > 1
				  ) ORDER BY gidNumber;";
		$db->setQuery($query);
		$duplicateGroups = $db->loadObjectList();

		// var to hold original groups
		$original = array();

		// loop through each group
		foreach ($duplicateGroups as $duplicateGroup)
		{
			// make sure to keep the original group
			if (!in_array($duplicateGroup->cn, $original))
			{
				$original[] = $duplicateGroup->cn;
				continue;
			}

			// delete group
			// also deletes membership related stuff
			$hzGroup = Hubzero_Group::getInstance($duplicateGroup->gidNumber);
			$hzGroup->delete();
		}

		// Add unique index to cn column
		if ($db->tableExists('#__xgroups'))
		{
			if (!$db->tableHasKey('#__xgroups', 'idx_cn'))
			{
				$query  = "ALTER TABLE `#__xgroups` ADD UNIQUE INDEX `idx_cn` (`cn`);";
				$db->setQuery($query);
				$db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		// Add unique index to cn column
		if ($db->tableExists('#__xgroups'))
		{
			if ($db->tableHasKey('#__xgroups', 'idx_cn'))
			{
				$query  = "ALTER TABLE `#__xgroups` DROP INDEX `idx_cn`;";
				$db->setQuery($query);
				$db->query();
			}
		}

	}
}