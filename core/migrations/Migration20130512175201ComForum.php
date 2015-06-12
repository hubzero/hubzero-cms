<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for add watching table
 **/
class Migration20130512175201ComForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableHasField('#__forum_posts', 'thread'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD `thread` int(11) NOT NULL DEFAULT '0';";
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
		$query = "";

		if ($this->db->tableHasField('#__forum_posts', 'thread'))
		{
			$query .= "ALTER TABLE `#__forum_posts` DROP `thread`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}