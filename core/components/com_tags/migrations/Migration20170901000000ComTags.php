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
 * Migration script for installing tags tables
 **/
class Migration20170901000000ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__tags'))
		{
			$query = "CREATE TABLE `#__tags` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `tag` varchar(100) NOT NULL DEFAULT '',
			  `raw_tag` varchar(100) NOT NULL DEFAULT '',
			  `description` text NOT NULL,
			  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `objects` int(11) NOT NULL DEFAULT '0',
			  `substitutes` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_tag` (`tag`),
			  KEY `idx_objects` (`objects`),
			  KEY `idx_substitutes` (`substitutes`),
			  FULLTEXT KEY `ftidx_description` (`description`),
			  FULLTEXT KEY `ftidx_raw_tag_description` (`raw_tag`,`description`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tags_object'))
		{
			$query = "CREATE TABLE `#__tags_object` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `objectid` int(11) unsigned NOT NULL DEFAULT '0',
			  `tagid` int(11) unsigned NOT NULL DEFAULT '0',
			  `strength` tinyint(3) NOT NULL DEFAULT '0',
			  `taggerid` int(11) unsigned NOT NULL DEFAULT '0',
			  `taggedon` datetime DEFAULT NULL,
			  `tbl` varchar(255) NOT NULL DEFAULT '',
			  `label` varchar(30) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`),
			  KEY `idx_objectid_tbl` (`objectid`,`tbl`),
			  KEY `idx_label_tagid` (`label`,`tagid`),
			  KEY `idx_tbl_objectid_label_tagid` (`tbl`,`objectid`,`label`,`tagid`),
			  KEY `idx_tagid` (`tagid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tags_substitute'))
		{
			$query = "CREATE TABLE `#__tags_substitute` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `tag_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `tag` varchar(100) NOT NULL DEFAULT '',
			  `raw_tag` varchar(100) NOT NULL DEFAULT '',
			  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_tag_id` (`tag_id`),
			  KEY `idx_tag` (`tag`),
			  KEY `idx_created_by` (`created_by`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tags_log'))
		{
			$query = "CREATE TABLE `#__tags_log` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `tag_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `timestamp` datetime DEFAULT NULL,
			  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `action` varchar(50) NOT NULL DEFAULT '',
			  `comments` text NOT NULL,
			  `actorid` int(11) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_tag_id` (`tag_id`),
			  KEY `idx_user_id` (`user_id`)
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
		if ($this->db->tableExists('#__tags'))
		{
			$query = "DROP TABLE IF EXISTS `#__tags`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tags_object'))
		{
			$query = "DROP TABLE IF EXISTS `#__tags_object`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tags_substitute'))
		{
			$query = "DROP TABLE IF EXISTS `#__tags_substitute`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tags_log'))
		{
			$query = "DROP TABLE IF EXISTS `#__tags_log`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
