<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130403000000Core extends Hubzero_Migration
{
	protected static function up(&$db)
	{
		$query = "ALTER TABLE `#__auth_link` ADD COLUMN `linked_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;";

		$db->setQuery($query);
		$db->query();
	}

	protected static function down(&$db)
	{
		$query = "ALTER TABLE `#__auth_link` DROP COLUMN `linked_on`;";

		$db->setQuery($query);
		$db->query();
	}
}