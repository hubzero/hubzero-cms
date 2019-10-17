<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for changing the data_definition field type to accommodate
 * longer strings needed for files with many fields
 **/
class Migration20191017000001ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__project_databases') && $this->db->tableHasField('#__project_databases', 'data_definition'))
		{
			$query = "ALTER TABLE `#__project_databases` MODIFY `data_definition` MEDIUMTEXT";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__project_databases') && $this->db->tableHasField('#__project_databases', 'data_definition'))
		{
			$query = "ALTER TABLE `#__project_databases` MODIFY `data_definition` TEXT";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
