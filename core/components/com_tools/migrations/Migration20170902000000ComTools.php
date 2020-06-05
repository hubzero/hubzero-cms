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
 * Migration script for installing tools TRAC tables
 **/
class Migration20170902000000ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__trac_group_permission'))
		{
			$query = "CREATE TABLE `#__trac_group_permission` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `group_id` int(11) NOT NULL,
			  `action` varchar(255) NOT NULL,
			  `trac_project_id` int(11) NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_group_id_action_trac_project_id` (`group_id`,`action`,`trac_project_id`) USING BTREE
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__trac_project'))
		{
			$query = "CREATE TABLE `#__trac_project` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__trac_projects'))
		{
			$query = "CREATE TABLE `#__trac_projects` (
			  `id` int(11) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `type` int(11) NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__trac_user_permission'))
		{
			$query = "CREATE TABLE `#__trac_user_permission` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) DEFAULT NULL,
			  `action` varchar(255) DEFAULT NULL,
			  `trac_project_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_user_id_action_trac_project_id` (`user_id`,`action`,`trac_project_id`) USING BTREE
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_version_tracperm'))
		{
			$query = "CREATE TABLE `#__tool_version_tracperm` (
			  `tool_version_id` int(11) NOT NULL,
			  `tracperm` varchar(64) NOT NULL,
			  UNIQUE KEY `uidx_tool_version_id_tracperm` (`tool_version_id`,`tracperm`)
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
		if ($this->db->tableExists('#__trac_group_permission'))
		{
			$query = "DROP TABLE IF EXISTS `#__trac_group_permission`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__trac_project'))
		{
			$query = "DROP TABLE IF EXISTS `#__trac_project`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__trac_projects'))
		{
			$query = "DROP TABLE IF EXISTS `#__trac_projects`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__trac_user_permission'))
		{
			$query = "DROP TABLE IF EXISTS `#__trac_user_permission`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_version_tracperm'))
		{
			$query = "DROP TABLE IF EXISTS `#__tool_version_tracperm`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
