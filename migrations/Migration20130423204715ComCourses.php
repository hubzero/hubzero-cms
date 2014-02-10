<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for creating default member roles if none exist
 **/
class Migration20130423204715ComCourses extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "SELECT * FROM `#__courses_roles`";

		$db->setQuery($query);

		if (!$db->loadResult())
		{
			$query = "INSERT INTO `jos_courses_roles` (`offering_id`, `alias`, `title`, `permissions`)
						VALUES
							(0, 'instructor', 'Instructor', ''),
							(0, 'manager', 'Manager', ''),
							(0, 'student', 'Student', '');";

			$db->setQuery($query);
			$db->query();
		}
	}
}