<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding offering_id to notes
 **/
class Migration20130703075132ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if ($this->db->tableHasField('#__courses_pages', 'porder'))
		{
			$query = "ALTER TABLE `#__courses_pages` CHANGE `porder` `ordering` INT(11)  NOT NULL  DEFAULT '0';";
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

		if ($this->db->tableHasField('#__courses_pages', 'ordering'))
		{
			$query .= "ALTER TABLE `#__courses_pages` CHANGE `ordering` `porder` INT(11)  NOT NULL  DEFAULT '0';";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}