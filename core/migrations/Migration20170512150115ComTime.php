<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing com_time and associated extensions and tables
 **/
class Migration20170512150115ComTime extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteComponentEntry('time');

		$this->deletePluginEntry('support', 'time');

		$this->deletePluginEntry('time', 'csv');
		$this->deletePluginEntry('time', 'summary');
		$this->deletePluginEntry('time', 'weeklybar');

		$tables = array(
			'#__time_auth_token',
			'#__time_funding_allocations',
			'#__time_funding_sources',
			'#__time_hub_allotments',
			'#__time_hub_contacts',
			'#__time_hubs',
			'#__time_liaisons',
			'#__time_proxies',
			'#__time_records',
			'#__time_tasks'
		);

		foreach ($tables as $table)
		{
			if ($this->db->tableExists($table))
			{
				$query = "DROP TABLE IF EXISTS `" . $table . "`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addComponentEntry('time');

		$this->addPluginEntry('support', 'time');

		$this->addPluginEntry('time', 'csv');
		$this->addPluginEntry('time', 'summary');
		$this->addPluginEntry('time', 'weeklybar');

		if (!$this->db->tableExists('#__time_auth_token'))
		{
			$query = "CREATE TABLE `#__time_auth_token` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL,
			  `token` varchar(255) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__time_funding_allocations'))
		{
			$query = "CREATE TABLE `#__time_funding_allocations` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `source_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `percentage` int(3) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_user_id` (`user_id`),
			  KEY `idx_source_id` (`source_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__time_funding_sources'))
		{
			$query = "CREATE TABLE `#__time_funding_sources` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `type` varchar(150) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__time_hub_allotments'))
		{
			$query = "CREATE TABLE `#__time_hub_allotments` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `hub_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `start_date` date NOT NULL DEFAULT '0000-00-00',
			  `end_date` date NOT NULL DEFAULT '0000-00-00',
			  `hours` double NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_hub_id` (`hub_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
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
			  PRIMARY KEY (`id`),
			  KEY `idx_hub_id` (`hub_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
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
			  `asset_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_active` (`active`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__time_liaisons'))
		{
			$query = "CREATE TABLE `#__time_liaisons` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__time_proxies'))
		{
			$query = "CREATE TABLE `#__time_proxies` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL,
			  `proxy_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
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
			  `date` datetime NOT NULL,
			  `end` datetime NOT NULL,
			  `description` longtext,
			  PRIMARY KEY (`id`),
			  KEY `idx_task_id` (`task_id`),
			  KEY `idx_user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
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
			  `priority` int(1) NOT NULL DEFAULT '0',
			  `assignee_id` int(11) NOT NULL DEFAULT '0',
			  `liaison_id` int(11) NOT NULL DEFAULT '0',
			  `billable` tinyint(2) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_hub_id` (`hub_id`),
			  KEY `idx_liaison_id` (`liaison_id`),
			  KEY `idx_assignee_id` (`assignee_id`),
			  KEY `idx_priority` (`priority`),
			  KEY `idx_billable` (`billable`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
