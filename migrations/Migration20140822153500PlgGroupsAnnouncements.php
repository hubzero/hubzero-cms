<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding indices to announcements tables
 **/
class Migration20140822153500PlgGroupsAnnouncements extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__announcements'))
		{
			if ($this->db->tableHasKey('#__announcements', 'jos_wishlist_vote_wishid_idx'))
			{
				$query = "ALTER TABLE `#__wishlist_vote` DROP INDEX `jos_wishlist_vote_wishid_idx`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__announcements', 'idx_scope_scope_id'))
			{
				$query = "ALTER TABLE `#__announcements` ADD INDEX `idx_scope_scope_id` (`scope`, `scope_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__announcements', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__announcements` ADD INDEX `idx_created_by` (`created_by`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__announcements', 'idx_state'))
			{
				$query = "ALTER TABLE `#__announcements` ADD INDEX `idx_state` (`state`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__announcements', 'idx_priority'))
			{
				$query = "ALTER TABLE `#__announcements` ADD INDEX `idx_priority` (`priority`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__announcements', 'idx_sticky'))
			{
				$query = "ALTER TABLE `#__announcements` ADD INDEX `idx_sticky` (`sticky`);";
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
		if ($this->db->tableExists('#__announcements'))
		{
			if ($this->db->tableHasKey('#__announcements', 'idx_state'))
			{
				$query = "ALTER TABLE `#__announcements` DROP INDEX `idx_state`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__announcements', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__announcements` DROP INDEX `idx_created_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__announcements', 'idx_scope_scope_id'))
			{
				$query = "ALTER TABLE `#__announcements` DROP INDEX `idx_scope_scope_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__announcements', 'idx_priority'))
			{
				$query = "ALTER TABLE `#__announcements` DROP INDEX `idx_priority`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__announcements', 'idx_sticky'))
			{
				$query = "ALTER TABLE `#__announcements` DROP INDEX `idx_sticky`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}