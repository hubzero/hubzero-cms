<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding more details to asset views table
 **/
class Migration20130729084642ComForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__forum_sections', 'ordering'))
		{
			$query = "ALTER TABLE `#__forum_sections` ADD `ordering` INT(11)  NOT NULL  DEFAULT '0'  AFTER `object_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__forum_categories', 'ordering'))
		{
			$query = "ALTER TABLE `#__forum_categories` ADD `ordering` INT(11)  NOT NULL  DEFAULT '0'  AFTER `object_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__forum_sections', 'ordering'))
		{
			$query = "ALTER TABLE `#__forum_sections` DROP `ordering`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__forum_categories', 'ordering'))
		{
			$query = "ALTER TABLE `#__forum_categories` DROP `ordering`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}