<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indexes to #__users_log_auth table
 **/
class Migration20170921114147ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__users_log_auth'))
		{
			if (!$this->db->tableHasKey('#__users_log_auth', 'idx_username'))
			{
				$query = "ALTER IGNORE TABLE `#__users_log_auth` ADD INDEX `idx_username` (`username`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__users_log_auth', 'idx_ip'))
			{
				$query = "ALTER IGNORE TABLE `#__users_log_auth` ADD INDEX `idx_ip` (`ip`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__users_log_auth', 'idx_status'))
			{
				$query = "ALTER IGNORE TABLE `#__users_log_auth` ADD INDEX `idx_status` (`status`)";
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
		if ($this->db->tableExists('#__users_log_auth'))
		{
			if ($this->db->tableHasKey('#__users_log_auth', 'idx_username'))
			{
				$query = "ALTER TABLE `#__users_log_auth` DROP KEY `idx_username`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__users_log_auth', 'idx_ip'))
			{
				$query = "ALTER TABLE `#__users_log_auth` DROP KEY `idx_ip`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__users_log_auth', 'idx_status'))
			{
				$query = "ALTER TABLE `#__users_log_auth` DROP KEY `idx_status`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
