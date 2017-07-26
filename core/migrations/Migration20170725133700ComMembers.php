<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding 'derivatives' field to publication licenses
 **/
class Migration20170725133700ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__user_profile_fields'))
		{
			if (!$this->db->tableHasField('#__user_profile_fields', 'placeholder'))
			{
				$query = "ALTER TABLE `#__user_profile_fields` ADD COLUMN `placeholder` varchar(255) NOT NULL default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__user_profile_fields'))
		{
			if ($this->db->tableHasField('#__user_profile_fields', 'placeholder'))
			{
				$query = "ALTER TABLE `#__user_profile_fields` DROP `placeholder`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
