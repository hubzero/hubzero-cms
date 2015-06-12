<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding an override field to courses grade book
 **/
class Migration20130731170234ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__courses_grade_book', 'override') && $this->db->tableHasField('#__courses_grade_book', 'scope_id'))
		{
			$query = "ALTER TABLE `#__courses_grade_book` ADD `override` DECIMAL(5,2)  NULL  DEFAULT NULL  AFTER `scope_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__courses_grade_book', 'override'))
		{
			$query = "ALTER TABLE `#__courses_grade_book` DROP `override`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}