<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for tracking total viewing time in media tracking table
 **/
class Migration20130731203037ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__media_tracking', 'total_viewing_time'))
		{
			$query = "ALTER TABLE `#__media_tracking` ADD COLUMN `total_viewing_time` int(11) DEFAULT 0;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__media_tracking', 'total_viewing_time'))
		{
			$query = "ALTER TABLE `#__media_tracking` DROP `total_viewing_time`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}