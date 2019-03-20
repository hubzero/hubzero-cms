<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing publications reviews table
 **/
class Migration20170901000000PlgPublicationsReviews extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__publication_ratings'))
		{
			$query = "CREATE TABLE `#__publication_ratings` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `publication_id` int(11) NOT NULL DEFAULT '0',
			  `publication_version_id` int(11) NOT NULL DEFAULT '0',
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
			  `comment` text NOT NULL,
			  `created` datetime DEFAULT NULL,
			  `anonymous` tinyint(3) NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`id`),
			  KEY `idx_publication_id` (`publication_id`),
			  KEY `idx_publication_version_id` (`publication_version_id`),
			  KEY `idx_state` (`state`),
			  KEY `idx_created_by` (`created_by`)
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
		if ($this->db->tableExists('#__publication_ratings'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_ratings`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
