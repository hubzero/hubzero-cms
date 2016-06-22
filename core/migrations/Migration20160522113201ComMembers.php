<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding 'action_browse' column to #__user_profile_fields
 **/
class Migration20160522113201ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__user_profile_fields'))
		{
			if (!$this->db->tableHasField('#__user_profile_fields', 'action_browse'))
			{
				$query = "ALTER TABLE `#__user_profile_fields` ADD `action_browse` TINYINT(2) NOT NULL DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();

				//$query = "UPDATE `#__user_profile_fields` SET `action_browse`=0 WHERE `name` IN ('orgtype', 'countryresident', 'countryorigin', 'url', 'phone', 'orcid', 'gender', 'race', 'disability', 'reason', 'tags', 'address', 'hispanic');";
				$query = "UPDATE `#__user_profile_fields` SET `action_browse`=1 WHERE `name` IN ('organization', 'bio');";
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
			if ($this->db->tableHasField('#__user_profile_fields', 'action_browse'))
			{
				$query = "ALTER TABLE `#__user_profile_fields` DROP COLUMN `action_browse`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
