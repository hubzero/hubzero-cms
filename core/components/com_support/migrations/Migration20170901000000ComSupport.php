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
 * Migration script for installing support tables
 **/
class Migration20170901000000ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__support_acl_acos'))
		{
			$query = "CREATE TABLE `#__support_acl_acos` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `model` varchar(100) NOT NULL DEFAULT '',
			  `foreign_key` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__support_acl_aros'))
		{
			$query = "CREATE TABLE `#__support_acl_aros` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `model` varchar(100) NOT NULL DEFAULT '',
			  `foreign_key` int(11) NOT NULL DEFAULT '0',
			  `alias` varchar(255) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`),
			  KEY `idx_model_foreign_key` (`model`,`foreign_key`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__support_acl_aros_acos'))
		{
			$query = "CREATE TABLE `#__support_acl_aros_acos` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `aro_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `aco_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `action_create` tinyint(3) NOT NULL DEFAULT '0',
			  `action_read` tinyint(3) NOT NULL DEFAULT '0',
			  `action_update` tinyint(3) NOT NULL DEFAULT '0',
			  `action_delete` tinyint(3) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_aco_id` (`aco_id`),
			  KEY `idx_aro_id` (`aro_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__support_attachments'))
		{
			$query = "CREATE TABLE `#__support_attachments` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `ticket` int(11) NOT NULL DEFAULT '0',
			  `filename` varchar(255) DEFAULT '',
			  `description` varchar(255) NOT NULL DEFAULT '',
			  `comment_id` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_ticket` (`ticket`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__support_categories'))
		{
			$query = "CREATE TABLE `#__support_categories` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `section_id` int(11) NOT NULL DEFAULT '0',
			  `alias` varchar(250) NOT NULL DEFAULT '',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__support_comments'))
		{
			$query = "CREATE TABLE `#__support_comments` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `ticket` int(11) unsigned NOT NULL DEFAULT '0',
			  `comment` text NOT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `changelog` text NOT NULL,
			  `access` tinyint(3) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_ticket` (`ticket`),
			  KEY `idx_created_by` (`created_by`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__support_messages'))
		{
			$query = "CREATE TABLE `#__support_messages` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(250) NOT NULL DEFAULT '',
			  `message` text NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__support_queries'))
		{
			$query = "CREATE TABLE `#__support_queries` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(250) NOT NULL DEFAULT '',
			  `conditions` text NOT NULL,
			  `query` text NOT NULL,
			  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `sort` varchar(100) NOT NULL DEFAULT '',
			  `sort_dir` varchar(100) NOT NULL DEFAULT '',
			  `created` datetime DEFAULT NULL,
			  `iscore` int(3) NOT NULL DEFAULT '0',
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `folder_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_user_id` (`user_id`),
			  KEY `idx_iscore` (`iscore`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__support_query_folders'))
		{
			$query = "CREATE TABLE `#__support_query_folders` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `title` varchar(200) NOT NULL DEFAULT '',
			  `alias` varchar(200) NOT NULL DEFAULT '',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `iscore` tinyint(2) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__support_statuses'))
		{
			$query = "CREATE TABLE `#__support_statuses` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `open` tinyint(2) NOT NULL DEFAULT '0',
			  `title` varchar(250) NOT NULL DEFAULT '',
			  `alias` varchar(250) NOT NULL DEFAULT '',
			  `color` varchar(50) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`),
			  KEY `idx_open` (`open`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__support_tickets'))
		{
			$query = "CREATE TABLE `#__support_tickets` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `status` tinyint(3) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `closed` datetime DEFAULT NULL,
			  `login` varchar(200) NOT NULL DEFAULT '',
			  `severity` varchar(30) NOT NULL DEFAULT '',
			  `owner` int(11) NOT NULL DEFAULT '0',
			  `category` varchar(50) NOT NULL DEFAULT '',
			  `summary` varchar(250) NOT NULL DEFAULT '',
			  `report` text NOT NULL,
			  `resolved` varchar(50) NOT NULL DEFAULT '',
			  `email` varchar(200) NOT NULL DEFAULT '',
			  `name` varchar(200) NOT NULL DEFAULT '',
			  `os` varchar(50) NOT NULL DEFAULT '',
			  `browser` varchar(50) NOT NULL DEFAULT '',
			  `ip` varchar(200) NOT NULL DEFAULT '',
			  `hostname` varchar(200) NOT NULL DEFAULT '',
			  `uas` varchar(250) NOT NULL DEFAULT '',
			  `referrer` varchar(250) NOT NULL DEFAULT '',
			  `cookies` tinyint(3) NOT NULL DEFAULT '0',
			  `instances` int(11) NOT NULL DEFAULT '1',
			  `section` int(11) NOT NULL DEFAULT '1',
			  `type` tinyint(3) NOT NULL DEFAULT '0',
			  `group_id` int(11) NOT NULL DEFAULT '0',
			  `open` tinyint(3) NOT NULL DEFAULT '1',
			  `target_date` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_owner` (`owner`),
			  KEY `idx_status` (`status`),
			  KEY `idx_open` (`open`),
			  KEY `idx_type` (`type`),
			  KEY `idx_severity` (`severity`),
			  KEY `idx_group_id` (`group_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__support_watching'))
		{
			$query = "CREATE TABLE `#__support_watching` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `ticket_id` int(11) NOT NULL DEFAULT '0',
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_ticket_id` (`ticket_id`),
			  KEY `idx_user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__abuse_reports'))
		{
			$query = "CREATE TABLE `#__abuse_reports` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `category` varchar(50) DEFAULT NULL,
			  `referenceid` int(11) unsigned NOT NULL DEFAULT '0',
			  `report` text NOT NULL,
			  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  `subject` varchar(150) DEFAULT NULL,
			  `reviewed` datetime DEFAULT NULL,
			  `reviewed_by` int(11) unsigned NOT NULL DEFAULT '0',
			  `note` text NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_reviewed_by` (`reviewed_by`),
			  KEY `idx_state` (`state`),
			  KEY `idx_category_referenceid` (`category`,`referenceid`)
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
		if ($this->db->tableExists('#__support_acl_acos'))
		{
			$query = "DROP TABLE IF EXISTS `#__support_acl_acos`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_acl_aros'))
		{
			$query = "DROP TABLE IF EXISTS `#__support_acl_aros`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_acl_aros_acos'))
		{
			$query = "DROP TABLE IF EXISTS `#__support_acl_aros_acos`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_attachments'))
		{
			$query = "DROP TABLE IF EXISTS `#__support_attachments`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_categories'))
		{
			$query = "DROP TABLE IF EXISTS `#__support_categories`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_comments'))
		{
			$query = "DROP TABLE IF EXISTS `#__support_comments`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_messages'))
		{
			$query = "DROP TABLE IF EXISTS `#__support_messages`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_queries'))
		{
			$query = "DROP TABLE IF EXISTS `#__support_queries`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_query_folders'))
		{
			$query = "DROP TABLE IF EXISTS `#__support_query_folders`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_statuses'))
		{
			$query = "DROP TABLE IF EXISTS `#__support_statuses`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_tickets'))
		{
			$query = "DROP TABLE IF EXISTS `#__support_tickets`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_watching'))
		{
			$query = "DROP TABLE IF EXISTS `#__support_watching`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__abuse_reports'))
		{
			$query = "DROP TABLE IF EXISTS `#__abuse_reports`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
