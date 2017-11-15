<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing feedback tables
 **/
class Migration20170901000000ComFeedback extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__feedback'))
		{
			$query = "CREATE TABLE `#__feedback` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) DEFAULT NULL,
			  `fullname` varchar(100) DEFAULT '',
			  `org` varchar(100) DEFAULT '',
			  `quote` text,
			  `picture` varchar(250) DEFAULT '',
			  `date` datetime DEFAULT '0000-00-00 00:00:00',
			  `publish_ok` tinyint(1) DEFAULT '0',
			  `contact_ok` tinyint(1) DEFAULT '0',
			  `notes` text,
			  `short_quote` text,
			  `miniquote` varchar(255) NOT NULL DEFAULT '',
			  `admin_rating` tinyint(1) NOT NULL DEFAULT '0',
			  `notable_quote` tinyint(1) NOT NULL DEFAULT '0',
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
		if ($this->db->tableExists('#__feedback'))
		{
			$query = "DROP TABLE IF EXISTS `#__feedback`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
