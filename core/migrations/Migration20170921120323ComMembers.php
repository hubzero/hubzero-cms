<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indexes to #__users tables
 **/
class Migration20170921120323ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__user_reputation'))
		{
			if (!$this->db->tableHasKey('#__user_reputation', 'idx_user_id'))
			{
				$query = "ALTER IGNORE TABLE `#__user_reputation` ADD INDEX `idx_user_id` (`user_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__users_password_history'))
		{
			if (!$this->db->tableHasKey('#__users_password_history', 'idx_user_id'))
			{
				$query = "ALTER IGNORE TABLE `#__users_password_history` ADD INDEX `idx_user_id` (`user_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__users_points'))
		{
			if (!$this->db->tableHasKey('#__users_points', 'idx_uid'))
			{
				$query = "ALTER IGNORE TABLE `#__users_points` ADD INDEX `idx_uid` (`uid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__users_quotas'))
		{
			if (!$this->db->tableHasKey('#__users_quotas', 'idx_user_id'))
			{
				$query = "ALTER IGNORE TABLE `#__users_quotas` ADD INDEX `idx_user_id` (`user_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__users_quotas', 'idx_class_id'))
			{
				$query = "ALTER IGNORE TABLE `#__users_quotas` ADD INDEX `idx_class_id` (`class_id`)";
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
		if ($this->db->tableExists('#__user_reputation'))
		{
			if ($this->db->tableHasKey('#__user_reputation', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__user_reputation` DROP KEY `idx_user_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__users_password_history'))
		{
			if ($this->db->tableHasKey('#__users_password_history', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__users_password_history` DROP KEY `idx_user_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__users_points'))
		{
			if ($this->db->tableHasKey('#__users_points', 'idx_uid'))
			{
				$query = "ALTER TABLE `#__users_points` DROP KEY `idx_uid`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__users_quotas'))
		{
			if ($this->db->tableHasKey('#__users_quotas', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__users_quotas` DROP KEY `idx_user_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__users_quotas', 'idx_class_id'))
			{
				$query = "ALTER TABLE `#__users_quotas` DROP KEY `idx_class_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
