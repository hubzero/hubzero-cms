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
 * Migration script for installing groups tables
 **/
class Migration20170901000000ComGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__xgroups'))
		{
			$query = "CREATE TABLE `#__xgroups` (
			  `gidNumber` int(11) NOT NULL AUTO_INCREMENT,
			  `cn` varchar(255) DEFAULT NULL,
			  `description` varchar(255) DEFAULT NULL,
			  `published` tinyint(3) DEFAULT '0',
			  `approved` tinyint(3) DEFAULT '1',
			  `type` tinyint(3) DEFAULT '0',
			  `public_desc` text,
			  `private_desc` text,
			  `restrict_msg` text,
			  `join_policy` tinyint(3) DEFAULT '0',
			  `discoverability` tinyint(3) DEFAULT NULL,
			  `discussion_email_autosubscribe` tinyint(3) DEFAULT NULL,
			  `logo` varchar(255) DEFAULT NULL,
			  `plugins` text,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) DEFAULT NULL,
			  `params` text,
			  PRIMARY KEY (`gidNumber`),
			  UNIQUE KEY `idx_cn` (`cn`),
			  FULLTEXT KEY `ftidx_cn_description_public_desc` (`cn`,`description`,`public_desc`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_members'))
		{
			$query = "CREATE TABLE `#__xgroups_members` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `gidNumber` int(11) NOT NULL,
			  `uidNumber` int(11) NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id` (`id`),
			  UNIQUE KEY `idx_gidNumber_uidNumber` (`gidNumber`,`uidNumber`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_managers'))
		{
			$query = "CREATE TABLE `#__xgroups_managers` (
			  `gidNumber` int(11) NOT NULL,
			  `uidNumber` int(11) NOT NULL,
			  PRIMARY KEY (`gidNumber`,`uidNumber`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_applicants'))
		{
			$query = "CREATE TABLE `#__xgroups_applicants` (
			  `gidNumber` int(11) NOT NULL,
			  `uidNumber` int(11) NOT NULL,
			  PRIMARY KEY (`gidNumber`,`uidNumber`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_invitees'))
		{
			$query = "CREATE TABLE `#__xgroups_invitees` (
			  `gidNumber` int(11) NOT NULL,
			  `uidNumber` int(11) NOT NULL,
			  PRIMARY KEY (`gidNumber`,`uidNumber`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_inviteemails'))
		{
			$query = "CREATE TABLE `#__xgroups_inviteemails` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `email` varchar(150) NOT NULL,
			  `gidNumber` int(11) NOT NULL,
			  `token` varchar(255) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_gidNumber` (`gidNumber`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_log'))
		{
			$query = "CREATE TABLE `#__xgroups_log` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `gidNumber` int(11) DEFAULT NULL,
			  `timestamp` datetime DEFAULT NULL,
			  `userid` int(11) DEFAULT NULL,
			  `action` varchar(50) DEFAULT NULL,
			  `comments` text,
			  `actorid` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_gidNumber` (`gidNumber`),
			  KEY `idx_userid` (`userid`),
			  KEY `idx_actorid` (`actorid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_memberoption'))
		{
			$query = "CREATE TABLE `#__xgroups_memberoption` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `gidNumber` int(11) DEFAULT NULL,
			  `userid` int(11) DEFAULT NULL,
			  `optionname` varchar(100) DEFAULT NULL,
			  `optionvalue` varchar(100) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_gidNumber` (`gidNumber`),
			  KEY `idx_userid` (`userid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_reasons'))
		{
			$query = "CREATE TABLE `#__xgroups_reasons` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uidNumber` int(11) NOT NULL,
			  `gidNumber` int(11) NOT NULL,
			  `reason` text,
			  `date` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_gidNumber` (`gidNumber`),
			  KEY `idx_uidNumber` (`uidNumber`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_recents'))
		{
			$query = "CREATE TABLE `#__xgroups_recents` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_user_id` (`user_id`),
			  KEY `idx_group_id` (`group_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_tracperm'))
		{
			$query = "CREATE TABLE `#__xgroups_tracperm` (
			  `group_id` int(11) NOT NULL,
			  `action` varchar(255) NOT NULL,
			  `project_id` int(11) NOT NULL,
			  UNIQUE KEY `id` (`group_id`,`action`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_roles'))
		{
			$query = "CREATE TABLE `#__xgroups_roles` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `gidNumber` int(11) DEFAULT NULL,
			  `name` varchar(150) DEFAULT NULL,
			  `permissions` text,
			  PRIMARY KEY (`id`),
			  KEY `idx_gidNumber` (`gidNumber`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_member_roles'))
		{
			$query = "CREATE TABLE `#__xgroups_member_roles` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `roleid` int(11) DEFAULT NULL,
			  `uidNumber` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		// Pages
		if (!$this->db->tableExists('#__xgroups_pages'))
		{
			$query = "CREATE TABLE `#__xgroups_pages` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `gidNumber` int(11) DEFAULT NULL,
			  `parent` int(11) DEFAULT '0',
			  `lft` int(11) DEFAULT NULL,
			  `rgt` int(11) DEFAULT NULL,
			  `depth` int(11) DEFAULT '1',
			  `category` int(11) DEFAULT NULL,
			  `template` varchar(100) DEFAULT NULL,
			  `alias` varchar(100) DEFAULT NULL,
			  `title` varchar(100) DEFAULT NULL,
			  `state` int(11) DEFAULT '1',
			  `privacy` varchar(10) DEFAULT NULL,
			  `home` int(11) DEFAULT '0',
			  `comments` tinyint(4) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_gidNumber` (`gidNumber`),
			  KEY `idx_state` (`state`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_pages_versions'))
		{
			$query = "CREATE TABLE `#__xgroups_pages_versions` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `pageid` int(11) DEFAULT NULL,
			  `version` int(11) DEFAULT NULL,
			  `content` longtext,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) DEFAULT NULL,
			  `approved` int(11) DEFAULT '1',
			  `approved_on` datetime DEFAULT NULL,
			  `approved_by` int(11) DEFAULT NULL,
			  `checked_errors` int(11) DEFAULT '0',
			  `scanned` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_pageid` (`pageid`),
			  KEY `idx_approved` (`approved`),
			  KEY `idx_scanned` (`scanned`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_pages_categories'))
		{
			$query = "CREATE TABLE `#__xgroups_pages_categories` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `gidNumber` int(11) DEFAULT NULL,
			  `title` varchar(255) DEFAULT NULL,
			  `color` varchar(6) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_gidNumber` (`gidNumber`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_pages_checkout'))
		{
			$query = "CREATE TABLE `#__xgroups_pages_checkout` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `pageid` int(11) DEFAULT NULL,
			  `userid` int(11) DEFAULT NULL,
			  `when` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_pages_hits'))
		{
			$query = "CREATE TABLE `#__xgroups_pages_hits` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `gidNumber` int(11) DEFAULT NULL,
			  `pageid` int(11) DEFAULT NULL,
			  `userid` int(11) DEFAULT NULL,
			  `date` datetime DEFAULT NULL,
			  `ip` varchar(15) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_modules'))
		{
			$query = "CREATE TABLE `#__xgroups_modules` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `gidNumber` int(11) DEFAULT NULL,
			  `title` varchar(255) DEFAULT '',
			  `content` text,
			  `position` varchar(50) DEFAULT NULL,
			  `ordering` int(11) DEFAULT NULL,
			  `state` int(1) DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) DEFAULT NULL,
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) DEFAULT NULL,
			  `approved` int(11) DEFAULT '1',
			  `approved_on` datetime DEFAULT NULL,
			  `approved_by` int(11) DEFAULT NULL,
			  `checked_errors` int(11) DEFAULT '0',
			  `scanned` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_gidNumber` (`gidNumber`),
			  KEY `idx_state` (`state`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_modules_menu'))
		{
			$query = "CREATE TABLE `#__xgroups_modules_menu` (
			  `moduleid` int(11) DEFAULT NULL,
			  `pageid` int(11) DEFAULT NULL
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
		if ($this->db->tableExists('#__xgroups'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_members'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_members`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_managers'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_managers`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_applicants'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_applicants`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_invitees'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_invitees`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_inviteemails'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_inviteemails`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_log'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_log`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_memberoption'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_memberoption`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_reasons'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_reasons`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_recents'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_recents`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_tracperm'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_tracperm`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_roles'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_roles`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_member_roles'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_member_roles`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Pages
		if ($this->db->tableExists('#__xgroups_pages'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_pages`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_pages_versions'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_pages_versions`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_pages_categories'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_pages_categories`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_pages_checkout'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_pages_checkout`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_pages_hits'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_pages_hits`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_modules'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_modules`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_modules_menu'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_modules_menu`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
