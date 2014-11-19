<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script registering the plugin
 **/
class Migration20141004111111ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		//checks whether table exists and if the 'scope' field already exists
		if ($this->db->tableExists('#__citations_secondary')
				&& !$this->db->tableHasField('#__citations_secondary', 'scope'))
		{
			$query = "ALTER TABLE `#__citations_secondary` 
			ADD COLUMN `scope` VARCHAR(250) NULL DEFAULT NULL AFTER `cid`,
			ADD COLUMN `scope_id` INT(11) NULL DEFAULT NULL AFTER `scope`;";

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
		if ($this->db->tableExists('#__citations_secondary') 
				&& $this->db->tableHasField('#__citations_secondary', 'scope'))
		{
			
			$query = "ALTER TABLE `#__citations_secondary`
			DROP COLUMN `scope`,
			DROP COLUMN `scope_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}