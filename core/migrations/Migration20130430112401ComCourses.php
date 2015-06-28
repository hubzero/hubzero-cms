<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for add watching table
 **/
class Migration20130430112401ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableHasField('#__courses_member_notes', 'timestamp'))
		{
			$query .= "ALTER TABLE `#__courses_member_notes` ADD `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `state`;";
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

		if ($this->db->tableHasField('#__courses_member_notes', 'timestamp'))
		{
			$query .= "ALTER TABLE `#__courses_member_notes` DROP `timestamp`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}