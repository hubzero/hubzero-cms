<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indexes to #__xmessage tables
 **/
class Migration20170129140423PlgXmessage extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xmessage'))
		{
			if ($this->db->tableHasField('#__xmessage', 'created_by') && !$this->db->tableHasKey('#__xmessage', 'idx_created_by'))
			{
				$query = "ALTER IGNORE TABLE `#__xmessage` ADD INDEX `idx_created_by` (`created_by`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__xmessage', 'type') && !$this->db->tableHasKey('#__xmessage', 'idx_type'))
			{
				$query = "ALTER IGNORE TABLE `#__xmessage` ADD INDEX `idx_type` (`type`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xmessage_notify'))
		{
			if ($this->db->tableHasField('#__xmessage_notify', 'type') && !$this->db->tableHasKey('#__xmessage_notify', 'idx_type'))
			{
				$query = "ALTER IGNORE TABLE `#__xmessage_notify` ADD INDEX `idx_type` (`type`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xmessage_recipient'))
		{
			if ($this->db->tableHasField('#__xmessage_recipient', 'state') && !$this->db->tableHasKey('#__xmessage_recipient', 'idx_state'))
			{
				$query = "ALTER IGNORE TABLE `#__xmessage_recipient` ADD INDEX `idx_state` (`state`)";
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
		if ($this->db->tableExists('#__xmessage'))
		{
			if ($this->db->tableHasKey('#__xmessage', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__xmessage` DROP KEY `idx_created_by`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__xmessage', 'idx_type'))
			{
				$query = "ALTER TABLE `#__xmessage` DROP KEY `idx_type`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xmessage_notify'))
		{
			if ($this->db->tableHasKey('#__xmessage_notify', 'idx_type'))
			{
				$query = "ALTER IGNORE TABLE `#__xmessage_notify` DROP KEY `idx_type`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xmessage_recipient'))
		{
			if ($this->db->tableHasKey('#__xmessage_recipient', 'idx_state'))
			{
				$query = "ALTER IGNORE TABLE `#__xmessage_recipient` DROP KEY `idx_state`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}