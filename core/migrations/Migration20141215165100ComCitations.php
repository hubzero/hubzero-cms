<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding scope_id column to #__citations table
 **/
class Migration20141215165100ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Checks whether table exists and if the 'scope' field already exists
		if ($this->db->tableExists('#__citations') && $this->db->tableHasField('#__citations', 'gid'))
		{
			$query = "ALTER TABLE `#__citations` CHANGE COLUMN `gid` `scope_id` VARCHAR(45) NULL DEFAULT NULL AFTER `scope`;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// Checks to see if field exists and removes it
		if ($this->db->tableExists('#__citations') && !!$this->db->tableHasField('#__citations', 'gid'))
		{
			$query = "ALTER TABLE `#__citations` CHANGE COLUMN `scope_id` `gid` VARCHAR(45) NULL DEFAULT NULL AFTER `scope`;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
