<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indexes to #__xgroups tables
 **/
class Migration20170124114147ComGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xgroups_inviteemails'))
		{
			if (!$this->db->tableHasKey('#__xgroups_inviteemails', 'idx_gidNumber'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_inviteemails` ADD INDEX `idx_gidNumber` (`gidNumber`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_log'))
		{
			if (!$this->db->tableHasKey('#__xgroups_log', 'idx_gidNumber'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_log` ADD INDEX `idx_gidNumber` (`gidNumber`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xgroups_log', 'idx_userid'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_log` ADD INDEX `idx_userid` (`userid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xgroups_log', 'idx_actorid'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_log` ADD INDEX `idx_actorid` (`actorid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_memberoption'))
		{
			if (!$this->db->tableHasKey('#__xgroups_memberoption', 'idx_gidNumber'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_memberoption` ADD INDEX `idx_gidNumber` (`gidNumber`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xgroups_memberoption', 'idx_userid'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_memberoption` ADD INDEX `idx_userid` (`userid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_modules'))
		{
			if (!$this->db->tableHasKey('#__xgroups_modules', 'idx_gidNumber'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_modules` ADD INDEX `idx_gidNumber` (`gidNumber`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xgroups_modules', 'idx_state'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_modules` ADD INDEX `idx_state` (`state`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_pages'))
		{
			if (!$this->db->tableHasKey('#__xgroups_pages', 'idx_gidNumber'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_pages` ADD INDEX `idx_gidNumber` (`gidNumber`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xgroups_pages', 'idx_state'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_pages` ADD INDEX `idx_state` (`state`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_pages_categories'))
		{
			if (!$this->db->tableHasKey('#__xgroups_pages_categories', 'idx_gidNumber'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_pages_categories` ADD INDEX `idx_gidNumber` (`gidNumber`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_pages_versions'))
		{
			if (!$this->db->tableHasKey('#__xgroups_pages_versions', 'idx_pageid'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_pages_versions` ADD INDEX `idx_pageid` (`pageid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xgroups_pages_versions', 'idx_approved'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_pages_versions` ADD INDEX `idx_approved` (`approved`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xgroups_pages_versions', 'idx_scanned'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_pages_versions` ADD INDEX `idx_scanned` (`scanned`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_reasons'))
		{
			if (!$this->db->tableHasKey('#__xgroups_reasons', 'idx_gidNumber'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_reasons` ADD INDEX `idx_gidNumber` (`gidNumber`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xgroups_reasons', 'idx_uidNumber'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_reasons` ADD INDEX `idx_uidNumber` (`uidNumber`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_roles'))
		{
			if (!$this->db->tableHasKey('#__xgroups_roles', 'idx_gidNumber'))
			{
				$query = "ALTER IGNORE TABLE `#__xgroups_roles` ADD INDEX `idx_gidNumber` (`gidNumber`)";
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
		if ($this->db->tableExists('#__xgroups_inviteemails'))
		{
			if ($this->db->tableHasKey('#__xgroups_inviteemails', 'idx_gidNumber'))
			{
				$query = "ALTER TABLE `#__xgroups_inviteemails` DROP KEY `idx_gidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_log'))
		{
			if ($this->db->tableHasKey('#__xgroups_log', 'idx_gidNumber'))
			{
				$query = "ALTER TABLE `#__xgroups_log` DROP KEY `idx_gidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__xgroups_log', 'idx_userid'))
			{
				$query = "ALTER TABLE `#__xgroups_log` DROP KEY `idx_userid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__xgroups_log', 'idx_actorid'))
			{
				$query = "ALTER TABLE `#__xgroups_log` DROP KEY `idx_actorid`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_memberoption'))
		{
			if ($this->db->tableHasKey('#__xgroups_memberoption', 'idx_gidNumber'))
			{
				$query = "ALTER TABLE `#__xgroups_memberoption` DROP KEY `idx_gidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__xgroups_memberoption', 'idx_userid'))
			{
				$query = "ALTER TABLE `#__xgroups_memberoption` DROP KEY `idx_userid`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_modules'))
		{
			if ($this->db->tableHasKey('#__xgroups_modules', 'idx_gidNumber'))
			{
				$query = "ALTER TABLE `#__xgroups_modules` DROP KEY `idx_gidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__xgroups_modules', 'idx_state'))
			{
				$query = "ALTER TABLE `#__xgroups_modules` DROP KEY `idx_state`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_pages'))
		{
			if ($this->db->tableHasKey('#__xgroups_pages', 'idx_gidNumber'))
			{
				$query = "ALTER TABLE `#__xgroups_pages` DROP KEY `idx_gidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__xgroups_pages', 'idx_state'))
			{
				$query = "ALTER TABLE `#__xgroups_pages` DROP KEY `idx_state`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_pages_categories'))
		{
			if ($this->db->tableHasKey('#__xgroups_pages_categories', 'idx_gidNumber'))
			{
				$query = "ALTER TABLE `#__xgroups_pages_categories` DROP KEY `idx_gidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_pages_versions'))
		{
			if ($this->db->tableHasKey('#__xgroups_pages_versions', 'idx_pageid'))
			{
				$query = "ALTER TABLE `#__xgroups_pages_versions` DROP KEY `idx_pageid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__xgroups_pages_versions', 'idx_approved'))
			{
				$query = "ALTER TABLE `#__xgroups_pages_versions` DROP KEY `idx_approved`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__xgroups_pages_versions', 'idx_scanned'))
			{
				$query = "ALTER TABLE `#__xgroups_pages_versions` DROP KEY `idx_scanned`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_reasons'))
		{
			if ($this->db->tableHasKey('#__xgroups_reasons', 'idx_gidNumber'))
			{
				$query = "ALTER TABLE `#__xgroups_reasons` DROP KEY `idx_gidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__xgroups_reasons', 'idx_uidNumber'))
			{
				$query = "ALTER TABLE `#__xgroups_reasons` DROP KEY `idx_uidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_roles'))
		{
			if ($this->db->tableHasKey('#__xgroups_roles', 'idx_gidNumber'))
			{
				$query = "ALTER TABLE `#__xgroups_roles` DROP KEY `idx_gidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}