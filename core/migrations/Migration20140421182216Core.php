<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for making sure passhash field is big enough (match uses_password table)
 **/
class Migration20140421182216Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__users_password_history') && $this->db->tableHasField('#__users_password_history', 'passhash'))
		{
			$query = "ALTER TABLE `#__users_password_history` CHANGE `passhash` `passhash` CHAR(127) NOT NULL  DEFAULT ''";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__users_password_history') && $this->db->tableHasField('#__users_password_history', 'passhash'))
		{
			$query = "ALTER TABLE `#__users_password_history` CHANGE `passhash` `passhash` CHAR(32) NOT NULL  DEFAULT ''";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}