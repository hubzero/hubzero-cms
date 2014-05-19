<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for grade book unique index and form asset id reference
 **/
class Migration20130413000000ComCourses extends Base
{
	public function up()
	{
		$query = '';
		$runExtra = false;

		// Add a unique index on grade book and asset_id field to forms table
		if (!$this->db->tableHasKey('#__courses_grade_book', 'alternate_key'))
		{
			$query .= "ALTER TABLE `#__courses_grade_book` ADD UNIQUE INDEX `alternate_key` (`user_id`, `scope`, `scope_id`);\n";
		}

		if (!$this->db->tableHasField('#__courses_forms', 'asset_id'))
		{
			$query .= "ALTER TABLE `#__courses_forms` ADD `asset_id` INT(11)  NULL  DEFAULT NULL  AFTER `created`;\n";
			$runExtra = true;
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($runExtra)
		{
			// Get the form id from the asset content fields
			$this->db->setQuery("SELECT `id`, `content` FROM `#__courses_assets` WHERE `type`='exam';");
			$rows = $this->db->loadObjectList();

			// Now insert those into the new forms asset_id field
			foreach ($rows as $row)
			{
				$query  = "UPDATE `#__courses_forms`";
				$query .= " SET `asset_id` = " . $this->db->Quote($row->id);
				$query .= " WHERE `id` = " . $this->db->Quote(json_decode($row->content)->form_id) . " AND `asset_id` IS NULL";

				$this->db->setQuery($query);
				$this->db->query();
			}

			// Delete the content field for asset type of exam
			$query = "UPDATE `#__courses_assets` SET `content` = '' WHERE `type` = 'exam';";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}