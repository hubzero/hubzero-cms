<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding wiki page links table
 **/
class Migration20130423115913ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableExists('#__wiki_page_links'))
		{
			$query .= "CREATE TABLE IF NOT EXISTS `#__wiki_page_links` (
							`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
							`page_id` int(11) NOT NULL DEFAULT '0',
							`timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`scope` varchar(50) NOT NULL DEFAULT '',
							`scope_id` int(11) NOT NULL DEFAULT '0',
							`link` varchar(255) NOT NULL DEFAULT '',
							`url` varchar(250) NOT NULL DEFAULT '',
							PRIMARY KEY (`id`),
							KEY `idx_page_id` (`page_id`),
							KEY `idx_scoped` (`scope`,`scope_id`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "";

		if ($this->db->tableExists('#__wiki_page_links'))
		{
			$query .= "DROP TABLE IF EXISTS `#__wiki_page_links`";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}