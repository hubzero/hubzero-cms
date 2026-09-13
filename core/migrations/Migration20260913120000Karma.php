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
 * Migration script for creating the Hubzero\Karma tables
 *
 * Tables only. The seed lives in the migration that follows this one, so a
 * failed seed never leaves half-built schema behind.
 *
 * InnoDB throughout rather than the MyISAM of the older migrations: the award
 * path writes a ledger row and a balance in one transaction, and a
 * write-heavy ledger under table-level locking would be a problem in itself.
 **/
class Migration20260913120000Karma extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__karma_scales'))
		{
			$query = "CREATE TABLE `#__karma_scales` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `alias` varchar(100) NOT NULL DEFAULT '',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `description` text,
			  `floor` int(11) NOT NULL DEFAULT '-25',
			  `ceiling` int(11) NOT NULL DEFAULT '50',
			  `initial` int(11) NOT NULL DEFAULT '0',
			  `decay_per_day` float NOT NULL DEFAULT '0',
			  `decay_after_days` int(11) NOT NULL DEFAULT '0',
			  `decay_toward` int(11) NOT NULL DEFAULT '0',
			  `visibility_self` varchar(20) NOT NULL DEFAULT 'adjective',
			  `visibility_public` varchar(20) NOT NULL DEFAULT 'hidden',
			  `adjectives` text,
			  `state` tinyint(3) NOT NULL DEFAULT '1',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `params` text,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_alias` (`alias`),
			  KEY `idx_state` (`state`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__karma_rules'))
		{
			$query = "CREATE TABLE `#__karma_rules` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `scale_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `alias` varchar(100) NOT NULL DEFAULT '',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `description` text,
			  `delta` float NOT NULL DEFAULT '0',
			  `daily_cap` int(11) NOT NULL DEFAULT '0',
			  `per_source_cap` int(11) NOT NULL DEFAULT '0',
			  `requires_karma` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(3) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_scale_alias` (`scale_id`,`alias`),
			  KEY `idx_alias` (`alias`),
			  KEY `idx_state` (`state`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__karma_gates'))
		{
			$query = "CREATE TABLE `#__karma_gates` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `scale_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `alias` varchar(100) NOT NULL DEFAULT '',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `description` text,
			  `bands` text,
			  `default_value` varchar(100) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_alias` (`alias`),
			  KEY `idx_scale_id` (`scale_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__karma_ledgers'))
		{
			$query = "CREATE TABLE `#__karma_ledgers` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `scale_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `subject_id` int(11) NOT NULL DEFAULT '0',
			  `actor_id` int(11) NOT NULL DEFAULT '0',
			  `delta` float NOT NULL DEFAULT '0',
			  `applied` float NOT NULL DEFAULT '0',
			  `rule` varchar(100) NOT NULL DEFAULT '',
			  `source_type` varchar(100) NOT NULL DEFAULT '',
			  `source_id` int(11) NOT NULL DEFAULT '0',
			  `expires` datetime DEFAULT NULL,
			  `state` tinyint(3) NOT NULL DEFAULT '1',
			  `created` datetime DEFAULT NULL,
			  `params` text,
			  PRIMARY KEY (`id`),
			  KEY `idx_subject_scale_state` (`subject_id`,`scale_id`,`state`),
			  KEY `idx_source` (`source_type`,`source_id`),
			  KEY `idx_scale_created` (`scale_id`,`created`),
			  KEY `idx_expires` (`expires`),
			  KEY `idx_rule` (`rule`),
			  KEY `idx_actor_id` (`actor_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__karma_balances'))
		{
			$query = "CREATE TABLE `#__karma_balances` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `scale_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `karma` float NOT NULL DEFAULT '0',
			  `raw` float NOT NULL DEFAULT '0',
			  `positive_count` int(11) NOT NULL DEFAULT '0',
			  `negative_count` int(11) NOT NULL DEFAULT '0',
			  `last_event` datetime DEFAULT NULL,
			  `last_recalculated` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_user_scale` (`user_id`,`scale_id`),
			  KEY `idx_scale_karma` (`scale_id`,`karma`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		foreach (array('balances', 'ledgers', 'gates', 'rules', 'scales') as $table)
		{
			if ($this->db->tableExists('#__karma_' . $table))
			{
				$this->db->setQuery("DROP TABLE IF EXISTS `#__karma_" . $table . "`;");
				$this->db->query();
			}
		}
	}
}
