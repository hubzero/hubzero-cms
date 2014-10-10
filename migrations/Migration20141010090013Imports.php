<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding content import tables
 **/
class Migration20141010090013Imports extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__imports'))
		{
			$query = "CREATE TABLE `#__imports` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `type` varchar(150) NOT NULL,
				  `name` varchar(150) DEFAULT NULL,
				  `notes` text,
				  `file` varchar(255) DEFAULT '',
				  `count` int(11) unsigned NOT NULL DEFAULT '0',
				  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
				  `created_at` datetime DEFAULT NULL,
				  `state` int(11) unsigned NOT NULL DEFAULT '1',
				  `mode` varchar(10) DEFAULT 'UPDATE',
				  `params` text,
				  `hooks` text,
				  `fields` text NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__import_hooks'))
		{
			$query = "CREATE TABLE `#__import_hooks` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `event` varchar(25) DEFAULT NULL,
				  `type` varchar(150) NOT NULL,
				  `name` varchar(255) DEFAULT NULL,
				  `notes` text,
				  `file` varchar(100) DEFAULT NULL,
				  `state` int(11) NOT NULL DEFAULT '1',
				  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__import_runs'))
		{
			$query = "CREATE TABLE `#__import_runs` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `import_id` int(11) DEFAULT NULL,
				  `processed` int(11) DEFAULT NULL,
				  `count` int(11) DEFAULT NULL,
				  `ran_by` int(11) DEFAULT NULL,
				  `ran_at` datetime DEFAULT NULL,
				  `dry_run` int(11) DEFAULT '0',
				  PRIMARY KEY (`id`)
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
		if ($this->db->tableExists('#__imports'))
		{
			$query = "DROP TABLE `#__imports`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__import_hooks'))
		{
			$query = "DROP TABLE `#__import_hooks`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__import_runs'))
		{
			$query = "DROP TABLE `#__import_runs`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}