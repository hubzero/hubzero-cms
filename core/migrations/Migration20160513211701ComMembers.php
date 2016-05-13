<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding 'dependents' column to #__user_profile_options
 **/
class Migration20160513211701ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__user_profile_options'))
		{
			if (!$this->db->tableHasField('#__user_profile_options', 'dependents'))
			{
				$query = "ALTER TABLE `#__user_profile_options` ADD `dependents` TINYTEXT  NULL;";
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
		if ($this->db->tableExists('#__user_profile_options'))
		{
			if ($this->db->tableHasField('#__user_profile_options', 'dependents'))
			{
				$query = "ALTER TABLE `#__user_profile_options` DROP COLUMN `dependents`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
