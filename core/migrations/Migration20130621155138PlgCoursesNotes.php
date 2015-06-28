<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding offering_id to notes
 **/
class Migration20130621155138PlgCoursesNotes extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableHasField('#__courses_member_notes', 'access'))
		{
			$query = "ALTER TABLE `#__courses_member_notes` ADD `access` TINYINT(2)  NOT NULL  DEFAULT '0';";
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

		if ($this->db->tableHasField('#__courses_member_notes', 'access'))
		{
			$query .= "ALTER TABLE `#__courses_member_notes` DROP `access`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}