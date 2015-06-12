<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for setting up/updating time component
 **/
class Migration20140714201025ComTime extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__time_auth_tokens'))
		{
			$query = "DROP TABLE `#__time_auth_tokens`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__time_hub_contacts'))
		{
			$query = "CREATE TABLE `#__time_hub_contacts` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `name` varchar(255) NOT NULL DEFAULT '',
				  `phone` varchar(255) DEFAULT '000-000-0000',
				  `email` varchar(255) DEFAULT '',
				  `role` varchar(255) DEFAULT '',
				  `hub_id` int(11) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__time_hubs'))
		{
			$query = "CREATE TABLE `#__time_hubs` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `name` varchar(255) NOT NULL DEFAULT '',
				  `name_normalized` varchar(255) NOT NULL DEFAULT '',
				  `liaison` varchar(255) DEFAULT NULL,
				  `anniversary_date` date DEFAULT '0000-00-00',
				  `support_level` varchar(255) DEFAULT 'Standard Support',
				  `active` int(1) NOT NULL DEFAULT '1',
				  `notes` blob,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__time_records'))
		{
			$query = "CREATE TABLE `#__time_records` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `task_id` int(11) NOT NULL,
				  `user_id` int(11) NOT NULL,
				  `time` double NOT NULL,
				  `date` date NOT NULL,
				  `description` longtext,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		else if ($this->db->tableExists('#__time_records') && $this->db->tableHasField('#__time_records', 'billed'))
		{
			$query = "ALTER TABLE `#__time_records` DROP `billed`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__time_reports'))
		{
			$query = "DROP TABLE `#__time_reports`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__time_reports_records_assoc'))
		{
			$query = "DROP TABLE `#__time_reports_records_assoc`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__time_tasks'))
		{
			$query = "CREATE TABLE `#__time_tasks` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `name` varchar(255) NOT NULL DEFAULT '',
				  `hub_id` int(11) NOT NULL,
				  `start_date` date DEFAULT '0000-00-00',
				  `end_date` date DEFAULT '0000-00-00',
				  `active` int(1) NOT NULL DEFAULT '1',
				  `description` blob,
				  `priority` int(1) DEFAULT NULL,
				  `assignee` int(11) DEFAULT NULL,
				  `liaison` int(11) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__time_users'))
		{
			$query = "CREATE TABLE `#__time_users` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `user_id` int(11) NOT NULL,
				  `manager_id` int(11) NOT NULL,
				  `liaison` int(1) DEFAULT '0',
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->addComponentEntry('Time', 'com_time', 0);
		$this->deletePluginEntry('time');
	}
}