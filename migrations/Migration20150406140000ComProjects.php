<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding project repo table
 **/
class Migration20150406140000ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__project_repos'))
		{
			$query = "CREATE TABLE `jos_project_repos` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `project_id` int(11) NOT NULL,
			  `name` varchar(64) NOT NULL DEFAULT '',
			  `about` varchar(255),
			  `path` varchar(255) NOT NULL DEFAULT '',
			  `status` int(11) NOT NULL DEFAULT '0',
			  `created` datetime NOT NULL,
			  `created_by` int(11) NOT NULL,
			  `remote` tinyint(1) NOT NULL DEFAULT '0',
			  `engine` varchar(100) NOT NULL DEFAULT 'git',
			  `params` text,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `repo` (`project_id`,`name`, `path`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}