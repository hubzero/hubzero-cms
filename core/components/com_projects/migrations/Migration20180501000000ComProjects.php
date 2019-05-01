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
 * Migration script for adding several indexes to projects table
 **/
class Migration20180501000000ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__projects'))
		{
			if (!$this->db->tableHasKey('#__projects', 'idx_owned_by_group'))
			{
				$query = "ALTER TABLE `#__projects` ADD INDEX `idx_owned_by_group` (`owned_by_group`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__projects', 'idx_owned_by_user'))
			{
				$query = "ALTER TABLE `#__projects` ADD INDEX `idx_owned_by_user` (`owned_by_user`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__projects', 'idx_private'))
			{
				$query = "ALTER TABLE `#__projects` ADD INDEX `idx_private` (`private`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__projects', 'idx_state'))
			{
				$query = "ALTER TABLE `#__projects` ADD INDEX `idx_state` (`state`);";
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
		if ($this->db->tableExists('#__projects'))
		{
			if ($this->db->tableHasKey('#__projects', 'idx_owned_by_group'))
			{
				$query = "ALTER TABLE `#__projects` DROP INDEX `idx_owned_by_group`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__projects', 'idx_owned_by_user'))
			{
				$query = "ALTER TABLE `#__projects` DROP INDEX `idx_owned_by_user`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__projects', 'idx_private'))
			{
				$query = "ALTER TABLE `#__projects` DROP INDEX `idx_private`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__projects', 'idx_state'))
			{
				$query = "ALTER TABLE `#__projects` DROP INDEX `idx_state`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
