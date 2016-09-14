<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting character set on tables to UTF8
 **/
class Migration20160914183602ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__project_owners'))
		{
			$query = "ALTER TABLE `#__project_owners` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_activity'))
		{
			$query = "ALTER TABLE `#__project_activity` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_types'))
		{
			$query = "ALTER TABLE `#__project_types` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__projects'))
		{
			$query = "ALTER TABLE `#__projects` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__project_owners'))
		{
			$query = "ALTER TABLE `#__project_owners` CONVERT TO CHARACTER SET latin1 COLLATE latin1_swedish_ci";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_activity'))
		{
			$query = "ALTER TABLE `#__project_activity` CONVERT TO CHARACTER SET latin1 COLLATE latin1_swedish_ci";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_types'))
		{
			$query = "ALTER TABLE `#__project_types` CONVERT TO CHARACTER SET latin1 COLLATE latin1_swedish_ci";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__projects'))
		{
			$query = "ALTER TABLE `#__projects` CONVERT TO CHARACTER SET latin1 COLLATE latin1_swedish_ci";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
