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

		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "INSERT INTO `jos_plugins`(`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
					SELECT 'Projects - Databases', 'databases', 'projects', 0, 5, 0, 0, 0, 0, NULL, ''
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_plugins` WHERE name = 'Projects - Databases');";
		}
		else
		{
			$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)
					SELECT 'plg_projects_databases', 'plugin', 'databases', 'projects', 0, 1, 1, 0, null, null, null, null, 0, '0000-00-00 00:00:00', 0, 0
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__extensions` WHERE name = 'plg_projects_databases');";
		}

		$db->setQuery($query);
		$db->query();
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

		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "DELETE FROM `#__plugins` WHERE folder='projects' AND element='databases';";
		}
		else
		{
			$query = "DELETE FROM `#__extensions` WHERE type='plugin' AND folder='projects' AND element='databases';";
		}

		$db->setQuery($query);
		$db->query();
	}
}