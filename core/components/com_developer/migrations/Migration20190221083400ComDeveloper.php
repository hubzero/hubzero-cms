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
 * Migration script for adding indices to com_developer tables
 **/
class Migration20190221083400ComDeveloper extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__developer_access_tokens'))
		{
			if (!$this->db->tableHasKey('#__developer_access_tokens', 'idx_application_id'))
			{
				$query = "ALTER TABLE `#__developer_access_tokens` ADD INDEX `idx_application_id` (`application_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__developer_access_tokens', 'idx_state'))
			{
				$query = "ALTER TABLE `#__developer_access_tokens` ADD INDEX `idx_state` (`state`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__developer_access_tokens', 'idx_uidNumber'))
			{
				$query = "ALTER TABLE `#__developer_access_tokens` ADD INDEX `idx_uidNumber` (`uidNumber`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__developer_application_team_members'))
		{
			if (!$this->db->tableHasKey('#__developer_application_team_members', 'idx_application_id'))
			{
				$query = "ALTER TABLE `#__developer_application_team_members` ADD INDEX `idx_application_id` (`application_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__developer_application_team_members', 'idx_uidNumber'))
			{
				$query = "ALTER TABLE `#__developer_application_team_members` ADD INDEX `idx_uidNumber` (`uidNumber`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__developer_applications'))
		{
			if (!$this->db->tableHasKey('#__developer_applications', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__developer_applications` ADD INDEX `idx_created_by` (`created_by`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__developer_applications', 'idx_state'))
			{
				$query = "ALTER TABLE `#__developer_applications` ADD INDEX `idx_state` (`state`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__developer_applications', 'idx_hub_account'))
			{
				$query = "ALTER TABLE `#__developer_applications` ADD INDEX `idx_hub_account` (`hub_account`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__developer_authorization_codes'))
		{
			if (!$this->db->tableHasKey('#__developer_authorization_codes', 'idx_uidNumber'))
			{
				$query = "ALTER TABLE `#__developer_authorization_codes` ADD INDEX `idx_uidNumber` (`uidNumber`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__developer_authorization_codes', 'idx_application_id'))
			{
				$query = "ALTER TABLE `#__developer_authorization_codes` ADD INDEX `idx_application_id` (`application_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__developer_rate_limit'))
		{
			if (!$this->db->tableHasKey('#__developer_rate_limit', 'idx_uidNumber'))
			{
				$query = "ALTER TABLE `#__developer_rate_limit` ADD INDEX `idx_uidNumber` (`uidNumber`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__developer_rate_limit', 'idx_application_id'))
			{
				$query = "ALTER TABLE `#__developer_rate_limit` ADD INDEX `idx_application_id` (`application_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__developer_refresh_tokens'))
		{
			if (!$this->db->tableHasKey('#__developer_refresh_tokens', 'idx_uidNumber'))
			{
				$query = "ALTER TABLE `#__developer_refresh_tokens` ADD INDEX `idx_uidNumber` (`uidNumber`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__developer_refresh_tokens', 'idx_application_id'))
			{
				$query = "ALTER TABLE `#__developer_refresh_tokens` ADD INDEX `idx_application_id` (`application_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__developer_refresh_tokens', 'idx_state'))
			{
				$query = "ALTER TABLE `#__developer_refresh_tokens` ADD INDEX `idx_state` (`state`)";
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
		if ($this->db->tableExists('#__developer_access_tokens'))
		{
			if ($this->db->tableHasKey('#__developer_access_tokens', 'idx_application_id'))
			{
				$query = "ALTER TABLE `#___developer_access_tokens` DROP KEY `idx_application_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__developer_access_tokens', 'idx_state'))
			{
				$query = "ALTER TABLE `#___developer_access_tokens` DROP KEY `idx_state`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__developer_access_tokens', 'idx_uidNumber'))
			{
				$query = "ALTER TABLE `#___developer_access_tokens` DROP KEY `idx_uidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__developer_application_team_members'))
		{
			if ($this->db->tableHasKey('#__developer_application_team_members', 'idx_application_id'))
			{
				$query = "ALTER TABLE `#___developer_application_team_members` DROP KEY `idx_application_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__developer_application_team_members', 'idx_uidNumber'))
			{
				$query = "ALTER TABLE `#___developer_application_team_members` DROP KEY `idx_uidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__developer_applications'))
		{
			if ($this->db->tableHasKey('#__developer_applications', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#___developer_applications` DROP KEY `idx_created_by`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__developer_applications', 'idx_state'))
			{
				$query = "ALTER TABLE `#___developer_applications` DROP KEY `idx_state`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__developer_applications', 'idx_hub_account'))
			{
				$query = "ALTER TABLE `#___developer_applications` DROP KEY `idx_hub_account`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__developer_authorization_codes'))
		{
			if ($this->db->tableHasKey('#__developer_authorization_codes', 'idx_uidNumber'))
			{
				$query = "ALTER TABLE `#___developer_authorization_codes` DROP KEY `idx_uidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__developer_authorization_codes', 'idx_application_id'))
			{
				$query = "ALTER TABLE `#___developer_authorization_codes` DROP KEY `idx_application_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__developer_rate_limit'))
		{
			if ($this->db->tableHasKey('#__developer_rate_limit', 'idx_uidNumber'))
			{
				$query = "ALTER TABLE `#___developer_rate_limit` DROP KEY `idx_uidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__developer_rate_limit', 'idx_application_id'))
			{
				$query = "ALTER TABLE `#___developer_rate_limit` DROP KEY `idx_application_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__developer_refresh_tokens'))
		{
			if ($this->db->tableHasKey('#__developer_refresh_tokens', 'idx_uidNumber'))
			{
				$query = "ALTER TABLE `#___developer_refresh_tokens` DROP KEY `idx_uidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__developer_refresh_tokens', 'idx_application_id'))
			{
				$query = "ALTER TABLE `#___developer_refresh_tokens` DROP KEY `idx_application_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__developer_refresh_tokens', 'idx_state'))
			{
				$query = "ALTER TABLE `#___developer_refresh_tokens` DROP KEY `idx_state`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
