<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140212162812ComCourses extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableExists('#__courses_asset_unity'))
		{
			$query = "CREATE TABLE `#__courses_asset_unity` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `member_id` int(11) NOT NULL,
					  `asset_id` int(11) NOT NULL,
					  `created` datetime NOT NULL,
					  `passed` tinyint(1) NOT NULL,
					  `details` text,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8";

			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists('#__courses_asset_unity'))
		{
			$query = "DROP TABLE `#__courses_asset_unity`";

			$db->setQuery($query);
			$db->query();
		}
	}
}