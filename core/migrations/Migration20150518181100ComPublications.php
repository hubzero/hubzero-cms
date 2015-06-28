<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding item_watch table
 **/
class Migration20150518181100ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__item_watch'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__item_watch` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `item_id` int(11) NOT NULL DEFAULT '0',
				  `item_type` varchar(150) NOT NULL,
				  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `created_by` int(11) NOT NULL DEFAULT '0',
				  `email` varchar(150),
				  `state` tinyint(2) NOT NULL DEFAULT '0',
				  `params` text,
				  PRIMARY KEY (`id`),
				  KEY `idx_item_type_item_id` (`item_type`,`item_id`),
				  KEY `idx_created_by_email` (`created_by`, `email`)
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
		if ($this->db->tableExists('#__item_watch'))
		{
			$query = "DROP TABLE IF EXISTS `#__item_watch`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}