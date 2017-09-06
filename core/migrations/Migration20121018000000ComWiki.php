<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding modified, version_id, and length fields to wiki table
 **/
class Migration20121018000000ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__wiki_page'))
		{
			if (!$this->db->tableHasField('#__wiki_page', 'modified'))
			{
				$query = "ALTER TABLE `#__wiki_page` ADD `modified` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00'  AFTER `state`";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__wiki_page', 'version_id'))
			{
				$query = "ALTER TABLE `#__wiki_page` ADD `version_id` INT(11)  NOT NULL  DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_version'))
		{
			if (!$this->db->tableHasField('#__wiki_version', 'length'))
			{
				$query = "ALTER TABLE `#__wiki_version` ADD `length` INT(11)  NOT NULL  DEFAULT '0'";
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
		if ($this->db->tableExists('#__wiki_page'))
		{
			if ($this->db->tableHasField('#__wiki_page', 'modified'))
			{
				$query = "ALTER TABLE `#__wiki_page` DROP COLUMN `modified`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__wiki_page', 'version_id'))
			{
				$query = "ALTER TABLE `#__wiki_page` DROP COLUMN `version_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_version'))
		{
			if ($this->db->tableHasField('#__wiki_version', 'length'))
			{
				$query = "ALTER TABLE `#__wiki_version` DROP COLUMN `length`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
