<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for add watching table
 **/
class Migration20130507085501ComForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableHasField('#__forum_posts', 'lft'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD `lft` int(11) NOT NULL DEFAULT '0';\n";
		}

		if (!$this->db->tableHasField('#__forum_posts', 'rgt'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD `rgt` int(11) NOT NULL DEFAULT '0';";
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

		if ($this->db->tableHasField('#__forum_posts', 'lft'))
		{
			$query .= "ALTER TABLE `#__forum_posts` DROP `lft`;\n";
		}

		if ($this->db->tableHasField('#__forum_posts', 'rgt'))
		{
			$query .= "ALTER TABLE `#__forum_posts` DROP `rgt`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}