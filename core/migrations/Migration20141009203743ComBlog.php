<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing several blog field data types
 **/
class Migration20141009203743ComBlog extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__blog_comments') && $this->db->tableHasField('#__blog_comments', 'anonymous'))
		{
			$info = $this->db->getTableColumns('#__blog_comments', false);

			if ($info['anonymous']->Type != "tinyint(2) unsigned")
			{
				$query = "ALTER TABLE `#__blog_comments` CHANGE COLUMN `anonymous` `anonymous` TINYINT(2) UNSIGNED NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__blog_entries') && $this->db->tableHasField('#__blog_entries', 'created'))
		{
			$info = $this->db->getTableColumns('#__blog_entries', false);

			if ($info['created']->Null != "NO")
			{
				$query = "ALTER TABLE `#__blog_entries` CHANGE COLUMN `created` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}