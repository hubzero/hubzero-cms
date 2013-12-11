<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20131211150056ComResources extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "ALTER TABLE #__document_text_data ADD COLUMN hash CHAR(40)";

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
		$query = "ALTER TABLE #__document_text_data DROP COLUMN hash";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}
