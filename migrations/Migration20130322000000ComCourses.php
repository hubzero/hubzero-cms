<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130322000000ComCourses extends Hubzero_Migration
{
	protected static function up(&$db)
	{
		$query = "ALTER TABLE `#__courses_form_respondent_progress` ADD `submitted` DATETIME  NULL  AFTER `answer_id`;";

		$db->setQuery($query);
		$db->query();
	}

	protected static function down(&$db)
	{
		$query = "ALTER TABLE `#__courses_form_respondent_progress` DROP `submitted`;";

		$db->setQuery($query);
		$db->query();
	}
}