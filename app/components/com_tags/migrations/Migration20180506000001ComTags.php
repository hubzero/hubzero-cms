<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ...
 **/
class Migration20180506000001ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__tags_object') && !$this->db->tableHasField('#__tags_object', 'ordering'))
		{
			$query = "ALTER TABLE `#__tags_object` ADD COLUMN `ordering` int(11) DEFAULT NULL AFTER `label`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__tags_object') && $this->db->tableHasField('#__tags_object', 'ordering'))
		{
			$query = "ALTER TABLE `#__tags_object` DROP COLUMN `ordering`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
