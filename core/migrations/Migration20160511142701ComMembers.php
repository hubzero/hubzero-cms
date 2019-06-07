<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

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
				$query = "ALTER TABLE `#__user_profiles` ADD COLUMN `id` INT(11) UNSIGNED  PRIMARY KEY  NOT NULL  AUTO_INCREMENT  FIRST;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__user_profiles', 'idx_user_id_profile_key'))
			{
				$query = "ALTER TABLE `#__user_profiles` DROP KEY `idx_user_id_profile_key`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__user_profiles', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__user_profiles` ADD INDEX `idx_user_id` (`user_id`)";
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
				$query = "ALTER TABLE `#__user_profiles` DROP KEY `idx_user_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__user_profiles', 'idx_user_id_profile_key'))
			{
				$query = "ALTER TABLE `#__user_profiles` ADD UNIQUE INDEX `idx_user_id_profile_key` (`user_id`,`profile_key`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
