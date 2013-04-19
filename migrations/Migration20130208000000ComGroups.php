<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130208000000ComGroups extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = '';

		if ($db->tableHasField('#__xgroups', 'access'))
		{
			$query .= "ALTER TABLE `#__xgroups` DROP `access`;\n";
		}
		if ($db->tableHasField('#__xgroups', 'privacy') && !$db->tableHasField('#__xgroups', 'discoverability'))
		{
			$query .= "ALTER TABLE `#__xgroups` CHANGE `privacy` `discoverability` TINYINT(3);\n";
		}
		if (!$db->tableHasField('#__xgroups', 'approved'))
		{
			$query .= "ALTER TABLE `#__xgroups` ADD COLUMN `approved` TINYINT(3) DEFAULT 1 AFTER `published`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	protected static function down($db)
	{
		$query = '';

		if ($db->tableHasField('#__xgroups', 'approved'))
		{
			$query .= "ALTER TABLE `#__xgroups` DROP `approved`;\n";
		}
		if (!$db->tableHasField('#__xgroups', 'privacy') && $db->tableHasField('#__xgroups', 'discoverability'))
		{
			$query .= "ALTER TABLE `#__xgroups` CHANGE `discoverability` `privacy` TINYINT(3);\n";
		}
		if (!$db->tableHasField('#__xgroups', 'access'))
		{
			$query .= "ALTER TABLE `#__xgroups` ADD COLUMN `access` tinyint(3) DEFAULT '0' AFTER `type`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}
