<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding first_visit column to courses_members
 **/
class Migration20131024114858ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableHasField('#__courses_members', 'first_visit'))
		{
			$query = "ALTER TABLE `#__courses_members` ADD `first_visit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
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

		if ($this->db->tableHasField('#__courses_members', 'first_visit'))
		{
			$query .= "ALTER TABLE `#__courses_members` DROP `first_visit`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}