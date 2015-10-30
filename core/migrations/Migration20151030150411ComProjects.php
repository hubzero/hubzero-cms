<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding projects tables to support filesystem connections
 **/
class Migration20151030150411ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Add connections table
		if (!$this->db->tableExists('#__projects_connections'))
		{
			$query = "CREATE TABLE `#__projects_connections` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`name` varchar(255) DEFAULT NULL,
						`project_id` int(11) NOT NULL,
						`provider_id` int(11) NOT NULL,
						`params` text,
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Add providers table
		if (!$this->db->tableExists('#__projects_connection_providers'))
		{
			$query = "CREATE TABLE `#__projects_connection_providers` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`alias` varchar(255) NOT NULL DEFAULT '',
						`name` varchar(255) NOT NULL DEFAULT '',
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// Drop connections table
		if ($this->db->tableExists('#__projects_connections'))
		{
			$query = "DROP TABLE `#__projects_connections`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Drop providers table
		if ($this->db->tableExists('#__projects_connection_providers'))
		{
			$query = "DROP TABLE `#__projects_connection_providers`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}