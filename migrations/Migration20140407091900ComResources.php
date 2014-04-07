<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding indices
 **/
class Migration20140407091900ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resource_assoc'))
		{
			if (!$this->db->tableHasKey('#__resource_assoc', 'idx_parent_id_child_id'))
			{
				$query = "ALTER TABLE `#__resource_assoc` ADD INDEX `idx_parent_id_child_id` (`parent_id`, `child_id`);";
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
		if ($this->db->tableExists('#__resource_assoc'))
		{
			if ($this->db->tableHasKey('#__resource_assoc', 'idx_parent_id_child_id'))
			{
				$query = "ALTER TABLE `#__resource_assoc` DROP INDEX `idx_parent_id_child_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}