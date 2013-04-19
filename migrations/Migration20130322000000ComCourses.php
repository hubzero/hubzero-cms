<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130322000000ComCourses extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = '';

		if (!$db->tableHasField('#__courses_form_respondent_progress', 'submitted'))
		{
			$query .= "ALTER TABLE `#__courses_form_respondent_progress` ADD `submitted` DATETIME  NULL  AFTER `answer_id`;";
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

		if ($db->tableHasField('#__courses_form_respondent_progress', 'submitted'))
		{
			$query .= "ALTER TABLE `#__courses_form_respondent_progress` DROP `submitted`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}