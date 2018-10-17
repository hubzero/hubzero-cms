<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ...
 **/
class Migration20180506000000ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__focus_areas') && !$this->db->tableHasField('#__focus_areas', 'label'))
		{
			$query = "ALTER TABLE `#__focus_areas` ADD COLUMN `label` varchar(255) DEFAULT '' AFTER `tag_id`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__focus_areas') && !$this->db->tableHasField('#__focus_areas', 'about'))
		{
			$query = "ALTER TABLE `#__focus_areas` ADD COLUMN `about` text DEFAULT '' AFTER `label`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__focus_areas') && $this->db->tableHasField('#__focus_areas', 'label'))
		{
			$query = "ALTER TABLE `#__focus_areas` DROP COLUMN `label`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__focus_areas') && $this->db->tableHasField('#__focus_areas', 'about'))
		{
			$query = "ALTER TABLE `#__focus_areas` DROP COLUMN `about`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
