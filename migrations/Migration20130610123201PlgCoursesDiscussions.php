<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing wrong datatype on column
 **/
class Migration20130610123201PlgCoursesDiscussions extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";

		if (!$db->tableHasField('#__forum_posts', 'scope_sub_id'))
		{
			$query = "ALTER TABLE `#__forum_posts` ADD `scope_sub_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `scope_id`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "";

		if ($db->tableHasField('#__forum_posts', 'scope_sub_id'))
		{
			$query .= "ALTER TABLE `#__forum_posts` DROP `scope_sub_id`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}