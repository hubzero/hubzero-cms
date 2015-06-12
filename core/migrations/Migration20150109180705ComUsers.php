<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for joomla 2.5.28 update
 **/
class Migration20150109180705ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__user_profiles') && $this->db->tableHasField('#__user_profiles', 'profile_value'))
		{
			$query = "ALTER TABLE `#__user_profiles` CHANGE `profile_value` `profile_value` TEXT NOT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__user_profiles') && $this->db->tableHasField('#__user_profiles', 'profile_value'))
		{
			$query = "ALTER TABLE `#__user_profiles` CHANGE `profile_value` `profile_value` VARCHAR(255) NOT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}