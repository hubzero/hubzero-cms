<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing activity tables
 **/
class Migration20170901000000ComActivity extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__activity_digests'))
		{
			$query = "CREATE TABLE `#__activity_digests` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `scope` varchar(250) NOT NULL,
			  `scope_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `frequency` tinyint(3) unsigned NOT NULL DEFAULT '0',
			  `sent` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_frequency` (`frequency`),
			  KEY `idx_scope_scope_id` (`scope`,`scope_id`)
			) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__activity_logs'))
		{
			$query = "CREATE TABLE `#__activity_logs` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
			  `description` text,
			  `action` varchar(100) DEFAULT NULL,
			  `scope` varchar(250) NOT NULL DEFAULT '',
			  `scope_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `details` text,
			  `anonymous` tinyint(2) unsigned NOT NULL DEFAULT '0',
			  `parent` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_scope_scope_id` (`scope`,`scope_id`),
			  KEY `idx_action` (`action`),
			  KEY `idx_parent` (`parent`)
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
			  `created` datetime DEFAULT NULL,
			  `viewed` datetime DEFAULT NULL,
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  `starred` tinyint(2) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_log_id` (`log_id`),
			  KEY `idx_state` (`state`),
			  KEY `idx_starred` (`starred`),
			  KEY `idx_scope_scope_id` (`scope`,`scope_id`)
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
			  `created` datetime DEFAULT NULL,
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
		if ($this->db->tableExists('#__activity_digests'))
		{
			$query = "DROP TABLE IF EXISTS `#__activity_digests`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

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
