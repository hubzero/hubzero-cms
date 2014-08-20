<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding all day event flag.
 **/
class Migration20140820171853ComEvents extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// all day field
		if (!$this->db->tableHasField('#__events', 'allday'))
		{
			$query = "ALTER TABLE `#__events` ADD COLUMN allday INT(11) DEFAULT 0 AFTER publish_down;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// all day field
		if ($this->db->tableHasField('#__events', 'allday'))
		{
			$query = "ALTER TABLE `#__events` DROP COLUMN allday;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}