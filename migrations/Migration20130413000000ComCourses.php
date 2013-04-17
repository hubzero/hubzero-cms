<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130413000000ComCourses extends Hubzero_Migration
{
	protected static function up(&$db)
	{
		// Add a unique index on grade book and asset_id field to forms table
		$query  = "ALTER TABLE `#__courses_grade_book` ADD UNIQUE INDEX `alternate_key` (`user_id`, `scope`, `scope_id`);\n";
		$query .= "ALTER TABLE `#__courses_forms` ADD `asset_id` INT(11)  NULL  DEFAULT NULL  AFTER `created`;\n";
		$query .= "ALTER TABLE `#__courses_forms` ADD `asset_id` INT(11)  NULL  DEFAULT NULL  AFTER `created`;\n";

		$db->setQuery($query);
		$db->query();

		// Get the form id from the asset content fields
		$db->setQuery = "SELECT `id`, `content` FROM `#__courses_assets` WHERE `type`='exam';";
		$rows = $db->loadObjectList();

		// Now insert those into the new forms asset_id field
		foreach ($rows as $row)
		{
			$query  = "UPDATE `#__courses_forms`";
			$query .= " SET `asset_id` = " . $db->Quote($row->id);
			$query .= " WHERE `id` = " . $db->Quote(json_decode($row->content)->form_id) . " AND `asset_id` IS NULL";

			$db->setQuery($query);
			$db->query();
		}

		// Delete the content field for asset type of exam
		$query = "UPDATE `#__courses_assets` SET `content` = '' WHERE `type` = 'exam';";

		$db->setQuery($query);
		$db->query();
	}
}