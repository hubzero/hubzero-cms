<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130208000000ComGroups extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__xgroups` DROP `access`;
					ALTER TABLE `#__xgroups` CHANGE `privacy` `discoverability` TINYINT(3);
					ALTER TABLE `#__xgroups` ADD COLUMN `approved` TINYINT(3) DEFAULT 1 AFTER `published`;";

		$db->setQuery($query);
		$db->query();
	}

	protected static function down($db)
	{
		$query = "ALTER TABLE `#__xgroups` DROP `approved`;
					ALTER TABLE `#__xgroups` CHANGE `discoverability` `privacy` TINYINT(3);
					ALTER TABLE `#__xgroups` ADD COLUMN `access` tinyint(3) DEFAULT '0' AFTER `type`";

		$db->setQuery($query);
		$db->query();
	}
}
