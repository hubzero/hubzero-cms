<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for add watching table
 **/
class Migration20130507085501ComForum extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";

		if (!$db->tableHasField('#__forum_posts', 'lft'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD `lft` int(11) NOT NULL DEFAULT '0';";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}

		if (!$db->tableHasField('#__forum_posts', 'rgt'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD `rgt` int(11) NOT NULL DEFAULT '0';";
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

		if ($db->tableHasField('#__forum_posts', 'lft'))
		{
			$query .= "ALTER TABLE `#__forum_posts` DROP `lft`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableHasField('#__forum_posts', 'rgt'))
		{
			$query .= "ALTER TABLE `#__forum_posts` DROP `rgt`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}