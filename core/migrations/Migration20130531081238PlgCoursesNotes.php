<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding offering_id to notes
 **/
class Migration20130531081238PlgCoursesNotes extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableHasField('#__courses_member_notes', 'section_id'))
		{
			$query = "ALTER TABLE `#__courses_member_notes` ADD `section_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `timestamp`;";
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

		if ($this->db->tableHasField('#__courses_member_notes', 'section_id'))
		{
			$query .= "ALTER TABLE `#__courses_member_notes` DROP `section_id`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}