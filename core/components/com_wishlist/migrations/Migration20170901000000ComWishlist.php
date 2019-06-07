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
 * Migration script for installing wishlist tables
 **/
class Migration20170901000000ComWishlist extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__wishlist'))
		{
			$query = "CREATE TABLE `#__wishlist` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `category` varchar(50) NOT NULL,
			  `referenceid` int(11) NOT NULL DEFAULT '0',
			  `title` varchar(150) NOT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `state` int(3) NOT NULL DEFAULT '0',
			  `public` int(3) NOT NULL DEFAULT '1',
			  `description` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_category_referenceid` (`category`,`referenceid`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_state` (`state`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wishlist_item'))
		{
			$query = "CREATE TABLE `#__wishlist_item` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `wishlist` int(11) DEFAULT '0',
			  `subject` varchar(200) NOT NULL,
			  `about` text,
			  `proposed_by` int(11) DEFAULT '0',
			  `granted_by` int(11) DEFAULT '0',
			  `assigned` int(11) DEFAULT '0',
			  `granted_vid` int(11) DEFAULT '0',
			  `proposed` datetime DEFAULT NULL,
			  `granted` datetime DEFAULT NULL,
			  `status` int(3) NOT NULL DEFAULT '0',
			  `due` datetime DEFAULT NULL,
			  `anonymous` int(3) DEFAULT '0',
			  `ranking` int(11) DEFAULT '0',
			  `points` int(11) DEFAULT '0',
			  `private` int(3) DEFAULT '0',
			  `accepted` int(3) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_wishlist` (`wishlist`),
			  FULLTEXT KEY `ftidx_subject_about` (`subject`,`about`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wishlist_owners'))
		{
			$query = "CREATE TABLE `#__wishlist_owners` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `wishlist` int(11) unsigned NOT NULL DEFAULT '0',
			  `userid` int(11) unsigned NOT NULL DEFAULT '0',
			  `type` int(11) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_wishlist` (`wishlist`),
			  KEY `idx_userid` (`userid`),
			  KEY `idx_type` (`type`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wishlist_ownergroups'))
		{
			$query = "CREATE TABLE `#__wishlist_ownergroups` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `wishlist` int(11) unsigned NOT NULL DEFAULT '0',
			  `groupid` int(11) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_wishlist` (`wishlist`),
			  KEY `idx_groupid` (`groupid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wishlist_vote'))
		{
			$query = "CREATE TABLE `#__wishlist_vote` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `wishid` int(11) unsigned NOT NULL DEFAULT '0',
			  `userid` int(11) unsigned NOT NULL DEFAULT '0',
			  `voted` datetime DEFAULT NULL,
			  `importance` int(3) unsigned NOT NULL DEFAULT '0',
			  `effort` int(3) NOT NULL DEFAULT '0',
			  `due` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_wishid` (`wishid`),
			  KEY `idx_userid` (`userid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wishlist_implementation'))
		{
			$query = "CREATE TABLE `#__wishlist_implementation` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `wishid` int(11) NOT NULL DEFAULT '0',
			  `version` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `minor_edit` int(1) NOT NULL DEFAULT '0',
			  `pagetext` text,
			  `pagehtml` text,
			  `approved` int(1) NOT NULL DEFAULT '0',
			  `summary` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_wishid` (`wishid`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_approved` (`approved`),
			  FULLTEXT KEY `ftidx_pagetext` (`pagetext`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wish_attachments'))
		{
			$query = "CREATE TABLE `#__wish_attachments` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `wish` int(11) NOT NULL DEFAULT '0',
			  `filename` varchar(255) DEFAULT NULL,
			  `description` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_wish` (`wish`)
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
		if ($this->db->tableExists('#__wishlist'))
		{
			$query = "DROP TABLE IF EXISTS `#__wishlist`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wishlist_item'))
		{
			$query = "DROP TABLE IF EXISTS `#__wishlist_item`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wishlist_owners'))
		{
			$query = "DROP TABLE IF EXISTS `#__wishlist_owners`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wishlist_ownergroups'))
		{
			$query = "DROP TABLE IF EXISTS `#__wishlist_ownergroups`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wishlist_vote'))
		{
			$query = "DROP TABLE IF EXISTS `#__wishlist_vote`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wishlist_implementation'))
		{
			$query = "DROP TABLE IF EXISTS `#__wishlist_implementation`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wish_attachments'))
		{
			$query = "DROP TABLE IF EXISTS `#__wish_attachments`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
