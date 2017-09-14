<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding 'state' field to resource_types table
 **/
class Migration20170731201800ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resource_types'))
		{
			if (!$this->db->tableHasField('#__resource_types', 'state'))
			{
				$query = "ALTER TABLE `#__resource_types` ADD `state` INT(3) NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__resource_types` SET `state`=1";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__resource_types', 'idx_state'))
			{
				$query = "ALTER IGNORE TABLE `#__resource_types` ADD INDEX `idx_state` (`state`)";
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
		if ($this->db->tableExists('#__resource_types'))
		{
			if ($this->db->tableHasKey('#__resource_types', 'idx_state'))
			{
				$query = "ALTER TABLE `#__resource_types` DROP KEY `idx_state`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__resource_types', 'state'))
			{
				$query = "ALTER TABLE `#__resource_types` DROP COLUMN `state`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
