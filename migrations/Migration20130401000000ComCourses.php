<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130401000000ComCourses extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__courses_offering_sections` ADD `enrollment` TINYINT(2)  NOT NULL  DEFAULT '0'  AFTER `created_by`;";

		$db->setQuery($query);
		$db->query();
	}

	protected static function down($db)
	{
		$query = "ALTER TABLE `#__courses_offering_sections` DROP `enrollment`;";

		$db->setQuery($query);
		$db->query();
	}
}