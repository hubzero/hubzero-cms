<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding missing index to #__collections_items table
 **/
class Migration20160418114900ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__collections_items'))
		{
			if (!$this->db->tableHasKey('#__collections_items', 'idx_type_object_id'))
			{
				$query = "ALTER TABLE `#__collections_items` ADD INDEX `idx_type_object_id` (`type`, `object_id`);";
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
		if ($this->db->tableExists('#__collections_items'))
		{
			if ($this->db->tableHasKey('#__collections_items', 'idx_type_object_id'))
			{
				$query = "ALTER TABLE `#__collections_items` DROP INDEX `idx_type_object_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
