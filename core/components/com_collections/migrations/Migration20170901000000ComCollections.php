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
 * Migration script for installing collections tables
 **/
class Migration20170901000000ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__collections'))
		{
			$query = "CREATE TABLE `#__collections` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `alias` varchar(255) NOT NULL,
			  `object_id` int(11) NOT NULL DEFAULT '0',
			  `object_type` varchar(150) NOT NULL DEFAULT '',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(3) NOT NULL DEFAULT '1',
			  `access` tinyint(3) NOT NULL DEFAULT '0',
			  `is_default` tinyint(2) NOT NULL DEFAULT '0',
			  `description` mediumtext NOT NULL,
			  `positive` int(11) NOT NULL DEFAULT '0',
			  `negative` int(11) NOT NULL DEFAULT '0',
			  `sort` varchar(50) NOT NULL DEFAULT 'created',
			  `layout` varchar(50) NOT NULL DEFAULT 'grid',
			  PRIMARY KEY (`id`),
			  KEY `idx_object_type_object_id` (`object_type`,`object_id`),
			  KEY `idx_state` (`state`),
			  KEY `idx_access` (`access`),
			  KEY `idx_created_by` (`created_by`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__collections_items'))
		{
			$query = "CREATE TABLE `#__collections_items` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `description` mediumtext NOT NULL,
			  `url` varchar(255) NOT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(3) NOT NULL DEFAULT '1',
			  `access` tinyint(2) NOT NULL DEFAULT '0',
			  `positive` int(11) NOT NULL DEFAULT '0',
			  `negative` int(11) NOT NULL DEFAULT '0',
			  `type` varchar(150) NOT NULL DEFAULT '',
			  `object_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_state` (`state`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_type_object_id` (`type`,`object_id`),
			  FULLTEXT KEY `idx_fulltxt_title_description` (`title`,`description`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__collections_posts'))
		{
			$query = "CREATE TABLE `#__collections_posts` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `collection_id` int(11) NOT NULL DEFAULT '0',
			  `item_id` int(11) NOT NULL DEFAULT '0',
			  `description` mediumtext NOT NULL,
			  `original` tinyint(2) NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_collection_id` (`collection_id`),
			  KEY `idx_item_id` (`item_id`),
			  KEY `idx_original` (`original`),
			  FULLTEXT KEY `idx_fulltxt_description` (`description`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__collections_assets'))
		{
			$query = "CREATE TABLE `#__collections_assets` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `item_id` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `filename` varchar(255) NOT NULL DEFAULT '',
			  `description` mediumtext NOT NULL,
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  `type` varchar(50) NOT NULL DEFAULT 'file',
			  `ordering` tinyint(3) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_item_id` (`item_id`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_state` (`state`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__collections_votes'))
		{
			$query = "CREATE TABLE `#__collections_votes` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `item_id` int(11) NOT NULL DEFAULT '0',
			  `voted` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_item_id_user_id` (`item_id`,`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__collections_following'))
		{
			$query = "CREATE TABLE `#__collections_following` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `follower_type` varchar(150) NOT NULL,
			  `follower_id` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `following_type` varchar(150) NOT NULL DEFAULT '',
			  `following_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_follower_type_follower_id` (`follower_type`,`follower_id`),
			  KEY `idx_following_type_following_id` (`following_type`,`following_id`)
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
		if ($this->db->tableExists('#__collections'))
		{
			$query = "DROP TABLE IF EXISTS `#__collections`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__collections_items'))
		{
			$query = "DROP TABLE IF EXISTS `#__collections_items`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__collections_posts'))
		{
			$query = "DROP TABLE IF EXISTS `#__collections_posts`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__collections_assets'))
		{
			$query = "DROP TABLE IF EXISTS `#__collections_assets`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__collections_votes'))
		{
			$query = "DROP TABLE IF EXISTS `#__collections_votes`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__collections_following'))
		{
			$query = "DROP TABLE IF EXISTS `#__collections_following`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
