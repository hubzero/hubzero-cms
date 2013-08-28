<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding com cron component
 **/
class Migration20130426072033ComCron extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableExists('#__cron_jobs'))
		{
			$query = "CREATE TABLE `#__cron_jobs` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`title` varchar(255) NOT NULL DEFAULT '',
						`state` tinyint(3) NOT NULL DEFAULT '0',
						`plugin` varchar(255) NOT NULL DEFAULT '',
						`event` varchar(255) NOT NULL DEFAULT '',
						`last_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
						`next_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
						`recurrence` varchar(50) NOT NULL DEFAULT '',
						`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
						`created_by` int(11) NOT NULL DEFAULT '0',
						`modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
						`modified_by` int(11) NOT NULL DEFAULT '0',
						`active` tinyint(3) NOT NULL DEFAULT '0',
						`ordering` int(11) NOT NULL DEFAULT '0',
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
			$db->setQuery($query);
			$db->query();
		}

		self::addComponentEntry('Cron');
		self::addPluginEntry('cron', 'support');
		self::addPluginEntry('cron', 'members');
		self::addPluginEntry('cron', 'cache');
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists('#__cron_jobs'))
		{
			$query = "DROP TABLE `#__cron_jobs`;";
			$db->setQuery($query);
			$db->query();
		}

		self::deleteComponentEntry('Cron');
		self::deletePluginEntry('cron');
	}
}