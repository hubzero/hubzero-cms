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
 * Migration script for installing poll tables
 **/
class Migration20170901000000ComPoll extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__polls'))
		{
			$query = "CREATE TABLE `#__polls` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(150) NOT NULL DEFAULT '',
			  `alias` varchar(255) NOT NULL DEFAULT '',
			  `voters` int(9) NOT NULL DEFAULT '0',
			  `checked_out` int(11) NOT NULL DEFAULT '0',
			  `checked_out_time` datetime DEFAULT NULL,
			  `state` tinyint(1) DEFAULT '0',
			  `access` int(11) NOT NULL DEFAULT '0',
			  `lag` int(11) NOT NULL DEFAULT '0',
			  `open` tinyint(1) NOT NULL DEFAULT '0',
			  `opened` date DEFAULT NULL,
			  `closed` date DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__poll_options'))
		{
			$query = "CREATE TABLE `#__poll_options` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `poll_id` int(11) DEFAULT '0',
			  `text` text NOT NULL,
			  `hits` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_pollid_text` (`poll_id`,`text`(1))
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__poll_menus'))
		{
			$query = "CREATE TABLE `#__poll_menus` (
			  `poll_id` int(11) NOT NULL DEFAULT '0',
			  `menu_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`poll_id`,`menu_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__poll_dates'))
		{
			$query = "CREATE TABLE `#__poll_dates` (
			  `id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `date` datetime DEFAULT NULL,
			  `vote_id` int(11) NOT NULL DEFAULT '0',
			  `poll_id` int(11) NOT NULL DEFAULT '0',
			  `voter_ip` varchar(50) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_poll_id` (`poll_id`)
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
		if ($this->db->tableExists('#__polls'))
		{
			$query = "DROP TABLE IF EXISTS `#__polls`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__poll_options'))
		{
			$query = "DROP TABLE IF EXISTS `#__poll_options`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__poll_menus'))
		{
			$query = "DROP TABLE IF EXISTS `#__poll_menus`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__poll_dates'))
		{
			$query = "DROP TABLE IF EXISTS `#__poll_dates`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
