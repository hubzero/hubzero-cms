<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for resource import tables
 **/
class Migration20140324161600ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = '';

		// imports table
		if (!$this->db->tableExists('#__resource_imports'))
		{
			$query .= "CREATE TABLE IF NOT EXISTS `#__resource_imports` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `name` varchar(150) DEFAULT NULL,
						  `notes` text,
						  `file` varchar(255) DEFAULT '',
						  `count` int(11) DEFAULT NULL,
						  `created_by` int(11) DEFAULT NULL,
						  `created_at` datetime DEFAULT NULL,
						  `state` int(11) DEFAULT '1',
						  `mode` varchar(10) DEFAULT 'UPDATE',
						  `params` text,
						  `hooks` text,
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
		}

		// runs table
		if (!$this->db->tableExists('#__resource_import_runs'))
		{
			$query .= "CREATE TABLE IF NOT EXISTS `#__resource_import_runs` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `import_id` int(11) DEFAULT NULL,
						  `processed` int(11) DEFAULT NULL,
						  `count` int(11) DEFAULT NULL,
						  `ran_by` int(11) DEFAULT NULL,
						  `ran_at` datetime DEFAULT NULL,
						  `dry_run` int(11) DEFAULT '0',
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
		}

		// hooks table
		if (!$this->db->tableExists('#__resource_import_hooks'))
		{
			$query .= "CREATE TABLE IF NOT EXISTS `#__resource_import_hooks` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `type` varchar(25) DEFAULT NULL,
						  `name` varchar(255) DEFAULT NULL,
						  `notes` text,
						  `file` varchar(100) DEFAULT NULL,
						  `state` int(11) DEFAULT '1',
						  `created` datetime DEFAULT NULL,
						  `created_by` int(11) DEFAULT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=latin1;\n";
		}

		if ($query != '')
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
		$query = '';

		// imports table
		if ($this->db->tableExists('#__resource_imports'))
		{
			$query .= "DROP TABLE IF EXISTS `#__resource_imports`;\n";
		}

		// runs table
		if ($this->db->tableExists('#__resource_import_runs'))
		{
			$query .= "DROP TABLE IF EXISTS `#__resource_import_runs`;\n";
		}

		// hooks table
		if ($this->db->tableExists('#__resource_import_hooks'))
		{
			$query .= "DROP TABLE IF EXISTS `#__resource_import_hooks`;\n";
		}

		if ($query != '')
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}