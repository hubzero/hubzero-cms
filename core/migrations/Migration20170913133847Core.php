<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing incorrect index on activity tables
 **/
class Migration20170913133847Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__activity_recipients') && $this->db->tableHasKey('#__activity_recipients', 'idx_user_id'))
		{
			$query = "ALTER TABLE `#__activity_recipients` DROP KEY `idx_user_id`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "ALTER IGNORE TABLE `#__activity_recipients` ADD INDEX `idx_scope_scope_id` (`scope`, `scope_id`)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__activity_digests') && $this->db->tableHasKey('#__activity_digests', 'idx_user_id'))
		{
			$query = "ALTER TABLE `#__activity_digests` DROP KEY `idx_user_id`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "ALTER IGNORE TABLE `#__activity_digests` ADD INDEX `idx_scope_scope_id` (`scope`, `scope_id`)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__activity_recipients') && $this->db->tableHasKey('#__activity_recipients', 'idx_scope_scope_id'))
		{
			$query = "ALTER TABLE `#__activity_recipients` DROP KEY `idx_scope_scope_id`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "ALTER IGNORE TABLE `#__activity_recipients` ADD INDEX `idx_user_id` (`scope_id`)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__activity_digests') && $this->db->tableHasKey('#__activity_digests', 'idx_scope_scope_id'))
		{
			$query = "ALTER TABLE `#__activity_digests` DROP KEY `idx_scope_scope_id`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "ALTER IGNORE TABLE `#__activity_digests` ADD INDEX `idx_user_id` (`scope_id`)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
