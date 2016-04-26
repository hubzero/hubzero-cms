<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding activity log tables
 **/
class Migration20160415105038CoreActivity extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__activity_logs'))
		{
			$query = "CREATE TABLE `#__activity_logs` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `created` datetime DEFAULT '0000-00-00 00:00:00',
				  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
				  `description` varchar(250) DEFAULT NULL,
				  `action` varchar(100) DEFAULT NULL,
				  `scope` varchar(250) NOT NULL DEFAULT '',
				  `scope_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `details` text,
				  PRIMARY KEY (`id`),
				  KEY `idx_created_by` (`created_by`),
				  KEY `idx_scope_scope_id` (`scope`,`scope_id`),
				  KEY `idx_action` (`action`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__activity_recipients'))
		{
			$query = "CREATE TABLE `#__activity_recipients` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `log_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `scope` varchar(250) NOT NULL,
				  `scope_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `viewed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `state` tinyint(2) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  KEY `idx_log_id` (`log_id`),
				  KEY `idx_user_id` (`scope_id`),
				  KEY `idx_state` (`state`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__activity_subscriptions'))
		{
			$query = "CREATE TABLE `#__activity_subscriptions` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `scope` varchar(250) NOT NULL DEFAULT '',
				  `scope_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  PRIMARY KEY (`id`),
				  KEY `idx_user_id` (`user_id`),
				  KEY `idx_scope_scope_id` (`scope`,`scope_id`)
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
		if ($this->db->tableExists('#__activity_logs'))
		{
			$query = "DROP TABLE IF EXISTS `#__activity_logs`;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__activity_recipients'))
		{
			$query = "DROP TABLE IF EXISTS `#__activity_recipients`;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__activity_subscriptions'))
		{
			$query = "DROP TABLE IF EXISTS `#__activity_subscriptions`;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
