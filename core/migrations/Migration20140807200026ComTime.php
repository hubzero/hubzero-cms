<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding asset_id field to time hubs table
 **/
class Migration20140807200026ComTime extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__time_hubs') && !$this->db->tableHasField('#__time_hubs', 'asset_id'))
		{
			$query = "ALTER TABLE `#__time_hubs` ADD `asset_id` INT(11) NULL DEFAULT NULL AFTER `notes`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__time_hubs') && $this->db->tableHasField('#__time_hubs', 'asset_id'))
		{
			$query = "ALTER TABLE `#__time_hubs` DROP `asset_id`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}