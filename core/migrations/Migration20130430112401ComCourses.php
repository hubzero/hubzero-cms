<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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