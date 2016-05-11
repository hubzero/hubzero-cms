<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for altering #__user_profiles to allow for multi-value fields
 **/
class Migration20160511142701ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__user_profiles'))
		{
			if (!$this->db->tableHasField('#__user_profiles', 'id'))
			{
				$query = "ALTER TABLE `#__user_profiles` ADD COLUMN `id` INT(11) UNSIGNED  NOT NULL  AUTO_INCREMENT;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__user_profiles', 'idx_user_id_profile_key'))
			{
				$query = "ALTER TABLE `#__support_tickets` DROP KEY `idx_user_id_profile_key`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__user_profiles', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__support_tickets` ADD INDEX `idx_user_id` (`user_id`)";
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
		if ($this->db->tableExists('#__user_profiles'))
		{
			if ($this->db->tableHasField('#__user_profiles', 'id'))
			{
				$query = "ALTER TABLE `#__user_profiles` DROP COLUMN `id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__user_profiles', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__support_tickets` DROP KEY `idx_user_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__user_profiles', 'idx_user_id_profile_key'))
			{
				$query = "ALTER TABLE `#__support_tickets` ADD UNIQUE INDEX `idx_user_id_profile_key` (`user_id`,`profile_key`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
