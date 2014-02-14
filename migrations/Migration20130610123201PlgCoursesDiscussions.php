<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing wrong datatype on column
 **/
class Migration20130610123201PlgCoursesDiscussions extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableHasField('#__forum_posts', 'scope_sub_id'))
		{
			$query = "ALTER TABLE `#__forum_posts` ADD `scope_sub_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `scope_id`;";
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

		if ($this->db->tableHasField('#__forum_posts', 'scope_sub_id'))
		{
			$query .= "ALTER TABLE `#__forum_posts` DROP `scope_sub_id`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}