<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing wrong datatype on column
 **/
class Migration20130617171001ComForum extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";

		if (!$db->tableHasField('#__forum_posts', 'closed'))
		{
			$query = "ALTER TABLE `#__forum_posts` ADD `closed` TINYINT(2)  NOT NULL  DEFAULT '0';";
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

		if ($db->tableHasField('#__forum_posts', 'closed'))
		{
			$query .= "ALTER TABLE `#__forum_posts` DROP `closed`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}