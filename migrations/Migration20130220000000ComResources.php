<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130220000000ComResources extends Hubzero_Migration
{
	protected static $up = "CREATE TABLE IF NOT EXISTS `#__media_tracking` (
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
				`total_views` int(11) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

	protected static $down = "DROP TABLE IF EXISTS `#__media_tracking`;";

	protected static function up($db)
	{
		$db->setQuery(self::$up);
		$db->query();
	}

	protected static function down($db)
	{
		$db->setQuery(self::$up);
		$db->query();
	}
}
