<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indexes and 'billable' column to #__time tables
 **/
class Migration20170220190109ComTime extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__time_tasks'))
		{
			if ($this->db->tableHasField('#__time_tasks', 'hub_id') && !$this->db->tableHasKey('#__time_tasks', 'idx_hub_id'))
			{
				$query = "ALTER IGNORE TABLE `#__time_tasks` ADD INDEX `idx_hub_id` (`hub_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__time_tasks', 'liaison_id'))
			{
				$query = "ALTER TABLE `#__time_tasks` CHANGE `liaison_id` `liaison_id` INT(11)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();

				if (!$this->db->tableHasKey('#__time_tasks', 'idx_liaison_id'))
				{
					$query = "ALTER IGNORE TABLE `#__time_tasks` ADD INDEX `idx_liaison_id` (`liaison_id`)";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			if ($this->db->tableHasField('#__time_tasks', 'assignee_id'))
			{
				$query = "ALTER TABLE `#__time_tasks` CHANGE `assignee_id` `assignee_id` INT(11)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();

				if (!$this->db->tableHasKey('#__time_tasks', 'idx_assignee_id'))
				{
					$query = "ALTER IGNORE TABLE `#__time_tasks` ADD INDEX `idx_assignee_id` (`assignee_id`)";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			if ($this->db->tableHasField('#__time_tasks', 'priority'))
			{
				$query = "ALTER TABLE `#__time_tasks` CHANGE `priority` `priority` INT(1)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();

				if (!$this->db->tableHasKey('#__time_tasks', 'idx_priority'))
				{
					$query = "ALTER IGNORE TABLE `#__time_tasks` ADD INDEX `idx_priority` (`priority`)";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			if (!$this->db->tableHasField('#__time_tasks', 'billable'))
			{
				$query = "ALTER TABLE `#__time_tasks` ADD `billable` TINYINT(2)  NOT NULL  DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__time_tasks', 'billable') && !$this->db->tableHasKey('#__time_tasks', 'idx_billable'))
			{
				$query = "ALTER IGNORE TABLE `#__time_tasks` ADD INDEX `idx_billable` (`billable`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__time_records'))
		{
			if ($this->db->tableHasField('#__time_records', 'task_id') && !$this->db->tableHasKey('#__time_records', 'idx_task_id'))
			{
				$query = "ALTER IGNORE TABLE `#__time_records` ADD INDEX `idx_task_id` (`task_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__time_records', 'user_id') && !$this->db->tableHasKey('#__time_records', 'idx_user_id'))
			{
				$query = "ALTER IGNORE TABLE `#__time_records` ADD INDEX `idx_user_id` (`user_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__time_hubs'))
		{
			if ($this->db->tableHasField('#__time_hubs', 'active') && !$this->db->tableHasKey('#__time_hubs', 'idx_active'))
			{
				$query = "ALTER IGNORE TABLE `#__time_hubs` ADD INDEX `idx_active` (`active`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__time_hub_contacts'))
		{
			if ($this->db->tableHasField('#__time_hub_contacts', 'hub_id') && !$this->db->tableHasKey('#__time_hub_contacts', 'idx_hub_id'))
			{
				$query = "ALTER IGNORE TABLE `#__time_hub_contacts` ADD INDEX `idx_hub_id` (`hub_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if (!$this->db->tableExists('#__time_hub_allotments'))
		{
			$query = "CREATE TABLE `#__time_hub_allotments` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `hub_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `start_date` date NOT NULL DEFAULT '0000-00-00',
				  `end_date` date NOT NULL DEFAULT '0000-00-00',
				  `hours` double NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  KEY `idx_hub_id` (`hub_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__time_tasks'))
		{
			if ($this->db->tableHasKey('#__time_tasks', 'idx_assignee_id'))
			{
				$query = "ALTER TABLE `#__time_tasks` DROP KEY `idx_assignee_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__time_tasks', 'idx_liaison_id'))
			{
				$query = "ALTER TABLE `#__time_tasks` DROP KEY `idx_liaison_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__time_tasks', 'idx_priority'))
			{
				$query = "ALTER TABLE `#__time_tasks` DROP KEY `idx_priority`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__time_tasks', 'idx_billable'))
			{
				$query = "ALTER TABLE `#__time_tasks` DROP KEY `idx_billable`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__time_tasks', 'billable'))
			{
				$query = "ALTER TABLE `#__time_tasks` DROP `billable`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__time_records'))
		{
			if ($this->db->tableHasKey('#__time_records', 'idx_task_id'))
			{
				$query = "ALTER TABLE `#__time_records` DROP KEY `idx_task_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__time_records', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__time_records` DROP KEY `idx_user_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__time_hubs'))
		{
			if ($this->db->tableHasKey('#__time_hubs', 'idx_active'))
			{
				$query = "ALTER TABLE `#__time_hubs` DROP KEY `idx_active`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__time_hub_contacts'))
		{
			if ($this->db->tableHasKey('#__time_hub_contacts', 'idx_hub_id'))
			{
				$query = "ALTER TABLE `#__time_hub_contacts` DROP KEY `idx_hub_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__time_hub_allotments'))
		{
			$query = "DROP TABLE `#__time_hub_allotments`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
