<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Add a column to store formatted citation in citations table
 **/
class Migration20140206141200ComCitations extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = '';

		if (!$db->tableHasField('#__citations', 'format'))
		{
			$query .= "ALTER TABLE `#__citations` ADD COLUMN `format` VARCHAR(11);";
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

		if ($db->tableHasField('#__citations', 'format'))
		{
			$query .= "ALTER TABLE `#__citations` DROP COLUMN `format`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}