<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130826150814ComResources extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__media_tracking_detailed` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(11) DEFAULT NULL,
				`session_id` varchar(200) DEFAULT NULL,
				`ip_address` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
				`object_id` int(11) DEFAULT NULL,
				`object_type` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
				`object_duration` int(11) DEFAULT NULL,
				`current_position` int(11) DEFAULT NULL,
				`farthest_position` int(11) DEFAULT NULL,
				`current_position_timestamp` datetime DEFAULT NULL,
				`farthest_position_timestamp` datetime DEFAULT NULL,
				`completed` int(11) DEFAULT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

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
		$query = "DROP TABLE `#__media_tracking_detailed`";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}