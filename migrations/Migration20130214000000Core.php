<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130214000000Core extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = '';

		if (!$db->tableHasField('host', 'venue_id'))
		{
			$query .= "ALTER TABLE `host` ADD COLUMN `venue_id` INT(11)  AFTER `portbase`;";
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

		if ($db->tableHasField('host', 'venue_id'))
		{
			$query .= "ALTER TABLE `host` DROP COLUMN `venue_id`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}
