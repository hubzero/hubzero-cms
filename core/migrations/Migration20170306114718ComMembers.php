<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add `min` and `max` columns to profile fields table
 **/
class Migration20170306114718ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__user_profile_fields'))
		{
			if (!$this->db->tableHasField('#__user_profile_fields', 'min'))
			{
				$query = "ALTER TABLE `#__user_profile_fields` ADD `min` int(11) NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__user_profile_fields', 'max'))
			{
				$query = "ALTER TABLE `#__user_profile_fields` ADD `max` int(11) NOT NULL DEFAULT '0'";
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
			if ($this->db->tableHasField('#__user_profile_fields', 'min'))
			{
				$query = "ALTER TABLE `#__user_profile_fields` DROP `min`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__user_profile_fields', 'max'))
			{
				$query = "ALTER TABLE `#__user_profile_fields` DROP `max`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
