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
 * Migration script for installing resources stats tables
 **/
class Migration20170902000000ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__resource_stats'))
		{
			$query = "CREATE TABLE `#__resource_stats` (
			  `id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `resid` bigint(20) NOT NULL,
			  `restype` int(11) DEFAULT NULL,
			  `users` bigint(20) DEFAULT NULL,
			  `jobs` bigint(20) DEFAULT NULL,
			  `avg_wall` int(20) DEFAULT NULL,
			  `tot_wall` int(20) DEFAULT NULL,
			  `avg_cpu` int(20) DEFAULT NULL,
			  `tot_cpu` int(20) DEFAULT NULL,
			  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `period` tinyint(4) NOT NULL DEFAULT '-1',
			  `processed_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_resid_restype_datetime_period` (`resid`,`restype`,`datetime`,`period`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__resource_stats_clusters'))
		{
			$query = "CREATE TABLE `#__resource_stats_clusters` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `cluster` varchar(255) NOT NULL DEFAULT '',
			  `username` varchar(32) NOT NULL DEFAULT '',
			  `uidNumber` int(11) NOT NULL DEFAULT '0',
			  `toolname` varchar(80) NOT NULL DEFAULT '',
			  `resid` int(11) NOT NULL DEFAULT '0',
			  `clustersize` varchar(255) NOT NULL DEFAULT '',
			  `cluster_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `cluster_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `institution` varchar(255) NOT NULL DEFAULT '',
			  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`),
			  KEY `idx_cluster` (`cluster`),
			  KEY `idx_username` (`username`),
			  KEY `idx_uidNumber` (`uidNumber`),
			  KEY `idx_toolname` (`toolname`),
			  KEY `idx_resid` (`resid`),
			  KEY `idx_clustersize` (`clustersize`),
			  KEY `idx_cluster_start` (`cluster_start`),
			  KEY `idx_cluster_end` (`cluster_end`),
			  KEY `idx_institution` (`institution`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__resource_stats_tools'))
		{
			$query = "CREATE TABLE `#__resource_stats_tools` (
			  `id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `resid` bigint(20) NOT NULL,
			  `restype` int(11) NOT NULL,
			  `users` bigint(20) DEFAULT NULL,
			  `sessions` bigint(20) DEFAULT NULL,
			  `simulations` bigint(20) DEFAULT NULL,
			  `jobs` bigint(20) DEFAULT NULL,
			  `avg_wall` double unsigned DEFAULT '0',
			  `tot_wall` double unsigned DEFAULT '0',
			  `avg_cpu` double unsigned DEFAULT '0',
			  `tot_cpu` double unsigned DEFAULT '0',
			  `avg_view` double unsigned DEFAULT '0',
			  `tot_view` double unsigned DEFAULT '0',
			  `avg_wait` double unsigned DEFAULT '0',
			  `tot_wait` double unsigned DEFAULT '0',
			  `avg_cpus` int(20) DEFAULT NULL,
			  `tot_cpus` int(20) DEFAULT NULL,
			  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `period` tinyint(4) NOT NULL DEFAULT '-1',
			  `processed_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=3908 DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__resource_stats_tools_tops'))
		{
			$query = "CREATE TABLE `#__resource_stats_tools_tops` (
			  `top` tinyint(4) NOT NULL DEFAULT '0',
			  `name` varchar(128) NOT NULL DEFAULT '',
			  `valfmt` tinyint(4) NOT NULL DEFAULT '0',
			  `size` tinyint(4) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`top`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__resource_stats_tools_topvals'))
		{
			$query = "CREATE TABLE `#__resource_stats_tools_topvals` (
			  `id` bigint(20) NOT NULL,
			  `top` tinyint(4) NOT NULL DEFAULT '0',
			  `rank` tinyint(4) NOT NULL DEFAULT '0',
			  `name` varchar(255) DEFAULT NULL,
			  `value` bigint(20) NOT NULL DEFAULT '0'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__resource_stats_tools_users'))
		{
			$query = "CREATE TABLE `#__resource_stats_tools_users` (
			  `id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `resid` bigint(20) NOT NULL,
			  `restype` int(11) NOT NULL,
			  `user` varchar(32) NOT NULL DEFAULT '',
			  `sessions` bigint(20) DEFAULT NULL,
			  `simulations` bigint(20) DEFAULT NULL,
			  `jobs` bigint(20) DEFAULT NULL,
			  `tot_wall` double unsigned DEFAULT '0',
			  `tot_cpu` double unsigned DEFAULT '0',
			  `tot_view` double unsigned DEFAULT '0',
			  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `period` tinyint(4) NOT NULL DEFAULT '-1',
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
		if ($this->db->tableExists('#__resource_stats'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_stats`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Imports
		if ($this->db->tableExists('#__resource_stats_clusters'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_stats_clusters`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resource_stats_tools'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_stats_tools`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resource_stats_tools_tops'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_stats_tools_tops`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Taxonomy
		if ($this->db->tableExists('#__resource_stats_tools_topvals'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_stats_tools_topvals`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resource_stats_tools_users'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_stats_tools_users`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
