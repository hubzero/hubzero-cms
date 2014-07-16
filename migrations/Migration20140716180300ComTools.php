<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding unique constraint to tool version zone
 **/
class Migration20140716180300ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__tool_version_zone') && !$this->db->tableHasKey('#__tool_version_zone', 'idx_zoneid_toolversionid'))
		{
			$query = "ALTER TABLE `#__tool_version_zone` ADD CONSTRAINT UNIQUE KEY `idx_zoneid_toolversionid`(zone_id, tool_version_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__tool_version_zone') && $this->db->tableHasKey('#__tool_version_zone', 'idx_zoneid_toolversionid'))
		{
			$query = "ALTER TABLE `#__tool_version_zone` DROP KEY `idx_zoneid_toolversionid`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}