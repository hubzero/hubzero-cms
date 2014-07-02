<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for updating support categories field names
 **/
class Migration20140509133410ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_categories'))
		{
			if (!$this->db->tableHasField('#__support_categories', 'section_id'))
			{
				$query = "ALTER TABLE `#__support_categories` CHANGE `section` `section_id` INT(11)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__support_categories', 'category'))
			{
				$query = "ALTER TABLE `#__support_categories` CHANGE `category` `alias` VARCHAR(250)  NOT NULL  DEFAULT '';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__support_categories', 'title'))
			{
				$query = "ALTER TABLE `#__support_categories` ADD `title` VARCHAR(255)  NOT NULL  DEFAULT '';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__support_categories', 'created'))
			{
				$query = "ALTER TABLE `#__support_categories` ADD `created` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__support_categories', 'created_by'))
			{
				$query = "ALTER TABLE `#__support_categories` ADD `created_by` INT(11)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__support_categories', 'modified'))
			{
				$query = "ALTER TABLE `#__support_categories` ADD `modified` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__support_categories', 'modified_by'))
			{
				$query = "ALTER TABLE `#__support_categories` ADD `modified_by` INT(11)  NOT NULL  DEFAULT '0';";
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
		if ($this->db->tableExists('#__support_categories'))
		{
			if ($this->db->tableHasField('#__support_categories', 'section_id'))
			{
				$query = "ALTER TABLE `#__support_categories` CHANGE `section_id` `section` INT(11)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__support_categories', 'alias'))
			{
				$query = "ALTER TABLE `#__support_categories` CHANGE `alias` `category` VARCHAR(50)  NOT NULL  DEFAULT '';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__support_categories', 'title'))
			{
				$query = "ALTER TABLE `#__support_categories` DROP `title`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__support_categories', 'created'))
			{
				$query = "ALTER TABLE `#__support_categories` DROP `created`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__support_categories', 'created_by'))
			{
				$query = "ALTER TABLE `#__support_categories` DROP `created_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__support_categories', 'modified'))
			{
				$query = "ALTER TABLE `#__support_categories` DROP `modified`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__support_categories', 'modified_by'))
			{
				$query = "ALTER TABLE `#__support_categories` DROP `modified_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}