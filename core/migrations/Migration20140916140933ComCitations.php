<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding scope column to #__citations table
 **/
class Migration20140916140933ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Checks whether table exists and if the 'scope' field already exists
		if ($this->db->tableExists('#__citations') && !$this->db->tableHasField('#__citations', 'scope'))
		{
			$query = "ALTER TABLE `#__citations` ADD COLUMN `scope` VARCHAR(45) NULL DEFAULT NULL;";
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
		if ($this->db->tableExists('#__citations') && $this->db->tableHasField('#__citations', 'scope'))
		{
			$query = "ALTER TABLE `#__citations` DROP COLUMN `scope`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}