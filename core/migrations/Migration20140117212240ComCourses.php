<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding a column to track whether an asset should have a corresponding gradebook entry or not
 **/
class Migration20140117212240ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__courses_assets') && $this->db->tableHasField('#__courses_assets', 'course_id') && !$this->db->tableHasField('#__courses_assets', 'graded'))
		{
			$query = "ALTER TABLE `#__courses_assets` ADD `graded` TINYINT(2) NULL DEFAULT NULL AFTER `course_id`";
			$this->db->setQuery($query);
			$this->db->query();

			// Mark all assets of type form as graded
			$query = "UPDATE `#__courses_assets` SET `graded` = 1 WHERE `type` = 'form'";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_assets') && $this->db->tableHasField('#__courses_assets', 'graded') && !$this->db->tableHasField('#__courses_assets', 'grade_weight'))
		{
			$query = "ALTER TABLE `#__courses_assets` ADD `grade_weight` VARCHAR(255) NOT NULL DEFAULT '' AFTER `graded`;";
			$this->db->setQuery($query);
			$this->db->query();

			// Mark all assets of type form as graded
			$query = "UPDATE `#__courses_assets` SET `grade_weight` = `subtype` WHERE `type` = 'form'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__courses_assets') && $this->db->tableHasField('#__courses_assets', 'graded'))
		{
			$query = "ALTER TABLE `#__courses_assets` DROP COLUMN `graded`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_assets') && $this->db->tableHasField('#__courses_assets', 'grade_weight'))
		{
			$query = "ALTER TABLE `#__courses_assets` DROP COLUMN `grade_weight`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
