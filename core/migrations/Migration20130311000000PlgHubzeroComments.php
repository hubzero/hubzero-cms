<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for creating centralized comments table
 **/
class Migration20130311000000PlgHubzeroComments extends Base
{
	public function up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__item_comments` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`item_id` int(11) NOT NULL DEFAULT '0',
				`item_type` varchar(150) NOT NULL,
				`content` text NOT NULL,
				`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created_by` int(11) NOT NULL DEFAULT '0',
				`modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`modified_by` int(11) NOT NULL DEFAULT '0',
				`anonymous` tinyint(2) NOT NULL DEFAULT '0',
				`parent` int(11) NOT NULL DEFAULT '0',
				`notify` tinyint(2) NOT NULL DEFAULT '0',
				`access` tinyint(2) NOT NULL DEFAULT '0',
				`state` tinyint(2) NOT NULL DEFAULT '0',
				`positive` int(11) NOT NULL DEFAULT '0',
				`negative` int(11) NOT NULL DEFAULT '0',
				`rating` int(2) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `#__item_comment_files` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`comment_id` int(11) NOT NULL DEFAULT '0',
				`filename` varchar(100) DEFAULT NULL,
				KEY `id` (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `#__item_votes` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`item_id` int(11) NOT NULL DEFAULT '0',
				`item_type` varchar(255) DEFAULT NULL,
				`ip` varchar(15) DEFAULT NULL,
				`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created_by` int(11) NOT NULL DEFAULT '0',
				`vote` tinyint(3) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		$this->db->setQuery($query);
		$this->db->query();
	}

	public function down()
	{
		$query = "DROP TABLE IF EXISTS `#__item_comments`;
				DROP TABLE IF EXISTS `#__item_comment_files`;
				DROP TABLE IF EXISTS `#__item_votes`;";

		$this->db->setQuery($query);
		$this->db->query();
	}
}