<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding length and effort fields to courses. Fixes fulltext index name.
 **/
class Migration20141112142733ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__courses'))
		{
			if (!$this->db->tableHasField('#__courses', 'length'))
			{
				$query = "ALTER TABLE `#__courses` ADD `length` VARCHAR(255) NULL  DEFAULT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__courses', 'effort'))
			{
				$query = "ALTER TABLE `#__courses` ADD `effort` VARCHAR(255) NULL  DEFAULT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__courses', 'jos_xgroups_cn_description_public_desc_ftidx'))
			{
				$query = "ALTER TABLE `#__courses` DROP INDEX `jos_xgroups_cn_description_public_desc_ftidx`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__courses', 'ftidx_alias_title_blurb'))
			{
				$query = "ALTER TABLE `#__courses` ADD FULLTEXT `ftidx_alias_title_blurb` (`alias`, `title`, `blurb`);";
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
		if ($this->db->tableExists('#__courses'))
		{
			if ($this->db->tableHasField('#__courses', 'length'))
			{
				$query = "ALTER TABLE `#__courses` DROP `length`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__courses', 'effort'))
			{
				$query = "ALTER TABLE `#__courses` DROP `effort`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}