<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for renaming fulltext index on #__blog_entries
 **/
class Migration20140822154900ComBlog extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__blog_entries'))
		{
			if ($this->db->tableHasKey('#__blog_entries', 'jos_blog_entries_title_content_ftidx'))
			{
				$query = "ALTER TABLE `#__blog_entries` DROP INDEX `jos_blog_entries_title_content_ftidx`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__blog_entries', 'ftidx_title_content'))
			{
				$query = "ALTER TABLE `#__blog_entries` ADD FULLTEXT `ftidx_title_content` (`title`, `content`);";
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
		if ($this->db->tableExists('#__blog_entries'))
		{
			if ($this->db->tableHasKey('#__blog_entries', 'ftidx_title_content'))
			{
				$query = "ALTER TABLE `#__blog_entries` DROP INDEX `ftidx_title_content`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__blog_entries', 'jos_blog_entries_title_content_ftidx'))
			{
				$query = "ALTER TABLE `#__blog_entries` ADD FULLTEXT `jos_blog_entries_title_content_ftidx` (`title`, `content`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}