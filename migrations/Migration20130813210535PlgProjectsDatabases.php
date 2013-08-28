<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for setting up databases projects plugin
 **/
class Migration20130813210535PlgProjectsDatabases extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableExists('#__project_databases'))
		{
			$query = "CREATE TABLE `#__project_databases` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`project` int(11) NOT NULL,
						`database_name` varchar(64) NOT NULL,
						`title` varchar(127) NOT NULL DEFAULT '',
						`source_file` varchar(127) NOT NULL,
						`source_dir` varchar(127) NOT NULL,
						`source_revision` varchar(56) NOT NULL,
						`description` text,
						`data_definition` text,
						`revision` int(11) DEFAULT NULL,
						`created` datetime DEFAULT NULL,
						`created_by` int(11) DEFAULT NULL,
						`updated` datetime DEFAULT NULL,
						`updated_by` int(11) DEFAULT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$db->setQuery($query);
			$db->query();
		}

		if (!$db->tableExists('#__project_database_versions'))
		{
			$query = "CREATE TABLE `#__project_database_versions` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`database_name` varchar(64) NOT NULL,
						`version` int(11) NOT NULL DEFAULT '1',
						`data_definition` text,
						PRIMARY KEY (`id`,`database_name`,`version`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$db->setQuery($query);
			$db->query();
		}

		self::addPluginEntry('projects', 'databases');
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists('#__project_databases'))
		{
			$query = "DROP TABLE `#__project_databases`";

			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableExists('#__project_database_versions'))
		{
			$query = "DROP TABLE `#__project_database_versions`;";

			$db->setQuery($query);
			$db->query();
		}

		self::deletePluginEntry('projects', 'databases');
	}
}