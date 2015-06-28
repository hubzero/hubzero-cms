<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for including section id on course pages
 **/
class Migration20130809152737ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__courses_pages', 'section_id'))
		{
			$query = "ALTER TABLE `#__courses_pages` ADD `section_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `offering_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__courses_pages', 'section_id'))
		{
			$query = "ALTER TABLE `#__courses_pages` DROP `section_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}