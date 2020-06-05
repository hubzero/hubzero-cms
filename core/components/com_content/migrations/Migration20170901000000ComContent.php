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
 * Migration script for installing content tables
 **/
class Migration20170901000000ComContent extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__content'))
		{
			$query = "CREATE TABLE `#__content` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
			  `title_alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'Deprecated in HUBzero 2.0',
			  `introtext` mediumtext NOT NULL,
			  `fulltext` mediumtext NOT NULL,
			  `state` tinyint(3) NOT NULL DEFAULT '0',
			  `sectionid` int(10) unsigned NOT NULL DEFAULT '0',
			  `mask` int(10) unsigned NOT NULL DEFAULT '0',
			  `catid` int(10) unsigned NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
			  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
			  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
			  `checked_out_time` datetime DEFAULT NULL,
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  `images` text NOT NULL,
			  `urls` text NOT NULL,
			  `attribs` varchar(5120) NOT NULL,
			  `version` int(10) unsigned NOT NULL DEFAULT '1',
			  `parentid` int(10) unsigned NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `metakey` text NOT NULL,
			  `metadesc` text NOT NULL,
			  `access` int(10) unsigned NOT NULL DEFAULT '0',
			  `hits` int(10) unsigned NOT NULL DEFAULT '0',
			  `metadata` text NOT NULL,
			  `featured` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Set if article is featured.',
			  `language` char(7) NOT NULL COMMENT 'The language code for the article.',
			  `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
			  PRIMARY KEY (`id`),
			  KEY `idx_access` (`access`),
			  KEY `idx_checkout` (`checked_out`),
			  KEY `idx_state` (`state`),
			  KEY `idx_catid` (`catid`),
			  KEY `idx_createdby` (`created_by`),
			  KEY `idx_featured_catid` (`featured`,`catid`),
			  KEY `idx_language` (`language`),
			  KEY `idx_xreference` (`xreference`),
			  FULLTEXT KEY `ftidx_title` (`title`),
			  FULLTEXT KEY `ftidx_introtext_fulltext` (`introtext`,`fulltext`),
			  FULLTEXT KEY `ftidx_title_introtext_fulltext` (`title`,`introtext`,`fulltext`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__content_frontpage'))
		{
			$query = "CREATE TABLE `#__content_frontpage` (
			  `content_id` int(11) NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`content_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__content_rating'))
		{
			$query = "CREATE TABLE `#__content_rating` (
			  `content_id` int(11) NOT NULL DEFAULT '0',
			  `rating_sum` int(10) unsigned NOT NULL DEFAULT '0',
			  `rating_count` int(10) unsigned NOT NULL DEFAULT '0',
			  `lastip` varchar(50) NOT NULL DEFAULT '',
			  PRIMARY KEY (`content_id`)
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
		if ($this->db->tableExists('#__content'))
		{
			$query = "DROP TABLE IF EXISTS `#__content`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__content_frontpage'))
		{
			$query = "DROP TABLE IF EXISTS `#__content_frontpage`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__content_rating'))
		{
			$query = "DROP TABLE IF EXISTS `#__content_rating`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
