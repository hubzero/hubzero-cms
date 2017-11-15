<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding object id to forum tables
 **/
class Migration20121217000000ComForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = '';

		if ($this->db->tableExists('#__forum_sections') && !$this->db->tableHasField('#__forum_sections', 'object_id'))
		{
			$query .= "ALTER TABLE `#__forum_sections` ADD `object_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `asset_id`;\n";
		}
		if ($this->db->tableExists('#__forum_categories') && !$this->db->tableHasField('#__forum_categories', 'object_id'))
		{
			$query .= "ALTER TABLE `#__forum_categories` ADD `object_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `asset_id`;\n";
		}
		if ($this->db->tableExists('#__forum_posts') && !$this->db->tableHasField('#__forum_posts', 'object_id'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD `object_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `asset_id`;\n";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = '';

		if ($this->db->tableExists('#__forum_sections') && $this->db->tableHasField('#__forum_sections', 'object_id'))
		{
			$query .= "ALTER TABLE `#__forum_sections` DROP `object_id`;\n";
		}
		if ($this->db->tableExists('#__forum_categories') && $this->db->tableHasField('#__forum_categories', 'object_id'))
		{
			$query .= "ALTER TABLE `#__forum_categories` DROP `object_id`;\n";
		}
		if ($this->db->tableExists('#__forum_posts') && $this->db->tableHasField('#__forum_posts', 'object_id'))
		{
			$query .= "ALTER TABLE `#__forum_posts` DROP `object_id`;\n";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
