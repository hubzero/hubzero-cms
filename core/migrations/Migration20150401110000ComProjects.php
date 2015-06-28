<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding project tool tables
 **/
class Migration20150401110000ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__project_tools'))
		{
			$query = "CREATE TABLE `jos_project_tools` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `name` varchar(64) NOT NULL DEFAULT '',
			  `title` varchar(127) NOT NULL DEFAULT '',
			  `repotype` tinyint(1) NOT NULL DEFAULT '1',
			  `repopath` varchar(255) NOT NULL DEFAULT '',
			  `status` int(11) NOT NULL DEFAULT '0',
			  `status_changed` varchar(31) NOT NULL,
			  `status_changed_by` int(11) NOT NULL,
			  `created` datetime NOT NULL,
			  `created_by` int(11) NOT NULL,
			  `svntool_id` int(11) NOT NULL,
			  `project_id` int(11) NOT NULL,
			  `published` tinyint(1) NOT NULL DEFAULT '0',
			  `opendev` tinyint(1) NOT NULL DEFAULT '0',
			  `opensource` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `toolname` (`name`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableExists('#__project_tool_instances'))
		{
			$query = "CREATE TABLE `jos_project_tool_instances` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `parent_id` int(11) NOT NULL DEFAULT '0',
			  `parent_name` varchar(64) NOT NULL DEFAULT '',
			  `instance` varchar(100) NOT NULL DEFAULT '',
			  `revision` int(11) NOT NULL,
			  `commit` varchar(255) NOT NULL,
			  `access` varchar(16) NOT NULL,
			  `state` int(11) NOT NULL,
			  `created_by` int(11) NOT NULL,
			  `created` datetime NOT NULL,
			  `modified_by` int(11),
			  `modified` datetime,
			  `svntool_version_id` int(11) DEFAULT NULL,
			  `params` text,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `toolname` (`parent_name`,`instance`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableExists('#__project_tool_logs'))
		{
			$query = "CREATE TABLE `jos_project_tool_logs` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `parent_id` int(11) NOT NULL DEFAULT '0',
			  `parent_name` varchar(64) NOT NULL DEFAULT '',
			  `instance_id` int(11) DEFAULT NULL,
			  `action` varchar(255) NOT NULL DEFAULT '',
			  `actor` int(15) NOT NULL,
			  `recorded` datetime NOT NULL,
			  `project_activity_id` int(15) NOT NULL DEFAULT '0',
			  `status_changed` tinyint(1) NOT NULL DEFAULT '0',
			  `admin` tinyint(1) NOT NULL DEFAULT '0',
			  `access` tinyint(1) NOT NULL DEFAULT '0',
			  `log` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableExists('#__project_tool_views'))
		{
			$query = "CREATE TABLE `jos_project_tool_views` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `parent_id` int(11) NOT NULL DEFAULT '0',
			  `userid` int(15) NOT NULL,
			  `viewed` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableExists('#__project_tool_statuses'))
		{
			$query = "CREATE TABLE `jos_project_tool_statuses` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `status` varchar(100) NOT NULL DEFAULT '',
			  `status_about` text,
			  `next` varchar(100) NOT NULL DEFAULT '',
			  `next_admin` varchar(100) NOT NULL DEFAULT '',
			  `next_about` text,
			  `next_actor` tinyint(1) NOT NULL DEFAULT '0',
			  `wait_time` varchar(100) NOT NULL DEFAULT '',
			  `options` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "INSERT INTO `jos_project_tool_statuses` (`id`, `status`, `status_about`, `next`, `next_admin`, `next_about`, `next_actor`, `wait_time`, `options`)
			VALUES
				(1,'created','','upload your code and request install','wait for develper to upload code','You need to upload your code into the tool <a href=\"{app-source}\">repository</a>. When your code is ready for install, notify administrator via this screen.',0,'','{\"option-message\":\"1\",\"option-cancel\":\"1\"}'),
				(2,'deleted','','','','',2,'',''),
				(3,'uploaded','','wait for admin to install latest code','install developer code','Administrator need to intsall your uploaded code. You will get notified when the code is installed.',1,'24hrs','{\"option-message\":\"1\"}'),
				(4,'installed','','test installed code','wait for develper to test code','Test your code to make sure it is working as expected. Make further changes and request install if needed, or let administrator know the tool is working properly.',0,'','{\"option-message\":\"1\"}'),
				(5,'broken','','fix the code','wait for develper to fix code','There is a problem with your code that needs to be fixed before it can be installed.',0,'','{\"option-message\":\"1\"}'),
				(7,'working','','','','The tool is working and can now be published. If you need administrator to install an update, request install via this screen.',2,'','{\"option-message\":\"1\"}'),
				(8,'retired','','','','You tool is retired. ',2,'','{\"option-message\":\"1\"}');";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{

	}
}