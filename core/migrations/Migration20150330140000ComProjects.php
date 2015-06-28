<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding preview field to project activity table
 **/
class Migration20150330140000ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__project_activity'))
		{
			if (!$this->db->tableHasField('#__project_activity', 'preview'))
			{
				$query = "ALTER TABLE `#__project_activity` ADD COLUMN preview text DEFAULT '';";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}