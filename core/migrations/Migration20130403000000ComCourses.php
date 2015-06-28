<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding course_id to pages table
 **/
class Migration20130403000000ComCourses extends Base
{
	public function up()
	{
		$query = '';

		if (!$this->db->tableHasField('#__courses_pages', 'course_id'))
		{
			$query .= "ALTER TABLE `#__courses_pages` ADD `course_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `id`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		$query = '';

		if ($this->db->tableHasField('#__courses_pages', 'course_id'))
		{
			$query .= "ALTER TABLE `#__courses_pages` DROP `course_id`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}