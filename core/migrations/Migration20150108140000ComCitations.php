<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding custom fields to citations
 **/
class Migration20150108140000ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		//checks whether table exists and if the 'scope' field already exists
		if ($this->db->tableExists('#__citations') && !$this->db->tableHasField('#__citations', 'custom1'))
		{
			$query = "ALTER TABLE `#__citations`
			ADD COLUMN `custom1` TEXT NULL DEFAULT NULL,
			ADD COLUMN `custom2` TEXT NULL DEFAULT NULL,
			ADD COLUMN `custom3` VARCHAR(45) NULL DEFAULT NULL,
			ADD COLUMN `custom4` VARCHAR(45) NULL DEFAULT NULL";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// Checks to see if gid field exists and removes it
		if ($this->db->tableExists('#__citations') && $this->db->tableHasField('#__citations', 'custom1'))
		{
			$query = "ALTER TABLE `#__citations`
			DROP COLUMN `custom1`,
			DROP COLUMN `custom2`,
			DROP COLUMN `custom3`,
			DROP COLUMN `custom4`";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}