<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing feedaggregator tables
 **/
class Migration20170901000000ComFeedaggregator extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__feedaggregator_feeds'))
		{
			$query = "CREATE TABLE `#__feedaggregator_feeds` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `url` varchar(255) DEFAULT NULL,
			  `created` date DEFAULT NULL,
			  `name` varchar(255) DEFAULT NULL,
			  `description` varchar(255) DEFAULT NULL,
			  `enabled` varchar(45) NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id_UNIQUE` (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__feedaggregator_posts'))
		{
			$query = "CREATE TABLE `#__feedaggregator_posts` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` varchar(255) DEFAULT NULL,
			  `feed_id` int(11) NOT NULL,
			  `status` varchar(45) DEFAULT NULL,
			  `description` text,
			  `url` varchar(255) DEFAULT NULL,
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
		if ($this->db->tableExists('#__feedaggregator_feeds'))
		{
			$query = "DROP TABLE IF EXISTS `#__feedaggregator_feeds`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__feedaggregator_posts'))
		{
			$query = "DROP TABLE IF EXISTS `#__feedaggregator_posts`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
