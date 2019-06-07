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
 * Migration script for installing resources tables
 **/
class Migration20170901000000ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Primary tables
		if (!$this->db->tableExists('#__resources'))
		{
			$query = "CREATE TABLE `#__resources` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `title` varchar(250) NOT NULL DEFAULT '',
			  `type` int(11) NOT NULL DEFAULT '0',
			  `logical_type` int(11) NOT NULL DEFAULT '0',
			  `introtext` text NOT NULL,
			  `fulltxt` text NOT NULL,
			  `footertext` text NOT NULL,
			  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `published` int(1) NOT NULL DEFAULT '0',
			  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `access` int(11) NOT NULL DEFAULT '0',
			  `hits` int(11) NOT NULL DEFAULT '0',
			  `path` varchar(200) NOT NULL DEFAULT '',
			  `checked_out` int(11) NOT NULL DEFAULT '0',
			  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `standalone` tinyint(1) NOT NULL DEFAULT '0',
			  `group_owner` varchar(250) NOT NULL DEFAULT '',
			  `group_access` text,
			  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
			  `times_rated` int(11) NOT NULL DEFAULT '0',
			  `params` text,
			  `attribs` text,
			  `alias` varchar(100) NOT NULL DEFAULT '',
			  `ranking` float NOT NULL DEFAULT '0',
			  `master_doi` varchar(100) DEFAULT '',
			  PRIMARY KEY (`id`),
			  FULLTEXT KEY `ftidx_title` (`title`),
			  FULLTEXT KEY `ftidx_introtext_fulltxt` (`introtext`,`fulltxt`),
			  FULLTEXT KEY `ftidx_title_introtext_fulltxt` (`title`,`introtext`,`fulltxt`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__resource_types'))
		{
			$query = "CREATE TABLE `#__resource_types` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `alias` varchar(100) DEFAULT NULL,
			  `type` varchar(200) NOT NULL DEFAULT '',
			  `category` int(11) NOT NULL DEFAULT '0',
			  `description` tinytext,
			  `contributable` int(2) DEFAULT '1',
			  `customFields` text,
			  `params` text,
			  `state` int(3) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_category` (`category`),
			  KEY `idx_state` (`state`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__resource_assoc'))
		{
			$query = "CREATE TABLE `#__resource_assoc` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `parent_id` int(11) NOT NULL DEFAULT '0',
			  `child_id` int(11) NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `grouping` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id` (`id`),
			  KEY `idx_parent_id_child_id` (`parent_id`,`child_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__resource_licenses'))
		{
			$query = "CREATE TABLE `#__resource_licenses` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) DEFAULT NULL,
			  `text` text,
			  `title` varchar(100) DEFAULT NULL,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `apps_only` tinyint(3) NOT NULL DEFAULT '0',
			  `main` varchar(255) DEFAULT NULL,
			  `icon` varchar(255) DEFAULT NULL,
			  `url` varchar(255) DEFAULT NULL,
			  `agreement` tinyint(2) NOT NULL DEFAULT '0',
			  `info` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		// Authors
		if (!$this->db->tableExists('#__author_assoc'))
		{
			$query = "CREATE TABLE `#__author_assoc` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `subtable` varchar(50) NOT NULL DEFAULT '',
			  `subid` int(11) NOT NULL DEFAULT '0',
			  `authorid` int(11) NOT NULL DEFAULT '0',
			  `ordering` int(11) DEFAULT NULL,
			  `role` varchar(50) DEFAULT NULL,
			  `name` varchar(255) DEFAULT NULL,
			  `organization` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_subtable_subid_authorid` (`subtable`,`subid`,`authorid`),
			  UNIQUE KEY `id` (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__author_roles'))
		{
			$query = "CREATE TABLE `#__author_roles` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) DEFAULT NULL,
			  `alias` varchar(255) DEFAULT NULL,
			  `state` tinyint(3) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__author_role_types'))
		{
			$query = "CREATE TABLE `#__author_role_types` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `role_id` int(11) NOT NULL DEFAULT '0',
			  `type_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		// Imports
		if (!$this->db->tableExists('#__resource_imports'))
		{
			$query = "CREATE TABLE `#__resource_imports` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(150) DEFAULT NULL,
			  `notes` text,
			  `file` varchar(255) DEFAULT '',
			  `count` int(11) DEFAULT NULL,
			  `created_by` int(11) DEFAULT NULL,
			  `created_at` datetime DEFAULT NULL,
			  `state` int(11) DEFAULT '1',
			  `mode` varchar(10) DEFAULT 'UPDATE',
			  `params` text,
			  `hooks` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__author_role_types'))
		{
			$query = "CREATE TABLE `#__resource_import_runs` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `import_id` int(11) DEFAULT NULL,
			  `processed` int(11) DEFAULT NULL,
			  `count` int(11) DEFAULT NULL,
			  `ran_by` int(11) DEFAULT NULL,
			  `ran_at` datetime DEFAULT NULL,
			  `dry_run` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__resource_import_hooks'))
		{
			$query = "CREATE TABLE `#__resource_import_hooks` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `type` varchar(25) DEFAULT NULL,
			  `name` varchar(255) DEFAULT NULL,
			  `notes` text,
			  `file` varchar(100) DEFAULT NULL,
			  `state` int(11) DEFAULT '1',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		// Taxonomy
		if (!$this->db->tableExists('#__resource_taxonomy_audience'))
		{
			$query = "CREATE TABLE `#__resource_taxonomy_audience` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `rid` int(11) NOT NULL DEFAULT '0',
			  `versionid` int(11) DEFAULT '0',
			  `level0` tinyint(2) NOT NULL DEFAULT '0',
			  `level1` tinyint(2) NOT NULL DEFAULT '0',
			  `level2` tinyint(2) NOT NULL DEFAULT '0',
			  `level3` tinyint(2) NOT NULL DEFAULT '0',
			  `level4` tinyint(2) NOT NULL DEFAULT '0',
			  `level5` tinyint(2) NOT NULL DEFAULT '0',
			  `comments` varchar(255) DEFAULT '',
			  `added` datetime DEFAULT NULL,
			  `addedBy` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__resource_taxonomy_audience_levels'))
		{
			$query = "CREATE TABLE `#__resource_taxonomy_audience_levels` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `label` varchar(11) NOT NULL DEFAULT '0',
			  `title` varchar(100) DEFAULT '',
			  `description` varchar(255) DEFAULT '',
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
		// Primary tables
		if ($this->db->tableExists('#__resources'))
		{
			$query = "DROP TABLE IF EXISTS `#__resources`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resource_types'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_types`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resource_assoc'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_assoc`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resource_licenses'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_licenses`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Authors
		if ($this->db->tableExists('#__author_assoc'))
		{
			$query = "DROP TABLE IF EXISTS `#__author_assoc`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__author_roles'))
		{
			$query = "DROP TABLE IF EXISTS `#__author_roles`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__author_role_types'))
		{
			$query = "DROP TABLE IF EXISTS `#__author_role_types`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Imports
		if ($this->db->tableExists('#__resource_imports'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_imports`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resource_import_runs'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_import_runs`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resource_import_hooks'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_import_hooks`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Taxonomy
		if ($this->db->tableExists('#__resource_taxonomy_audience'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_taxonomy_audience`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resource_taxonomy_audience_levels'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_taxonomy_audience_levels`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
