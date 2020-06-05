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
 * Migration script for installing blog tables
 **/
class Migration20170901000000ComBlog extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__blog_entries'))
		{
			$query = "CREATE TABLE `#__blog_entries` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `alias` varchar(255) NOT NULL DEFAULT '',
			  `content` text NOT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  `params` tinytext NOT NULL,
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `hits` int(11) unsigned NOT NULL DEFAULT '0',
			  `allow_comments` tinyint(2) NOT NULL DEFAULT '0',
			  `scope` varchar(100) NOT NULL DEFAULT '',
			  `access` tinyint(3) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_alias` (`alias`),
			  KEY `idx_scope_id` (`scope_id`),
			  FULLTEXT KEY `ftidx_title` (`title`),
			  FULLTEXT KEY `ftidx_content` (`content`),
			  FULLTEXT KEY `ftidx_title_content` (`title`,`content`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__blog_comments'))
		{
			$query = "CREATE TABLE `#__blog_comments` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `entry_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `content` text NOT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
			  `anonymous` tinyint(2) unsigned NOT NULL DEFAULT '0',
			  `parent` int(11) unsigned NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_entry_id` (`entry_id`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_parent` (`parent`)
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
		if ($this->db->tableExists('#__blog_entries'))
		{
			$query = "DROP TABLE IF EXISTS `#__blog_entries`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__blog_comments'))
		{
			$query = "DROP TABLE IF EXISTS `#__blog_comments`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
