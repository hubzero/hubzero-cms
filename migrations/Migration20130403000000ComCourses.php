<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130403000000ComCourses extends Migration
{
	protected static function up($db)
	{
		$query = '';

		if (!$db->tableHasField('#__courses_pages', 'course_id'))
		{
			$query .= "ALTER TABLE `#__courses_pages` ADD `course_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `id`;";
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

		if ($db->tableHasField('#__courses_pages', 'course_id'))
		{
			$query .= "ALTER TABLE `#__courses_pages` DROP `course_id`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}