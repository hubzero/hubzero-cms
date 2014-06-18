<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for feedaggregator tables
 **/
class Migration20140311150000ComFeedaggregator extends Base
{
	public function up()
	{
		$query = '';

		if (!$this->db->tableExists('#__feedaggregator_feeds'))
		{
			$query .= "CREATE TABLE IF NOT EXISTS `#__feedaggregator_feeds` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `url` varchar(255) DEFAULT NULL,
					  `created` date DEFAULT NULL,
					  `name` varchar(255) DEFAULT NULL,
					  `description` varchar(255) DEFAULT NULL,
					  `enabled` varchar(45) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id_UNIQUE` (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
		}
		if (!$this->db->tableExists('#__feedaggregator_posts'))
		{
			$query .= "CREATE TABLE IF NOT EXISTS `#__feedaggregator_posts` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `title` varchar(255) DEFAULT NULL,
					  `created` int(20) DEFAULT NULL,
					  `created_by` varchar(255) DEFAULT NULL,
					  `feed_id` int(11) NOT NULL,
					  `status` varchar(45) DEFAULT NULL,
					  `description` text,
					  `url` varchar(255) DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		$query = '';

		if ($this->db->tableExists('#__feedaggregator_posts'))
		{
			$query .= "DROP TABLE IF EXISTS `#__feedaggregator_posts`;\n";
		}
		if ($this->db->tableExists('#__feedaggregator_feeds'))
		{
			$query .= "DROP TABLE IF EXISTS `#__feedaggregator_feeds`;\n";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}