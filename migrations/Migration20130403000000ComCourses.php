<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130403000000ComCourses extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__courses_pages` ADD `course_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `id`;";

		$db->setQuery($query);
		$db->query();
	}

	protected static function down($db)
	{
		$query = "ALTER TABLE `#__courses_pages` DROP `course_id`;";

		$db->setQuery($query);
		$db->query();
	}
}