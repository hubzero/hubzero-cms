<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing projects tables
 **/
class Migration20170901000000ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__projects'))
		{
			$query = "CREATE TABLE `#__projects` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `alias` varchar(30) NOT NULL DEFAULT '',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `picture` varchar(255) DEFAULT '',
			  `about` text,
			  `state` int(11) NOT NULL DEFAULT '0',
			  `type` int(11) NOT NULL DEFAULT '1',
			  `provisioned` int(11) NOT NULL DEFAULT '0',
			  `private` int(11) NOT NULL DEFAULT '1',
			  `created` datetime DEFAULT NULL,
			  `modified` datetime DEFAULT NULL,
			  `owned_by_user` int(11) NOT NULL DEFAULT '0',
			  `created_by_user` int(11) NOT NULL,
			  `owned_by_group` int(11) DEFAULT '0',
			  `modified_by` int(11) DEFAULT '0',
			  `setup_stage` int(11) NOT NULL DEFAULT '0',
			  `params` text,
			  `admin_notes` text,
			  `sync_group` tinyint(2) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_alias` (`alias`),
			  KEY `idx_sync_group` (`sync_group`),
			  FULLTEXT KEY `idx_fulltxt_alias_title_about` (`alias`,`title`,`about`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_types'))
		{
			$query = "CREATE TABLE `#__project_types` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `type` varchar(150) NOT NULL DEFAULT '',
			  `description` varchar(255) NOT NULL DEFAULT '',
			  `params` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_tools'))
		{
			$query = "CREATE TABLE `#__project_tools` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `name` varchar(64) NOT NULL DEFAULT '',
			  `title` varchar(127) NOT NULL DEFAULT '',
			  `repotype` tinyint(1) NOT NULL DEFAULT '1',
			  `repopath` varchar(255) NOT NULL DEFAULT '',
			  `status` int(11) NOT NULL DEFAULT '0',
			  `status_changed` varchar(31) NOT NULL,
			  `status_changed_by` int(11) NOT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL,
			  `svntool_id` int(11) NOT NULL,
			  `project_id` int(11) NOT NULL,
			  `published` tinyint(1) NOT NULL DEFAULT '0',
			  `opendev` tinyint(1) NOT NULL DEFAULT '0',
			  `opensource` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `toolname` (`name`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_tool_views'))
		{
			$query = "CREATE TABLE `#__project_tool_views` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `parent_id` int(11) NOT NULL DEFAULT '0',
			  `userid` int(15) NOT NULL,
			  `viewed` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_tool_statuses'))
		{
			$query = "CREATE TABLE `#__project_tool_statuses` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `status` varchar(100) NOT NULL DEFAULT '',
			  `status_about` text,
			  `next` varchar(100) NOT NULL DEFAULT '',
			  `next_admin` varchar(100) NOT NULL DEFAULT '',
			  `next_about` text,
			  `next_actor` tinyint(1) NOT NULL DEFAULT '0',
			  `wait_time` varchar(100) NOT NULL DEFAULT '',
			  `options` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_tool_logs'))
		{
			$query = "CREATE TABLE `#__project_tool_logs` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `parent_id` int(11) NOT NULL DEFAULT '0',
			  `parent_name` varchar(64) NOT NULL DEFAULT '',
			  `instance_id` int(11) DEFAULT NULL,
			  `action` varchar(255) NOT NULL DEFAULT '',
			  `actor` int(15) NOT NULL,
			  `recorded` datetime DEFAULT NULL,
			  `project_activity_id` int(15) NOT NULL DEFAULT '0',
			  `status_changed` tinyint(1) NOT NULL DEFAULT '0',
			  `admin` tinyint(1) NOT NULL DEFAULT '0',
			  `access` tinyint(1) NOT NULL DEFAULT '0',
			  `log` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_tool_instances'))
		{
			$query = "CREATE TABLE `#__project_tool_instances` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `parent_id` int(11) NOT NULL DEFAULT '0',
			  `parent_name` varchar(64) NOT NULL DEFAULT '',
			  `instance` varchar(100) NOT NULL DEFAULT '',
			  `revision` int(11) NOT NULL,
			  `commit` varchar(255) NOT NULL,
			  `access` varchar(16) NOT NULL,
			  `state` int(11) NOT NULL,
			  `created_by` int(11) NOT NULL,
			  `created` datetime DEFAULT NULL,
			  `modified_by` int(11) DEFAULT NULL,
			  `modified` datetime DEFAULT NULL,
			  `svntool_version_id` int(11) DEFAULT NULL,
			  `params` text,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `toolname` (`parent_name`,`instance`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_stats'))
		{
			$query = "CREATE TABLE `#__project_stats` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `month` int(2) DEFAULT NULL,
			  `year` int(2) DEFAULT NULL,
			  `week` int(2) DEFAULT NULL,
			  `processed` datetime DEFAULT NULL,
			  `stats` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_repos'))
		{
			$query = "CREATE TABLE `#__project_repos` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `project_id` int(11) NOT NULL,
			  `name` varchar(64) NOT NULL DEFAULT '',
			  `about` varchar(255) DEFAULT NULL,
			  `path` varchar(255) NOT NULL DEFAULT '',
			  `status` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL,
			  `remote` tinyint(1) NOT NULL DEFAULT '0',
			  `engine` varchar(100) NOT NULL DEFAULT 'git',
			  `params` text,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `repo` (`project_id`,`name`,`path`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_remote_files'))
		{
			$query = "CREATE TABLE `#__project_remote_files` (
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
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_public_stamps'))
		{
			$query = "CREATE TABLE `#__project_public_stamps` (
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
			  UNIQUE KEY `uidx_stamp` (`stamp`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_owners'))
		{
			$query = "CREATE TABLE `#__project_owners` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `projectid` int(11) NOT NULL DEFAULT '0',
			  `userid` int(11) NOT NULL DEFAULT '0',
			  `groupid` int(11) DEFAULT '0',
			  `invited_name` varchar(100) DEFAULT NULL,
			  `invited_email` varchar(100) DEFAULT NULL,
			  `invited_code` varchar(10) DEFAULT NULL,
			  `added` datetime DEFAULT NULL,
			  `lastvisit` datetime DEFAULT NULL,
			  `prev_visit` datetime DEFAULT NULL,
			  `status` int(11) NOT NULL DEFAULT '0',
			  `num_visits` int(11) NOT NULL DEFAULT '0',
			  `role` int(11) NOT NULL DEFAULT '0',
			  `native` int(11) NOT NULL DEFAULT '0',
			  `params` text,
			  PRIMARY KEY (`id`),
			  KEY `idx_projectid` (`projectid`),
			  KEY `idx_userid` (`userid`),
			  KEY `idx_groupid` (`groupid`),
			  KEY `idx_status` (`status`),
			  KEY `idx_role` (`role`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_microblog'))
		{
			$query = "CREATE TABLE `#__project_microblog` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `blogentry` text,
			  `posted` datetime DEFAULT NULL,
			  `posted_by` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(2) DEFAULT '0',
			  `params` tinytext,
			  `projectid` int(11) NOT NULL DEFAULT '0',
			  `activityid` int(11) NOT NULL DEFAULT '0',
			  `managers_only` tinyint(2) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_projectid` (`projectid`),
			  KEY `idx_state` (`state`),
			  FULLTEXT KEY `ftidx_blogentry` (`blogentry`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_logs'))
		{
			$query = "CREATE TABLE `#__project_logs` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `projectid` int(11) unsigned NOT NULL DEFAULT '0',
			  `userid` int(11) NOT NULL DEFAULT '0',
			  `ajax` tinyint(1) DEFAULT '0',
			  `owner` int(11) unsigned DEFAULT '0',
			  `ip` varchar(15) DEFAULT '0',
			  `section` varchar(100) DEFAULT 'general',
			  `layout` varchar(100) DEFAULT '',
			  `action` varchar(100) DEFAULT '',
			  `time` datetime DEFAULT NULL,
			  `request_uri` tinytext,
			  PRIMARY KEY (`id`),
			  KEY `idx_projectid` (`projectid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_descriptions'))
		{
			$query = "CREATE TABLE `#__project_descriptions` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `project_id` int(11) NOT NULL,
			  `description_key` varchar(100) NOT NULL DEFAULT '',
			  `description_value` text NOT NULL,
			  `ordering` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_user_id` (`project_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_description_fields'))
		{
			$query = "CREATE TABLE `#__project_description_fields` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `type` varchar(255) NOT NULL,
			  `name` varchar(255) NOT NULL DEFAULT '',
			  `label` varchar(255) NOT NULL DEFAULT '',
			  `placeholder` varchar(255) DEFAULT NULL,
			  `description` mediumtext,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `access` int(10) NOT NULL DEFAULT '0',
			  `option_other` tinyint(2) NOT NULL DEFAULT '0',
			  `option_blank` tinyint(2) NOT NULL DEFAULT '0',
			  `action_create` tinyint(2) NOT NULL DEFAULT '1',
			  `action_update` tinyint(2) NOT NULL DEFAULT '1',
			  `action_edit` tinyint(2) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`id`),
			  KEY `idx_type` (`type`),
			  KEY `idx_access` (`access`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_description_options'))
		{
			$query = "CREATE TABLE `#__project_description_options` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `field_id` int(11) NOT NULL DEFAULT '0',
			  `value` varchar(255) NOT NULL DEFAULT '',
			  `label` varchar(255) NOT NULL DEFAULT '',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `checked` tinyint(2) NOT NULL DEFAULT '0',
			  `dependents` tinytext,
			  PRIMARY KEY (`id`),
			  KEY `idx_field_id` (`field_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_comments'))
		{
			$query = "CREATE TABLE `#__project_comments` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `itemid` int(11) NOT NULL DEFAULT '0',
			  `comment` text NOT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `activityid` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  `parent_activity` int(11) DEFAULT '0',
			  `anonymous` tinyint(2) DEFAULT '0',
			  `admin` tinyint(2) DEFAULT '0',
			  `tbl` varchar(50) NOT NULL DEFAULT 'blog',
			  PRIMARY KEY (`id`),
			  KEY `idx_itemid` (`itemid`),
			  KEY `idx_activityid` (`activityid`),
			  KEY `idx_state` (`state`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_activity'))
		{
			$query = "CREATE TABLE `#__project_activity` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `projectid` int(11) NOT NULL DEFAULT '0',
			  `userid` int(11) NOT NULL DEFAULT '0',
			  `referenceid` varchar(255) NOT NULL DEFAULT '0',
			  `managers_only` tinyint(2) DEFAULT '0',
			  `admin` tinyint(2) DEFAULT '0',
			  `commentable` tinyint(2) NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  `recorded` datetime DEFAULT NULL,
			  `activity` varchar(255) NOT NULL DEFAULT '',
			  `highlighted` varchar(100) NOT NULL DEFAULT '',
			  `url` varchar(255) DEFAULT NULL,
			  `class` varchar(150) DEFAULT NULL,
			  `preview` text,
			  PRIMARY KEY (`id`),
			  KEY `idx_projectid` (`projectid`),
			  KEY `idx_state` (`state`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__projects_connections'))
		{
			$query = "CREATE TABLE `#__projects_connections` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) DEFAULT NULL,
			  `project_id` int(11) NOT NULL,
			  `provider_id` int(11) NOT NULL,
			  `owner_id` int(11) DEFAULT NULL,
			  `params` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__projects_connection_providers'))
		{
			$query = "CREATE TABLE `#__projects_connection_providers` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `alias` varchar(255) NOT NULL DEFAULT '',
			  `name` varchar(255) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__projects'))
		{
			$query = "DROP TABLE IF EXISTS `#__projects`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_types'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_types`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_tools'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_tools`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_tool_viewss'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_tool_views`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_tool_statuses'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_tool_statuses`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_tool_logs'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_tool_logs`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_tool_instances'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_tool_instances`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_stats'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_stats`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_repos'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_repos`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_remote_files'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_remote_files`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_public_stamps'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_public_stamps`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_owners'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_owners`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_microblog'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_microblog`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_logs'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_logs`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_descriptions'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_descriptions`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_description_fields'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_description_fields`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_description_options'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_description_options`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_comments'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_comments`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_activity'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_activity`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__projects_connections'))
		{
			$query = "DROP TABLE IF EXISTS `#__projects_connections`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__projects_connection_providers'))
		{
			$query = "DROP TABLE IF EXISTS `#__projects_connection_providers`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
