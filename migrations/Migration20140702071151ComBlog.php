<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding state, modified, modified_by 
 * fields to blog comments
 **/
class Migration20140702071151ComBlog extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__blog_comments', 'state'))
		{
			$query = "ALTER TABLE `#__blog_comments` ADD `state` TINYINT(2)  NOT NULL  DEFAULT '0'";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__blog_comments` SET state=1 WHERE state=0";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "SELECT referenceid FROM `#__abuse_reports` WHERE state=0 AND category IN ('blog', 'blogcomment')";
			$this->db->setQuery($query);
			if ($ids = $this->db->loadResultArray())
			{
				$ids = array_map('intval', $ids);

				$query = "UPDATE `#__blog_comments` SET state=3 WHERE id IN (" . implode(',', $ids) . ")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if (!$this->db->tableHasField('#__blog_comments', 'modified'))
		{
			$query = "ALTER TABLE `#__blog_comments` ADD `modified` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00'";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__blog_comments', 'modified_by'))
		{
			$query = "ALTER TABLE `#__blog_comments` ADD `modified_by` INT(11)  NOT NULL  DEFAULT '0'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__blog_comments', 'state'))
		{
			$query = "ALTER TABLE `#__blog_comments` DROP `state`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__blog_comments', 'modified'))
		{
			$query = "ALTER TABLE `#__blog_comments` DROP `modified`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__blog_comments', 'modified_by'))
		{
			$query = "ALTER TABLE `#__blog_comments` DROP `modified_by`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}