<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130412000000ComCourses extends Hubzero_Migration
{
	protected static function up(&$db)
	{
		$query = "ALTER TABLE `#__courses_offering_sections` ADD `grade_policy_id` INT(11)  NOT NULL  DEFAULT '1'  AFTER `enrollment`;";

		$db->setQuery($query);
		$db->query();
	}

	protected static function down(&$db)
	{
		$query = "ALTER TABLE `#__courses_offering_sections` DROP `grade_policy_id`;";

		$db->setQuery($query);
		$db->query();
	}
}