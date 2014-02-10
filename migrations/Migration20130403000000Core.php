<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130403000000Core extends Migration
{
	protected static function up($db)
	{
		$query = '';

		if (!$db->tableHasField('#__auth_link', 'linked_on'))
		{
			$query .= "ALTER TABLE `#__auth_link` ADD COLUMN `linked_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;";
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

		if ($db->tableHasField('#__auth_link', 'linked_on'))
		{
			$query .= "ALTER TABLE `#__auth_link` DROP COLUMN `linked_on`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}