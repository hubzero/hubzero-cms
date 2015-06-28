<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for changing data type of asset group description field
 **/
class Migration20131011164749ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__courses_asset_groups') && $this->db->tableHasField('#__courses_asset_groups', 'description'))
		{
			$query = "ALTER TABLE `#__courses_asset_groups` CHANGE `description` `description` TEXT  CHARACTER SET utf8  NOT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__courses_asset_groups') && $this->db->tableHasField('#__courses_asset_groups', 'description'))
		{
			$query = "ALTER TABLE `#__courses_asset_groups` CHANGE `description` `description` VARCHAR(255)  CHARACTER SET utf8  NOT NULL  DEFAULT ''";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}