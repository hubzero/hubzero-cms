<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding citations links table
 **/
class Migration20160111200400ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__citations_links'))
		{
			$query = "CREATE TABLE `#__citations_links` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `title` varchar(255) NOT NULL DEFAULT '',
				  `url` text,
				  `citation_id` int(11) unsigned NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  KEY `idx_citation_id` (`citation_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__citations_links'))
		{
			$query = "DROP TABLE IF EXISTS `#__citations_links`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}