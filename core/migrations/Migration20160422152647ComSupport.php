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
			if (!$this->db->tableHasKey('#__support_tickets', 'idx_status') && $this->db->tableHasField('#__support_tickets', 'status'))
			{
				$query = "ALTER TABLE `#__support_tickets` ADD INDEX `idx_status` (`status`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__support_tickets', 'idx_open') && $this->db->tableHasField('#__support_tickets', 'open'))
			{
				$query = "ALTER TABLE `#__support_tickets` ADD INDEX `idx_open` (`open`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__support_tickets', 'idx_type') && $this->db->tableHasField('#__support_tickets', 'type'))
			{
				$query = "ALTER TABLE `#__support_tickets` ADD INDEX `idx_type` (`type`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__support_tickets', 'idx_group') && $this->db->tableHasField('#__support_tickets', 'group'))
			{
				$query = "ALTER TABLE `#__support_tickets` ADD INDEX `idx_group` (`group`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__support_tickets', 'idx_severity') && $this->db->tableHasField('#__support_tickets', 'severity'))
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
