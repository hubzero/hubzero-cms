<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing wrong datatype on column
 **/
class Migration20130617121701ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableHasField('#__courses_offering_sections', 'params'))
		{
			$query = "ALTER TABLE `#__courses_offering_sections` ADD `params` TEXT  NOT NULL  AFTER `grade_policy_id`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "";

		if (!$this->db->tableHasField('#__courses_offerings', 'params'))
		{
			$query = "ALTER TABLE `#__courses_offerings` ADD `params` TEXT  NOT NULL  AFTER `created_by`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "";

		if ($this->db->tableHasField('#__courses_offering_sections', 'params'))
		{
			$query .= "ALTER TABLE `#__courses_offering_sections` DROP `params`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "";

		if ($this->db->tableHasField('#__courses_offerings', 'params'))
		{
			$query .= "ALTER TABLE `#__courses_offerings` DROP `params`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}