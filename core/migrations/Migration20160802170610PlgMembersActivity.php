<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add table for tracking activity digest settings
 **/
class Migration20160802170610PlgMembersActivity extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__activity_digests'))
		{
			$query = "CREATE TABLE `#__activity_digests` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `scope` varchar(250) NOT NULL,
			  `scope_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `frequency` tinyint(3) unsigned NOT NULL DEFAULT '0',
			  `sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `idx_user_id` (`scope_id`),
			  KEY `idx_frequency` (`frequency`)
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
		if ($this->db->tableExists('#__activity_digests'))
		{
			$query = "DROP TABLE `#__activity_digests`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
