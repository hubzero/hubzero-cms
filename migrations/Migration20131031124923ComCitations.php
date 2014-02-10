<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20131031124923ComCitations extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__citations` MODIFY `affiliated` int(11) NOT NULL DEFAULT 0;";

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
		$query = "ALTER TABLE `#__citations` MODIFY `affiliated` int(11) DEFAULT NULL;";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}