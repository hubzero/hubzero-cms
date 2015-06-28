<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for resource changes needed by com_hubgraph
 **/
class Migration20141112203716ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resource_assoc') && !$this->db->tableHasField('#__resource_assoc', 'id'))
		{
			$query = "ALTER TABLE `#__resource_assoc` ADD COLUMN `id` SERIAL NOT NULL PRIMARY KEY FIRST";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__author_assoc') && !$this->db->tableHasField('#__author_assoc', 'id'))
		{
			$query = "ALTER TABLE `#__author_assoc` DROP PRIMARY KEY";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "CREATE UNIQUE INDEX uidx_subtable_subid_authorid ON `#__author_assoc`(subtable, subid, authorid)";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "ALTER TABLE `#__author_assoc` ADD COLUMN `id` SERIAL NOT NULL PRIMARY KEY FIRST";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}