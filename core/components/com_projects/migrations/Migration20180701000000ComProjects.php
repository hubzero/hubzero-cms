<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding featured column
 **/
class Migration20180701000000ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__projects'))
		{
			if (!$this->db->tableHasField('#__projects', 'featured'))
			{
				$query = "ALTER TABLE `#__projects` ADD COLUMN `featured` TINYINT(2) UNSIGNED  NOT NULL  DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__projects', 'idx_featured'))
			{
				$query = "ALTER TABLE `#__projects` ADD INDEX `idx_featured` (`featured`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__projects'))
		{
			if ($this->db->tableHasKey('#__projects', 'idx_featured'))
			{
				$query = "ALTER TABLE `#__projects` DROP KEY `idx_featured`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__projects', 'featured'))
			{
				$query = "ALTER TABLE `#__projects` DROP COLUMN `featured`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
