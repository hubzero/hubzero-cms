<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing tools tables
 **/
class Migration20170901000000ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__recent_tools'))
		{
			$query = "CREATE TABLE `#__recent_tools` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uid` int(11) NOT NULL DEFAULT '0',
			  `tool` varchar(200) DEFAULT NULL,
			  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__screenshots'))
		{
			$query = "CREATE TABLE `#__screenshots` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `versionid` int(11) DEFAULT '0',
			  `title` varchar(127) DEFAULT '',
			  `ordering` int(11) DEFAULT '0',
			  `filename` varchar(100) NOT NULL,
			  `resourceid` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool'))
		{
			$query = "CREATE TABLE `#__tool` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `toolname` varchar(64) NOT NULL DEFAULT '',
			  `title` varchar(127) NOT NULL DEFAULT '',
			  `version` varchar(15) DEFAULT NULL,
			  `description` text,
			  `fulltxt` text,
			  `license` text,
			  `toolaccess` varchar(15) DEFAULT NULL,
			  `codeaccess` varchar(15) DEFAULT NULL,
			  `wikiaccess` varchar(15) DEFAULT NULL,
			  `published` tinyint(1) DEFAULT '0',
			  `state` int(15) DEFAULT NULL,
			  `priority` int(15) DEFAULT '3',
			  `team` text,
			  `registered` datetime DEFAULT NULL,
			  `registered_by` varchar(31) DEFAULT NULL,
			  `mw` varchar(31) DEFAULT NULL,
			  `vnc_geometry` varchar(31) DEFAULT NULL,
			  `ticketid` int(15) DEFAULT NULL,
			  `state_changed` datetime DEFAULT '0000-00-00 00:00:00',
			  `revision` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_toolname` (`toolname`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_authors'))
		{
			$query = "CREATE TABLE `#__tool_authors` (
			  `toolname` varchar(50) NOT NULL DEFAULT '',
			  `revision` int(15) NOT NULL DEFAULT '0',
			  `uid` int(11) NOT NULL DEFAULT '0',
			  `ordering` int(11) DEFAULT '0',
			  `version_id` int(11) NOT NULL DEFAULT '0',
			  `name` varchar(255) DEFAULT NULL,
			  `organization` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`toolname`,`revision`,`uid`,`version_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_groups'))
		{
			$query = "CREATE TABLE `#__tool_groups` (
			  `cn` varchar(255) NOT NULL DEFAULT '',
			  `toolid` int(11) NOT NULL DEFAULT '0',
			  `role` tinyint(2) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`cn`,`toolid`,`role`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_handlers'))
		{
			$query = "CREATE TABLE `#__tool_handlers` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `tool_id` int(11) NOT NULL,
			  `prompt` varchar(255) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_handler_rules'))
		{
			$query = "CREATE TABLE `#__tool_handler_rules` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `handler_id` int(11) NOT NULL,
			  `extension` varchar(10) NOT NULL DEFAULT '',
			  `quantity` varchar(10) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_licenses'))
		{
			$query = "CREATE TABLE `#__tool_licenses` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) DEFAULT NULL,
			  `text` text,
			  `title` varchar(100) DEFAULT NULL,
			  `ordering` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_session_classes'))
		{
			$query = "CREATE TABLE `#__tool_session_classes` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `alias` varchar(255) NOT NULL DEFAULT '',
			  `jobs` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_alias` (`alias`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_session_class_groups'))
		{
			$query = "CREATE TABLE `#__tool_session_class_groups` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `class_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_class_id` (`class_id`),
			  KEY `idx_group_id` (`group_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_statusviews'))
		{
			$query = "CREATE TABLE `#__tool_statusviews` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `ticketid` varchar(15) NOT NULL DEFAULT '',
			  `uid` varchar(31) NOT NULL DEFAULT '',
			  `viewed` datetime DEFAULT '0000-00-00 00:00:00',
			  `elapsed` int(11) DEFAULT '500000',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_version'))
		{
			$query = "CREATE TABLE `#__tool_version` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `toolname` varchar(64) NOT NULL DEFAULT '',
			  `instance` varchar(31) NOT NULL DEFAULT '',
			  `title` varchar(127) NOT NULL DEFAULT '',
			  `description` text,
			  `fulltxt` text,
			  `version` varchar(15) DEFAULT NULL,
			  `revision` int(11) DEFAULT NULL,
			  `toolaccess` varchar(15) DEFAULT NULL,
			  `codeaccess` varchar(15) DEFAULT NULL,
			  `wikiaccess` varchar(15) DEFAULT NULL,
			  `state` int(15) DEFAULT NULL,
			  `released_by` varchar(31) DEFAULT NULL,
			  `released` datetime DEFAULT NULL,
			  `unpublished` datetime DEFAULT NULL,
			  `exportControl` varchar(16) DEFAULT NULL,
			  `license` text,
			  `vnc_geometry` varchar(31) DEFAULT NULL,
			  `vnc_depth` int(11) DEFAULT NULL,
			  `vnc_timeout` int(11) DEFAULT NULL,
			  `vnc_command` varchar(100) DEFAULT NULL,
			  `mw` varchar(31) DEFAULT NULL,
			  `toolid` int(11) DEFAULT NULL,
			  `priority` int(11) DEFAULT NULL,
			  `params` text,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_toolname_instance` (`toolname`,`instance`),
			  KEY `idx_instance` (`instance`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_version_alias'))
		{
			$query = "CREATE TABLE `#__tool_version_alias` (
			  `tool_version_id` int(11) NOT NULL,
			  `alias` varchar(255) NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_version_hostreq'))
		{
			$query = "CREATE TABLE `#__tool_version_hostreq` (
			  `tool_version_id` int(11) NOT NULL,
			  `hostreq` varchar(255) NOT NULL,
			  UNIQUE KEY `uidx_tool_version_id_hostreq` (`tool_version_id`,`hostreq`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_version_middleware'))
		{
			$query = "CREATE TABLE `#__tool_version_middleware` (
			  `tool_version_id` int(11) NOT NULL,
			  `middleware` varchar(255) NOT NULL,
			  UNIQUE KEY `uidx_tool_version_id_middleware` (`tool_version_id`,`middleware`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_version_zone'))
		{
			$query = "CREATE TABLE `#__tool_version_zone` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `tool_version_id` int(11) NOT NULL,
			  `zone_id` int(11) NOT NULL,
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_zoneid_toolversionid` (`zone_id`,`tool_version_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__doi_mapping'))
		{
			$query = "CREATE TABLE `#__doi_mapping` (
			  `local_revision` int(11) NOT NULL,
			  `doi_label` int(11) NOT NULL,
			  `rid` int(11) NOT NULL,
			  `alias` varchar(30) DEFAULT NULL,
			  `versionid` int(11) DEFAULT '0',
			  `doi` varchar(50) DEFAULT NULL
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
		if ($this->db->tableExists('#__recent_tools'))
		{
			$query = "DROP TABLE IF EXISTS `#__recent_tools`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__screenshots'))
		{
			$query = "DROP TABLE IF EXISTS `#__screenshots`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_authors'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool_authors`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_groups'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool_groups`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_handlers'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool_handlers`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_handler_rules'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool_handler_rules`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_licenses'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool_licenses`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_session_classes'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool_session_classes`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_session_class_groups'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool_session_class_groups`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_statusviews'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool_statusviews`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_version'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool_version`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_version_alias'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool_version_alias`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_version_hostreq'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool_version_hostreq`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_version_middleware'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool_version_middleware`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_version_zone'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool_version_zone`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__doi_mapping'))
		{
			$query = "DROP TABLE IF EXISTS `#__doi_mapping`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
