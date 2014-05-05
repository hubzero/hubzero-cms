<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for renaming venue_id to zone_id on host table
 **/
class Migration20140505141538ComTools extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('host')
			&& $db->tableHasField('host', 'venue_id')
			&& !$db->tableHasField('host', 'zone_id'))
		{
			$query = "ALTER TABLE `host` CHANGE `venue_id` `zone_id` INT(11) NULL DEFAULT NULL";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists('host')
			&& !$db->tableHasField('host', 'venue_id')
			&& $db->tableHasField('host', 'zone_id'))
		{
			$query = "ALTER TABLE `host` CHANGE `zone_id` `venue_id` INT(11) NULL DEFAULT NULL";
			$db->setQuery($query);
			$db->query();
		}
	}
}