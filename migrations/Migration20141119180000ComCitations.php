<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script registering the plugin
 **/
class Migration20141119180000ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		//checks whether table exists and if the 'scope' field already exists
		if ($this->db->tableExists('#__citations_secondary')
				&& !$this->db->tableHasField('#__citations_secondary', 'link1_url'))
		{
			$query = "ALTER TABLE `#__citations_secondary` 
			ADD COLUMN `link1_url` TINYTEXT NULL DEFAULT NULL AFTER `search_string`,
			ADD COLUMN `link1_title` VARCHAR(60) NULL DEFAULT NULL AFTER `link1_url`,
			ADD COLUMN `link2_url` TINYTEXT NULL DEFAULT NULL AFTER `link1_title`,
			ADD COLUMN `link2_title` VARCHAR(60) NULL DEFAULT NULL AFTER `link2_url`,
			ADD COLUMN `link3_url` TINYTEXT NULL DEFAULT NULL AFTER `link2_title`,
			ADD COLUMN `link3_title` VARCHAR(60) NULL DEFAULT NULL AFTER `link3_url`;";

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
				&& $this->db->tableHasField('#__citations_secondary', 'link1_url'))
		{
			
			$query = "ALTER TABLE `#__citations_secondary`
			DROP COLUMN `link1_url`,
			DROP COLUMN `link1_title`,
			DROP COLUMN `link2_url`,
			DROP COLUMN `link2_title`,
			DROP COLUMN `link3_url`,
			DROP COLUMN `link3_tile`;";
			
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}