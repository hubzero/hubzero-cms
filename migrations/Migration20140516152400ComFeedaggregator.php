<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for for adding time plugin for support
 **/
class Migration20140516152400ComFeedaggregator extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__feedaggregator_posts'))
		{
			$query = "DROP TABLE IF EXISTS `#__feedaggregator_posts`;\n";
			$this->db->setQuery($query);
			$this->db->query();
			
			$query = "CREATE TABLE IF NOT EXISTS `#__feedaggregator_posts` (
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
			
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "DROP TABLE IF EXISTS `#__feedaggregator_posts`;\n";
		$this->db->setQuery($query);
		$this->db->query();
	}
}