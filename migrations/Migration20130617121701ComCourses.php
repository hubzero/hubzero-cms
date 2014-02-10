<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing wrong datatype on column
 **/
class Migration20130617121701ComCourses extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";

		if (!$db->tableHasField('#__courses_offering_sections', 'params'))
		{
			$query = "ALTER TABLE `#__courses_offering_sections` ADD `params` TEXT  NOT NULL  AFTER `grade_policy_id`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}

		$query = "";

		if (!$db->tableHasField('#__courses_offerings', 'params'))
		{
			$query = "ALTER TABLE `#__courses_offerings` ADD `params` TEXT  NOT NULL  AFTER `created_by`;";
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

		if ($db->tableHasField('#__courses_offering_sections', 'params'))
		{
			$query .= "ALTER TABLE `#__courses_offering_sections` DROP `params`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}

		$query = "";

		if ($db->tableHasField('#__courses_offerings', 'params'))
		{
			$query .= "ALTER TABLE `#__courses_offerings` DROP `params`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}