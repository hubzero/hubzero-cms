<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Add a column to store formatted citation in citations table
 **/
class Migration20140206131800ComCitations extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = '';

		if (!$db->tableHasField('#__citations', 'formatted'))
		{
			$query .= "ALTER TABLE `#__citations` ADD COLUMN `formatted` TEXT;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = '';

		if ($db->tableHasField('#__citations', 'formatted'))
		{
			$query .= "ALTER TABLE `#__citations` DROP COLUMN `formatted`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}