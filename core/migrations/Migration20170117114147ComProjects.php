<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indexes to #__project tables
 **/
class Migration20170117114147ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__project_activity'))
		{
			if (!$this->db->tableHasKey('#__project_activity', 'idx_projectid'))
			{
				$query = "ALTER IGNORE TABLE `#__project_activity` ADD INDEX `idx_projectid` (`projectid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__project_activity', 'idx_state'))
			{
				$query = "ALTER IGNORE TABLE `#__project_activity` ADD INDEX `idx_state` (`state`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__project_comments'))
		{
			if (!$this->db->tableHasKey('#__project_comments', 'idx_itemid'))
			{
				$query = "ALTER IGNORE TABLE `#__project_comments` ADD INDEX `idx_itemid` (`itemid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__project_comments', 'idx_activityid'))
			{
				$query = "ALTER IGNORE TABLE `#__project_comments` ADD INDEX `idx_activityid` (`activityid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__project_comments', 'idx_state'))
			{
				$query = "ALTER IGNORE TABLE `#__project_comments` ADD INDEX `idx_state` (`state`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__project_logs'))
		{
			if (!$this->db->tableHasKey('#__project_logs', 'idx_projectid'))
			{
				$query = "ALTER IGNORE TABLE `#__project_logs` ADD INDEX `idx_projectid` (`projectid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__project_microblog'))
		{
			if (!$this->db->tableHasKey('#__project_microblog', 'idx_projectid'))
			{
				$query = "ALTER IGNORE TABLE `#__project_microblog` ADD INDEX `idx_projectid` (`projectid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__project_microblog', 'idx_state'))
			{
				$query = "ALTER IGNORE TABLE `#__project_microblog` ADD INDEX `idx_state` (`state`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__project_owners'))
		{
			if (!$this->db->tableHasKey('#__project_owners', 'idx_projectid'))
			{
				$query = "ALTER IGNORE TABLE `#__project_owners` ADD INDEX `idx_projectid` (`projectid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__project_owners', 'idx_userid'))
			{
				$query = "ALTER IGNORE TABLE `#__project_owners` ADD INDEX `idx_userid` (`userid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__project_owners', 'idx_groupid'))
			{
				$query = "ALTER IGNORE TABLE `#__project_owners` ADD INDEX `idx_groupid` (`groupid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__project_owners', 'idx_status'))
			{
				$query = "ALTER IGNORE TABLE `#__project_owners` ADD INDEX `idx_status` (`status`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__project_owners', 'idx_role'))
			{
				$query = "ALTER IGNORE TABLE `#__project_owners` ADD INDEX `idx_role` (`role`)";
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
		if ($this->db->tableExists('#__project_activity'))
		{
			if ($this->db->tableHasKey('#__project_activity', 'idx_projectid'))
			{
				$query = "ALTER TABLE `#__project_activity` DROP KEY `idx_projectid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__project_activity', 'idx_state'))
			{
				$query = "ALTER TABLE `#__project_activity` DROP KEY `idx_state`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__project_comments'))
		{
			if ($this->db->tableHasKey('#__project_comments', 'idx_itemid'))
			{
				$query = "ALTER TABLE `#__project_comments` DROP KEY `idx_itemid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__project_comments', 'idx_activityid'))
			{
				$query = "ALTER TABLE `#__project_comments` DROP KEY `idx_activityid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__project_comments', 'idx_state'))
			{
				$query = "ALTER TABLE `#__project_comments` DROP KEY `idx_state`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__project_logs'))
		{
			if ($this->db->tableHasKey('#__project_logs', 'idx_projectid'))
			{
				$query = "ALTER TABLE `#__project_logs` DROP KEY `idx_projectid`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__project_microblog'))
		{
			if ($this->db->tableHasKey('#__project_microblog', 'idx_projectid'))
			{
				$query = "ALTER TABLE `#__project_microblog` DROP KEY `idx_projectid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__project_microblog', 'idx_state'))
			{
				$query = "ALTER TABLE `#__project_microblog` DROP KEY `idx_state`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__project_owners'))
		{
			if ($this->db->tableHasKey('#__project_owners', 'idx_projectid'))
			{
				$query = "ALTER TABLE `#__project_owners` DROP KEY `idx_projectid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__project_owners', 'idx_userid'))
			{
				$query = "ALTER TABLE `#__project_owners` DROP KEY `idx_userid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__project_owners', 'idx_groupid'))
			{
				$query = "ALTER TABLE `#__project_owners` DROP KEY `idx_groupid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__project_owners', 'idx_status'))
			{
				$query = "ALTER TABLE `#__project_owners` DROP KEY `idx_status`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__project_owners', 'idx_role'))
			{
				$query = "ALTER TABLE `#__project_owners` DROP KEY `idx_role`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}