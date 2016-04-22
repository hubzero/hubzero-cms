<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indexes to #__support_tickets table
 **/
class Migration20160422152647ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_tickets'))
		{
			if (!$this->db->tableHasKey('#__support_tickets', 'idx_status'))
			{
				$query = "ALTER TABLE `#__support_tickets` ADD INDEX `idx_status` (`status`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__support_tickets', 'idx_open'))
			{
				$query = "ALTER TABLE `#__support_tickets` ADD INDEX `idx_open` (`open`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__support_tickets', 'idx_type'))
			{
				$query = "ALTER TABLE `#__support_tickets` ADD INDEX `idx_type` (`type`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__support_tickets', 'idx_group'))
			{
				$query = "ALTER TABLE `#__support_tickets` ADD INDEX `idx_group` (`group`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__support_tickets', 'idx_severity'))
			{
				$query = "ALTER TABLE `#__support_tickets` ADD INDEX `idx_severity` (`severity`)";
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
		if ($this->db->tableExists('#__support_tickets'))
		{
			if ($this->db->tableHasKey('#__support_tickets', 'idx_status'))
			{
				$query = "ALTER TABLE `#__support_tickets` DROP KEY `idx_status`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__support_tickets', 'idx_open'))
			{
				$query = "ALTER TABLE `#__support_tickets` DROP KEY `idx_open`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__support_tickets', 'idx_type'))
			{
				$query = "ALTER TABLE `#__support_tickets` DROP KEY `idx_type`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__support_tickets', 'idx_group'))
			{
				$query = "ALTER TABLE `#__support_tickets` DROP KEY `idx_group`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__support_tickets', 'idx_severity'))
			{
				$query = "ALTER TABLE `#__support_tickets` DROP KEY `idx_severity`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}