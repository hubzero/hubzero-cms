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
 * Migration script for installing KB tables
 **/
class Migration20170901000000ComKb extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__kb_articles'))
		{
			$query = "CREATE TABLE `#__kb_articles` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `title` varchar(250) DEFAULT NULL,
			  `alias` varchar(200) DEFAULT NULL,
			  `params` text,
			  `fulltxt` text,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) DEFAULT '0',
			  `checked_out` int(11) DEFAULT '0',
			  `checked_out_time` datetime DEFAULT NULL,
			  `state` int(3) DEFAULT '0',
			  `access` tinyint(3) DEFAULT '0',
			  `hits` int(11) DEFAULT '0',
			  `version` int(11) DEFAULT '0',
			  `category` int(11) DEFAULT '0',
			  `helpful` int(11) NOT NULL DEFAULT '0',
			  `nothelpful` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_category` (`category`),
			  KEY `idx_alias` (`alias`),
			  FULLTEXT KEY `ftidx_title` (`title`),
			  FULLTEXT KEY `ftidx_title_params_fulltxt` (`title`,`params`,`fulltxt`),
			  FULLTEXT KEY `ftidx_params` (`params`),
			  FULLTEXT KEY `ftidx_fulltxt` (`fulltxt`),
			  FULLTEXT KEY `ftidx_title_fulltxt` (`title`,`fulltxt`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__kb_comments'))
		{
			$query = "CREATE TABLE `#__kb_comments` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `entry_id` int(11) NOT NULL DEFAULT '0',
			  `content` text,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `anonymous` tinyint(2) NOT NULL DEFAULT '0',
			  `parent` int(11) NOT NULL DEFAULT '0',
			  `asset_id` int(11) NOT NULL DEFAULT '0',
			  `helpful` int(11) NOT NULL DEFAULT '0',
			  `nothelpful` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_entry_id` (`entry_id`),
			  KEY `idx_state` (`state`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__kb_votes'))
		{
			$query = "CREATE TABLE `#__kb_votes` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `object_id` int(11) DEFAULT '0',
			  `ip` varchar(15) DEFAULT NULL,
			  `vote` varchar(10) DEFAULT NULL,
			  `user_id` int(11) DEFAULT '0',
			  `type` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_type_object_id` (`type`,`object_id`),
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
		if ($this->db->tableExists('#__kb_articles'))
		{
			$query = "DROP TABLE IF EXISTS `#__kb_articles`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__kb_comments'))
		{
			$query = "DROP TABLE IF EXISTS `#__kb_comments`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__kb_votes'))
		{
			$query = "DROP TABLE IF EXISTS `#__kb_votes`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
