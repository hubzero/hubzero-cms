<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add table for resource related pages
 **/
class Migration20160817174610PlgResourcesWindowstools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__resource_pages'))
		{
			$query = "CREATE TABLE `#__resource_pages` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `alias` varchar(255) NOT NULL DEFAULT '',
			  `content` text NOT NULL,
			  `plugin` varchar(255) NOT NULL DEFAULT '',
			  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
			  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
			  `state` tinyint(3) NOT NULL DEFAULT '0',
			  `access` tinyint(3) NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `resource_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_plugin` (`plugin`),
			  KEY `idx_resource_id` (`resource_id`),
			  KEY `idx_state` (`state`),
			  KEY `idx_access` (`access`)
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
		if ($this->db->tableExists('#__resource_pages'))
		{
			$query = "DROP TABLE `#__resource_pages`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
