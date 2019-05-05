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
 * Migration script for installing wiki tables
 **/
class Migration20170901000000ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__wiki_pages'))
		{
			$query = "CREATE TABLE `#__wiki_pages` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `namespace` varchar(255) NOT NULL,
			  `pagename` varchar(100) DEFAULT NULL,
			  `path` varchar(255) NOT NULL,
			  `hits` int(11) NOT NULL DEFAULT '0',
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
			  `times_rated` int(11) NOT NULL DEFAULT '0',
			  `title` varchar(255) DEFAULT NULL,
			  `scope` varchar(255) NOT NULL,
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `params` tinytext,
			  `ranking` float DEFAULT '0',
			  `access` tinyint(2) DEFAULT '0',
			  `state` tinyint(2) DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `version_id` int(11) NOT NULL DEFAULT '0',
			  `protected` tinyint(2) NOT NULL DEFAULT '0',
			  `parent` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_state` (`state`),
			  FULLTEXT KEY `ftidx_title` (`title`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wiki_versions'))
		{
			$query = "CREATE TABLE `#__wiki_versions` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `page_id` int(11) NOT NULL DEFAULT '0',
			  `version` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `minor_edit` int(1) NOT NULL DEFAULT '0',
			  `pagetext` text,
			  `pagehtml` text,
			  `approved` int(1) NOT NULL DEFAULT '0',
			  `summary` varchar(255) DEFAULT NULL,
			  `length` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_pageid` (`page_id`),
			  KEY `idx_approved` (`approved`),
			  FULLTEXT KEY `ftidx_pagetext` (`pagetext`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wiki_attachments'))
		{
			$query = "CREATE TABLE `#__wiki_attachments` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `page_id` int(11) DEFAULT '0',
			  `filename` varchar(255) DEFAULT NULL,
			  `description` tinytext,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_pageid` (`page_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wiki_authors'))
		{
			$query = "CREATE TABLE `#__wiki_authors` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) DEFAULT '0',
			  `page_id` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_page_id` (`page_id`),
			  KEY `idx_user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wiki_comments'))
		{
			$query = "CREATE TABLE `#__wiki_comments` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `page_id` int(11) DEFAULT '0',
			  `version` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `ctext` text,
			  `chtml` text,
			  `rating` tinyint(1) NOT NULL DEFAULT '0',
			  `anonymous` tinyint(1) NOT NULL DEFAULT '0',
			  `parent` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_pageid` (`page_id`),
			  KEY `idx_version` (`version`),
			  KEY `idx_status` (`state`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wiki_links'))
		{
			$query = "CREATE TABLE `#__wiki_links` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `page_id` int(11) NOT NULL DEFAULT '0',
			  `timestamp` datetime DEFAULT NULL,
			  `scope` varchar(50) NOT NULL DEFAULT '',
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `link` varchar(255) NOT NULL DEFAULT '',
			  `url` varchar(250) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`),
			  KEY `idx_page_id` (`page_id`),
			  KEY `idx_scope_scope_id` (`scope`,`scope_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wiki_logs'))
		{
			$query = "CREATE TABLE `#__wiki_logs` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `page_id` int(11) NOT NULL DEFAULT '0',
			  `timestamp` datetime DEFAULT NULL,
			  `user_id` int(11) DEFAULT '0',
			  `action` varchar(50) DEFAULT NULL,
			  `comments` text,
			  `actorid` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wiki_metrics'))
		{
			$query = "CREATE TABLE `#__wiki_metrics` (
			  `page_id` int(11) NOT NULL DEFAULT '0',
			  `pagename` varchar(100) DEFAULT NULL,
			  `hits` int(11) NOT NULL DEFAULT '0',
			  `visitors` int(11) NOT NULL DEFAULT '0',
			  `visits` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`page_id`)
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
		if ($this->db->tableExists('#__wiki_pages'))
		{
			$query = "DROP TABLE IF EXISTS `#__wiki_pages`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_versions'))
		{
			$query = "DROP TABLE IF EXISTS `#__wiki_versions`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_attachments'))
		{
			$query = "DROP TABLE IF EXISTS `#__wiki_attachments`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_authors'))
		{
			$query = "DROP TABLE IF EXISTS `#__wiki_authors`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_comments'))
		{
			$query = "DROP TABLE IF EXISTS `#__wiki_comments`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_links'))
		{
			$query = "DROP TABLE IF EXISTS `#__wiki_links`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_logs'))
		{
			$query = "DROP TABLE IF EXISTS `#__wiki_logs`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_metrics'))
		{
			$query = "DROP TABLE IF EXISTS `#__wiki_metrics`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
