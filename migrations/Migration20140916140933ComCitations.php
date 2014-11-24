<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script registering the plugin
 **/
class Migration20140916140933ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		//checks whether table exists and if the 'scope' field already exists
		if ($this->db->tableExists('#__citations')
			&& !$this->db->tableHasField('#__citations', 'scope'))
		{
			$query = "ALTER TABLE `#__citations` ADD COLUMN `scope` VARCHAR(45) NULL DEFAULT NULL AFTER `gid`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		//checks to see if gid field exists and removes it
		if ($this->db->tableExists('#__citations')
				&& $this->db->tableHasField('#__citations', 'scope'))
		{
			$query = "ALTER TABLE `#__citations` DROP COLUMN `scope`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}