<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for dropping unused #__tags_group table
 **/
class Migration20190902000001ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__tags_object'))
		{
			$query = "ALTER TABLE `#__tags_object` ADD UNIQUE INDEX unique_tag_per_obj (objectid, tagid, tbl)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Up
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__tags_object'))
		{
			$query = "ALTER TABLE `#__tags_object` DROP INDEX unique_tag_per_obj";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
