<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130329000000ComCourses extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__courses_member_notes` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`scope` varchar(255) NOT NULL DEFAULT '',
			`scope_id` int(11) NOT NULL DEFAULT '0',
			`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`created_by` int(11) NOT NULL DEFAULT '0',
			`content` mediumtext NOT NULL,
			`pos_x` int(11) NOT NULL DEFAULT '0',
			`pos_y` int(11) NOT NULL DEFAULT '0',
			`width` int(11) NOT NULL DEFAULT '0',
			`height` int(11) NOT NULL DEFAULT '0',
			`state` tinyint(2) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($query);
		$db->query();

		self::addPluginEntry('courses', 'notes');
	}

	protected static function down($db)
	{
		$query = "DROP TABLE IF EXISTS `#__courses_member_notes`;";
		$db->setQuery($query);
		$db->query();

		self::deletePluginEntry('courses', 'notes');
	}
}