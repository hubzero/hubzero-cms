<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130404000000ComCitations extends Hubzero_Migration
{
	protected static function up(&$db)
	{
		$query = "ALTER TABLE `#__citations_sponsors` ADD COLUMN `image` VARCHAR(200);";

		$db->setQuery($query);
		$db->query();
	}

	protected static function down(&$db)
	{
		$query = "ALTER TABLE `#__citations_sponsors` DROP COLUMN `image`;";

		$db->setQuery($query);
		$db->query();
	}
}