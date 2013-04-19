<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130114000000Core extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = '';

		if ($db->tableExists('#__venue') && !$db->tableExists('venue'))
		{
			$query .= "RENAME TABLE `#__venue` TO `venue`;\n";
		}

		$query .= "ALTER TABLE `venue` CHANGE `venue` `venue` VARCHAR(40) DEFAULT NULL;\n";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}

		// Reset query
		$query = '';

		if ($db->tableHasField('venue', 'network'))
		{
			$query .= "ALTER TABLE `venue` DROP COLUMN `network`;\n";
		}
		if (!$db->tableHasField('venue', 'state'))
		{
			$query .= "ALTER TABLE `venue` ADD COLUMN `state` VARCHAR(15) DEFAULT NULL AFTER `venue`;\n";
		}
		if (!$db->tableHasField('venue', 'type'))
		{
			$query .= "ALTER TABLE `venue` ADD COLUMN `type` VARCHAR(10) DEFAULT NULL AFTER `state`;\n";
		}
		if (!$db->tableHasField('venue', 'mw_version'))
		{
			$query .= "ALTER TABLE `venue` ADD COLUMN `mw_version` VARCHAR(3) DEFAULT NULL AFTER `type`;\n";
		}
		if (!$db->tableHasField('venue', 'ssh_key_path'))
		{
			$query .= "ALTER TABLE `venue` ADD COLUMN `ssh_key_path` VARCHAR(200) DEFAULT NULL AFTER `mw_version`;\n";
		}
		if (!$db->tableHasField('venue', 'latitude'))
		{
			$query .= "ALTER TABLE `venue` ADD COLUMN `latitude` DOUBLE DEFAULT NULL AFTER `ssh_key_path`;\n";
		}
		if (!$db->tableHasField('venue', 'longitude'))
		{
			$query .= "ALTER TABLE `venue` ADD COLUMN `longitude` DOUBLE DEFAULT NULL AFTER `latitude`;\n";
		}
		if (!$db->tableHasField('venue', 'master'))
		{
			$query .= "ALTER TABLE `venue` ADD COLUMN `master` VARCHAR(255) DEFAULT NULL AFTER `longitude`;\n";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}

		// Reset query
		$query = '';

		if ($db->tableExists('#__venue_countries') && !$db->tableExists('venue_countries'))
		{
			$query .= "RENAME TABLE `#__venue_countries` TO `venue_countries`;\n";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}

		// Reset query
		$query = '';

		if (!$db->tableHasField('venue_countries', 'id'))
		{
			$query .= "ALTER TABLE `venue_countries` ADD COLUMN `id` INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT FIRST;\n";
		}
		if (!$db->tableHasField('venue_countries', 'venue_id'))
		{
			$query .= "ALTER TABLE `venue_countries` ADD COLUMN `venue_id` INT(11) NOT NULL AFTER `id`;\n";
		}
		if ($db->tableHasField('venue_countries', 'venue'))
		{
			$query .= "ALTER TABLE `venue_countries` DROP COLUMN `venue`;\n";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}