<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing forum tables
 **/
class Migration20170901000000ComForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__forum_sections'))
		{
			$query = "CREATE TABLE `#__forum_sections` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) DEFAULT NULL,
			  `alias` varchar(255) DEFAULT NULL,
			  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `access` tinyint(2) NOT NULL DEFAULT '0',
			  `state` tinyint(3) NOT NULL DEFAULT '0',
			  `scope` varchar(100) NOT NULL DEFAULT 'site',
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `asset_id` int(11) NOT NULL DEFAULT '0',
			  `object_id` int(11) NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_asset_id` (`asset_id`),
			  KEY `idx_object_id` (`object_id`),
			  KEY `idx_scoped` (`scope`,`scope_id`),
			  KEY `idx_access` (`access`),
			  KEY `idx_state` (`state`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__forum_categories'))
		{
			$query = "CREATE TABLE `#__forum_categories` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) DEFAULT NULL,
			  `alias` varchar(255) DEFAULT NULL,
			  `description` text,
			  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `access` tinyint(2) NOT NULL DEFAULT '0',
			  `state` tinyint(3) NOT NULL DEFAULT '0',
			  `scope` varchar(100) NOT NULL DEFAULT 'site',
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `section_id` int(11) NOT NULL DEFAULT '0',
			  `closed` tinyint(2) NOT NULL DEFAULT '0',
			  `asset_id` int(11) NOT NULL DEFAULT '0',
			  `object_id` int(11) NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_asset_id` (`asset_id`),
			  KEY `idx_object_id` (`object_id`),
			  KEY `idx_section_id` (`section_id`),
			  KEY `idx_state` (`state`),
			  KEY `idx_access` (`access`),
			  KEY `idx_closed` (`closed`),
			  KEY `idx_scoped` (`scope`,`scope_id`),
			  KEY `idx_scope_scope_id` (`scope`,`scope_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__forum_posts'))
		{
			$query = "CREATE TABLE `#__forum_posts` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `category_id` int(11) NOT NULL DEFAULT '0',
			  `title` varchar(255) DEFAULT NULL,
			  `comment` text,
			  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(3) NOT NULL DEFAULT '0',
			  `sticky` tinyint(2) NOT NULL DEFAULT '0',
			  `parent` int(11) NOT NULL DEFAULT '0',
			  `hits` int(11) NOT NULL DEFAULT '0',
			  `scope` varchar(100) NOT NULL DEFAULT 'site',
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `scope_sub_id` int(11) NOT NULL DEFAULT '0',
			  `access` tinyint(2) NOT NULL DEFAULT '0',
			  `anonymous` tinyint(2) NOT NULL DEFAULT '0',
			  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `last_activity` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `asset_id` int(11) NOT NULL DEFAULT '0',
			  `object_id` int(11) NOT NULL DEFAULT '0',
			  `lft` int(11) NOT NULL DEFAULT '0',
			  `rgt` int(11) NOT NULL DEFAULT '0',
			  `thread` int(11) NOT NULL DEFAULT '0',
			  `closed` tinyint(2) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_category_id` (`category_id`),
			  KEY `idx_scoped` (`scope`,`scope_id`),
			  KEY `idx_access` (`access`),
			  KEY `idx_object_id` (`object_id`),
			  KEY `idx_state` (`state`),
			  KEY `idx_sticky` (`sticky`),
			  KEY `idx_parent` (`parent`),
			  KEY `idx_asset_id` (`asset_id`),
			  KEY `idx_scope_scope_id` (`scope`,`scope_id`),
			  FULLTEXT KEY `ftidx_comment_title` (`comment`,`title`),
			  FULLTEXT KEY `ftidx_comment` (`comment`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__forum_attachments'))
		{
			$query = "CREATE TABLE `#__forum_attachments` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `parent` int(11) NOT NULL DEFAULT '0',
			  `post_id` int(11) NOT NULL DEFAULT '0',
			  `filename` varchar(255) DEFAULT NULL,
			  `description` varchar(255) DEFAULT NULL,
			  `state` int(3) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_filename_postid` (`filename`,`post_id`),
			  KEY `idx_parent` (`parent`),
			  KEY `idx_filename_post_id` (`filename`,`post_id`)
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
		if ($this->db->tableExists('#__forum_sections'))
		{
			$query = "DROP TABLE IF EXISTS `#__forum_sections`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__forum_categories'))
		{
			$query = "DROP TABLE IF EXISTS `#__forum_categories`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__forum_posts'))
		{
			$query = "DROP TABLE IF EXISTS `#__forum_posts`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__forum_attachments'))
		{
			$query = "DROP TABLE IF EXISTS `#__forum_attachments`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
