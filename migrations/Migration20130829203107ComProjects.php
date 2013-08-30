<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for setting up projects
 **/
class Migration20130829203107ComProjects extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$queries = array();

		if (!$db->tableExists('#__projects'))
		{
			// Create #__projects
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__projects` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`alias` varchar(30) NOT NULL DEFAULT '',
				`title` varchar(255) NOT NULL DEFAULT '',
				`picture` varchar(255) DEFAULT '',
				`about` text,
				`state` int(11) NOT NULL DEFAULT '0',
				`type` int(11) NOT NULL DEFAULT '1',
				`provisioned` int(11) NOT NULL DEFAULT '0',
				`private` int(11) NOT NULL DEFAULT '1',
				`created` datetime NOT NULL,
				`modified` datetime DEFAULT NULL,
				`owned_by_user` int(11) NOT NULL DEFAULT '0',
				`created_by_user` int(11) NOT NULL,
				`owned_by_group` int(11) DEFAULT '0',
				`modified_by` int(11) DEFAULT '0',
				`setup_stage` int(11) NOT NULL DEFAULT '0',
				`params` text,
				`admin_notes` text,
				PRIMARY KEY (`id`),
				UNIQUE KEY `alias` (`alias`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";

			// Make entries to enable HUB messaging
			$queries[] = "INSERT INTO `#__xmessage_component` (`component`,`action`,`title`) VALUES ('com_projects','projects_member_added','You were added or invited to a project')";

			$queries[] = "INSERT INTO `#__xmessage_component` (`component`,`action`,`title`) VALUES ('com_projects','projects_new_project_admin','Receive notifications about project(s) you monitor as an admin or reviewer')";

			$queries[] = "INSERT INTO `#__xmessage_component` (`component`,`action`,`title`) VALUES ('com_projects','projects_admin_message','Receive administrative messages about your project(s)')";
		}

		if (!$db->tableExists('#__project_activity'))
		{
			// Create #__project_activity
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_activity` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`projectid` int(11) NOT NULL DEFAULT '0',
				`userid` int(11) NOT NULL DEFAULT '0',
				`referenceid` varchar(255) NOT NULL DEFAULT '0',
				`managers_only` tinyint(2) DEFAULT '0',
				`admin` tinyint(2) DEFAULT '0',
				`commentable` tinyint(2) NOT NULL DEFAULT '0',
				`state` tinyint(2) NOT NULL DEFAULT '0',
				`recorded` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`activity` varchar(255) NOT NULL DEFAULT '',
				`highlighted` varchar(100) NOT NULL DEFAULT '',
				`url` varchar(255) DEFAULT NULL,
				`class` varchar(150) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__project_comments'))
		{
			// Create #__project_comments
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_comments` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`itemid` int(11) NOT NULL DEFAULT '0',
				`comment` text NOT NULL,
				`created` datetime DEFAULT '0000-00-00 00:00:00',
				`created_by` int(11) NOT NULL DEFAULT '0',
				`activityid` int(11) NOT NULL DEFAULT '0',
				`state` tinyint(2) NOT NULL DEFAULT '0',
				`parent_activity` int(11) DEFAULT '0',
				`anonymous` tinyint(2) DEFAULT '0',
				`admin` tinyint(2) DEFAULT '0',
				`tbl` varchar(50) NOT NULL DEFAULT 'blog',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__project_microblog'))
		{
			// Create #__project_microblog
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_microblog` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`blogentry` varchar(255) DEFAULT NULL,
				`posted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`posted_by` int(11) NOT NULL DEFAULT '0',
				`state` tinyint(2) DEFAULT '0',
				`params` tinytext,
				`projectid` int(11) NOT NULL DEFAULT '0',
				`activityid` int(11) NOT NULL DEFAULT '0',
				`managers_only` tinyint(2) DEFAULT '0',
				PRIMARY KEY (`id`),
				FULLTEXT KEY `title` (`blogentry`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__project_owners'))
		{
			// Create #__project_owners
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_owners` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`projectid` int(11) NOT NULL DEFAULT '0',
				`userid` int(11) NOT NULL DEFAULT '0',
				`groupid` int(11) DEFAULT '0',
				`invited_name` varchar(100) DEFAULT NULL,
				`invited_email` varchar(100) DEFAULT NULL,
				`invited_code` varchar(10) DEFAULT NULL,
				`added` datetime NOT NULL,
				`lastvisit` datetime DEFAULT NULL,
				`prev_visit` datetime DEFAULT NULL,
				`status` int(11) NOT NULL DEFAULT '0',
				`num_visits` int(11) NOT NULL DEFAULT '0',
				`role` int(11) NOT NULL DEFAULT '0',
				`native` int(11) NOT NULL DEFAULT '0',
				`params` text,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__project_todo'))
		{
			// Create table #__project_todo
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_todo` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`projectid` int(11) NOT NULL DEFAULT '0',
				`todolist` varchar(255) DEFAULT NULL,
				`created` datetime NOT NULL,
				`duedate` datetime DEFAULT NULL,
				`closed` datetime DEFAULT NULL,
				`created_by` int(11) NOT NULL DEFAULT '0',
				`assigned_to` int(11) DEFAULT '0',
				`closed_by` int(11) DEFAULT '0',
				`priority` int(11) DEFAULT '0',
				`activityid` int(11) NOT NULL DEFAULT '0',
				`state` tinyint(1) NOT NULL DEFAULT '0',
				`milestone` tinyint(1) NOT NULL DEFAULT '0',
				`private` tinyint(1) NOT NULL DEFAULT '0',
				`details` text,
				`content` varchar(255) NOT NULL,
				`color` varchar(20) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__project_types'))
		{
			// Create #__project_types
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_types` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`type` varchar(150) NOT NULL DEFAULT '',
				`description` varchar(255) NOT NULL DEFAULT '',
				`params` text,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";

			$queries[] = "INSERT INTO `#__project_types` (`type`,`description`,`params`) 
							SELECT 'General','Individual or collaborative projects of general nature','apps_dev=0\npublications_public=1\nteam_public=1\nallow_invite=0'
							FROM DUAL WHERE NOT EXISTS (SELECT `type` FROM `#__project_types` WHERE `type` = 'General')";
			$queries[] = "INSERT INTO `#__project_types` (`type`,`description`,`params`) 
							SELECT 'Content publication','Projects created with the purpose to publish data as a resource or a collection of related resources','apps_dev=0\npublications_public=1\nteam_public=1\nallow_invite=0'
							FROM DUAL WHERE NOT EXISTS (SELECT `type` FROM `#__project_types` WHERE `type` = 'Content publication')";
			$queries[] = "INSERT INTO `#__project_types` (`type`,`description`,`params`) 
							SELECT 'Application development','Projects created with the purpose to develop and publish a simulation tool or a code library','apps_dev=1\npublications_public=1\nteam_public=1\nallow_invite=0'
							FROM DUAL WHERE NOT EXISTS (SELECT `type` FROM `#__project_types` WHERE `type` = 'Application development')";
		}

		if (!$db->tableExists('#__project_logs'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_logs` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`projectid` int(11) unsigned NOT NULL DEFAULT '0',
				`userid` int(11) NOT NULL DEFAULT '0',
				`ajax` tinyint(1) DEFAULT '0',
				`owner` int(11) unsigned DEFAULT '0',
				`ip` varchar(15) DEFAULT '0',
				`section` varchar(100) DEFAULT 'general',
				`layout` varchar(100) DEFAULT '',
				`action` varchar(100) DEFAULT '',
				`time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`request_uri` tinytext,
				PRIMARY KEY (`id`),
				KEY `projectid` (`projectid`)
			) ENGINE=MyISAM DEFAULT CHARSET=UTF8";
		}

		if (!$db->tableExists('#__project_stats'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_stats` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`month` int(2) DEFAULT NULL,
				`year` int(2) DEFAULT NULL,
				`week` int(2) DEFAULT NULL,
				`processed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`stats` text,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__project_public_stamps'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_public_stamps` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`stamp` varchar(30) NOT NULL DEFAULT '0',
				`projectid` int(11) NOT NULL DEFAULT '0',
				`listed` tinyint(1) NOT NULL DEFAULT '0',
				`type` varchar(50) NOT NULL DEFAULT 'files',
				`reference` text NOT NULL,
				`expires` datetime DEFAULT NULL,
				`created` datetime DEFAULT NULL,
				`created_by` int(11) DEFAULT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `stamp` (`stamp`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__project_remote_files'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_remote_files` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`projectid` int(11) NOT NULL DEFAULT '0',
				`created_by` int(11) NOT NULL DEFAULT '0',
				`modified_by` int(11) DEFAULT '0',
				`paired` int(11) DEFAULT '0',
				`created` datetime DEFAULT NULL,
				`modified` datetime DEFAULT NULL,
				`synced` datetime DEFAULT NULL,
				`local_path` varchar(255) NOT NULL,
				`original_path` varchar(255) NOT NULL,
				`original_format` varchar(200) NOT NULL,
				`local_dirpath` varchar(255) NOT NULL DEFAULT '',
				`local_format` varchar(200) DEFAULT NULL,
				`local_md5` varchar(32) DEFAULT NULL,
				`service` varchar(50) NOT NULL,
				`type` varchar(25) NOT NULL DEFAULT 'file',
				`remote_editing` tinyint(1) NOT NULL DEFAULT '0',
				`remote_id` varchar(100) NOT NULL,
				`original_id` varchar(100) NOT NULL,
				`remote_parent` varchar(100) DEFAULT NULL,
				`remote_title` varchar(140) DEFAULT NULL,
				`remote_md5` varchar(32) DEFAULT NULL,
				`remote_format` varchar(200) DEFAULT NULL,
				`remote_author` varchar(100) DEFAULT NULL,
				`remote_modified` datetime DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (count($queries) > 0)
		{
			// Run queries
			foreach ($queries as $query)
			{
				$db->setQuery($query);
				$db->query();
			}
		}

		$componentParams = array(
			"component_on" => "0",
			"grantinfo" => "0",
			"confirm_step" => "0",
			"edit_settings" => "1",
			"restricted_data" => "0",
			"restricted_upfront" => "0",
			"approve_restricted" => "0",
			"privacylink" => "/legal/privacy",
			"HIPAAlink" => "/legal/privacy",
			"FERPAlink" => "/legal/privacy",
			"creatorgroup" => "",
			"admingroup" => "projectsadmin",
			"sdata_group" => "hipaa_reviewers",
			"ginfo_group" => "sps_reviewers",
			"min_name_length" => "6",
			"max_name_length" => "25",
			"reserved_names" => "clone, temp, test",
			"webpath" => "/srv/projects",
			"offroot" => "1",
			"gitpath" => "/usr/bin/git",
			"gitclone" => "/site/projects/clone/.git",
			"maxUpload" => "104857600",
			"defaultQuota" => "1",
			"premiumQuota" => "1",
			"approachingQuota" => "90",
			"pubQuota" => "1",
			"premiumPubQuota" => "1",
			"imagepath" => "/site/projects",
			"defaultpic" => "/components/com_projects/assets/img/project.png",
			"img_maxAllowed" => "5242880",
			"img_file_ext" => "jpg,jpeg,jpe,bmp,tif,tiff,png,gif",
			"logging" => "0",
			"messaging" => "1",
			"privacy" => "1",
			"limit" => "25",
			"sidebox_limit" => "3",
			"group_prefix" => "pr-",
			"use_alias" => "1",
			"documentation" => "/projects/features",
			"dbcheck" => "1"
		);
		$filesParams = array(
			"maxUpload" => "104857600",
			"maxDownload" => "1048576",
			"reservedNames" => "google , dropbox, shared, temp",
			"connectedProjects" => "",
			"enable_google" => "0",
			"google_clientId" => "",
			"google_clientSecret" => "",
			"google_appKey" => "",
			"google_folder" => "Google",
			"sync_lock" => "0",
			"auto_sync" => "1",
			"latex" => "1",
			"texpath" => "/usr/bin/",
			"gspath" => "/usr/bin/"
		);

		self::addComponentEntry('Projects', 'com_projects', 1, $componentParams);

		self::addPluginEntry('projects', 'blog');
		self::addPluginEntry('projects', 'team');
		self::addPluginEntry('projects', 'files', 1, $filesParams);
		self::addPluginEntry('projects', 'todo');
		self::addPluginEntry('projects', 'notes');
		self::addPluginEntry('members', 'projects');
		self::addPluginEntry('groups', 'projects');

		self::addModuleEntry('mod_myprojects');
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$queries = array();

		if ($db->tableExists('#__projects'))
		{
			// Create #__projects
			$queries[] = "DROP TABLE IF EXISTS `#__projects`";
		}

		if ($db->tableExists('#__project_activity'))
		{
			// Create #__project_activity
			$queries[] = "DROP TABLE IF EXISTS `#__project_activity`";
		}

		if ($db->tableExists('#__project_comments'))
		{
			// Create #__project_comments
			$queries[] = "DROP TABLE IF EXISTS `#__project_comments`";
		}

		if ($db->tableExists('#__project_microblog'))
		{
			// Create #__project_microblog
			$queries[] = "DROP TABLE IF EXISTS `#__project_microblog`";
		}

		if ($db->tableExists('#__project_owners'))
		{
			// Create #__project_owners
			$queries[] = "DROP TABLE IF EXISTS `#__project_owners`";
		}

		if ($db->tableExists('#__project_todo'))
		{
			// Create table #__project_todo
			$queries[] = "DROP TABLE IF EXISTS `#__project_todo`";
		}

		if ($db->tableExists('#__project_types'))
		{
			// Create #__project_types
			$queries[] = "DROP TABLE IF EXISTS `#__project_types`";
		}

		if ($db->tableExists('#__project_logs'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__project_logs`";
		}

		if ($db->tableExists('#__project_stats'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__project_stats`";
		}

		if ($db->tableExists('#__project_stats'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__project_stats`";
		}

		if ($db->tableExists('#__project_public_stamps'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__project_public_stamps`";
		}

		if ($db->tableExists('#__project_remote_files'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__project_remote_files`";
		}

		$queries[] = "DELETE FROM `#__xmessage_component` WHERE `component` = 'com_projects'";

		if (count($queries) > 0)
		{
			// Run queries
			foreach ($queries as $query)
			{
				$db->setQuery($query);
				$db->query();
			}
		}

		self::deleteComponentEntry('Projects');

		self::deletePluginEntry('projects');
		self::deletePluginEntry('members', 'projects');
		self::deletePluginEntry('groups', 'projects');

		self::deleteModuleEntry('mod_myprojects');
	}
}