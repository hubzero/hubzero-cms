<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding offering_id to notes
 **/
class Migration20130703075132ComCourses extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";

		if ($db->tableHasField('#__courses_pages', 'porder'))
		{
			$query = "ALTER TABLE `#__courses_pages` CHANGE `porder` `ordering` INT(11)  NOT NULL  DEFAULT '0';";
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
		$query = "";

		if ($db->tableHasField('#__courses_pages', 'ordering'))
		{
			$query .= "ALTER TABLE `#__courses_pages` CHANGE `ordering` `porder` INT(11)  NOT NULL  DEFAULT '0';";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}