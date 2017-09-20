<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add `username` column to storefront_permissions table
 **/
class Migration20170920165818ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_permissions'))
		{
			if (!$this->db->tableHasField('#__storefront_permissions', 'username'))
			{
				$query = "ALTER TABLE `#__storefront_permissions` ADD `username` varchar(255) DEFAULT NULL";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__storefront_permissions', 'single entry per item'))
			{
				$query = "ALTER TABLE `#__storefront_permissions` DROP KEY `single entry per item`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__storefront_permissions', 'idx_scope_scope_id'))
			{
				$query = "ALTER TABLE `#__storefront_permissions` ADD INDEX `idx_scope_scope_id` (`scope`, `scope_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__storefront_permissions', 'idx_uid'))
			{
				$query = "ALTER TABLE `#__storefront_permissions` ADD INDEX `idx_uid` (`uid`)";
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
		if ($this->db->tableExists('#__storefront_permissions'))
		{
			if ($this->db->tableHasKey('#__storefront_permissions', 'idx_uid'))
			{
				$query = "ALTER TABLE `#__storefront_permissions` DROP KEY `idx_uid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__storefront_permissions', 'idx_scope_scope_id'))
			{
				$query = "ALTER TABLE `#__storefront_permissions` DROP KEY `idx_scope_scope_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__storefront_permissions', 'username'))
			{
				$query = "ALTER TABLE `#__storefront_permissions` DROP `username`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__storefront_permissions', 'single entry per item'))
			{
				$query = "ALTER TABLE `#__storefront_permissions` ADD INDEX `single entry per item` (`scope`, `scope_id`, `uid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
