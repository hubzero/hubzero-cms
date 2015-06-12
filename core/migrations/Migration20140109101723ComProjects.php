<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script to change column type to TEXT to allow extended blog entry
 **/
class Migration20140109101723ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "ALTER TABLE `#__project_microblog` MODIFY `blogentry` TEXT DEFAULT NULL;";
		$this->db->setQuery($query);
		$this->db->query();
	}
}