<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding unique constraint to users.username field
 **/
class Migration20150109200952ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__users')
		 && $this->db->tableHasField('#__users', 'username')
		 && !$this->db->tableHasKey('#__users', 'uidx_username'))
		{
			$query = "ALTER TABLE `#__users` ADD CONSTRAINT UNIQUE KEY uidx_username(`username`)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__users') && $this->db->tableHasKey('#__users', 'uidx_username'))
		{
			$query = "ALTER TABLE `#__users` DROP KEY uidx_username";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}