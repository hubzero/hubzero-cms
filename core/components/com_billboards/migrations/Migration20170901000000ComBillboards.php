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
 * Migration script for installing billboards tables
 **/
class Migration20170901000000ComBillboards extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__billboards_billboards'))
		{
			$query = "CREATE TABLE `#__billboards_billboards` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `collection_id` int(11) DEFAULT NULL,
			  `name` varchar(255) DEFAULT NULL,
			  `header` varchar(255) DEFAULT NULL,
			  `text` text,
			  `learn_more_text` varchar(255) DEFAULT NULL,
			  `learn_more_target` varchar(255) DEFAULT NULL,
			  `learn_more_class` varchar(255) DEFAULT NULL,
			  `learn_more_location` varchar(255) DEFAULT NULL,
			  `background_img` varchar(255) DEFAULT NULL,
			  `padding` varchar(255) DEFAULT NULL,
			  `alias` varchar(255) DEFAULT NULL,
			  `css` text,
			  `published` tinyint(1) DEFAULT '0',
			  `ordering` int(11) DEFAULT NULL,
			  `checked_out` int(11) DEFAULT '0',
			  `checked_out_time` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_collection_id` (`collection_id`),
			  KEY `idx_published` (`published`),
			  KEY `idx_alias` (`alias`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__billboards_collections'))
		{
			$query = "CREATE TABLE `#__billboards_collections` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) DEFAULT NULL,
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
		if ($this->db->tableExists('#__billboards_billboards'))
		{
			$query = "DROP TABLE IF EXISTS `#__billboards_billboards`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__billboards_collections'))
		{
			$query = "DROP TABLE IF EXISTS `#__billboards_collections`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
