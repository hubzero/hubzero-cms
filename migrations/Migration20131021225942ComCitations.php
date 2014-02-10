<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20131021225942ComCitations extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query  = "ALTER TABLE `#__citations` MODIFY COLUMN `volume` VARCHAR(11);";
		$query .= "ALTER TABLE `#__citations` MODIFY COLUMN `year` VARCHAR(4);";

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
		$query  = "ALTER TABLE `#__citations` MODIFY COLUMN `volume` INT(11);";
		$query .= "ALTER TABLE `#__citations` MODIFY COLUMN `year` INT(4);";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}