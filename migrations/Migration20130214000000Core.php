<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130214000000Core extends Hubzero_Migration
{
	protected static function up(&$db)
	{
		$query = "ALTER TABLE `host` ADD COLUMN `venue_id` INT(11)  AFTER `portbase`;";

		$db->setQuery($query);
		$db->query();
	}

	protected static function down(&$db)
	{
		$query = "ALTER TABLE `host` DROP COLUMN `venue_id`;";

		$db->setQuery($query);
		$db->query();
	}
}
