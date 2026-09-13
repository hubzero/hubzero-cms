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
 * Create the Hubzero\Moderation tables
 *
 * Tables only; nothing is seeded here. InnoDB throughout: moderating spends a
 * credit and moves a score in one transaction, and the log is written on
 * every act.
 *
 * The review columns and table are created now although nothing writes them
 * until moderation review ships. They cost nothing to carry and retrofitting
 * them onto a live log would be painful.
 **/
class Migration20260913180000Moderation extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__moderation_reasons'))
		{
			$query = "CREATE TABLE `#__moderation_reasons` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `item_type` varchar(100) NOT NULL DEFAULT '',
			  `alias` varchar(100) NOT NULL DEFAULT '',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `description` text,
			  `value` tinyint(4) NOT NULL DEFAULT '0',
			  `karma_rule` varchar(100) DEFAULT NULL,
			  `reviewable` tinyint(2) NOT NULL DEFAULT '1',
			  `listable` tinyint(2) NOT NULL DEFAULT '1',
			  `fair_fraction` float NOT NULL DEFAULT '0.5',
			  `state` tinyint(3) NOT NULL DEFAULT '1',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_type_alias` (`item_type`,`alias`),
			  KEY `idx_state` (`state`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__moderation_logs'))
		{
			$query = "CREATE TABLE `#__moderation_logs` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `item_type` varchar(100) NOT NULL DEFAULT '',
			  `item_id` int(11) NOT NULL DEFAULT '0',
			  `container_id` int(11) NOT NULL DEFAULT '0',
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `author_id` int(11) NOT NULL DEFAULT '0',
			  `reason_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `value` tinyint(4) NOT NULL DEFAULT '0',
			  `credits_spent` int(11) NOT NULL DEFAULT '0',
			  `score_before` float NOT NULL DEFAULT '0',
			  `active` tinyint(2) NOT NULL DEFAULT '1',
			  `ip` varchar(45) DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `review_count` int(11) NOT NULL DEFAULT '0',
			  `reviews_needed` int(11) NOT NULL DEFAULT '0',
			  `review_status` tinyint(3) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_one_per_moderator` (`item_type`,`item_id`,`user_id`),
			  KEY `idx_item` (`item_type`,`item_id`),
			  KEY `idx_container` (`item_type`,`container_id`,`user_id`),
			  KEY `idx_user_created` (`user_id`,`created`),
			  KEY `idx_author` (`author_id`),
			  KEY `idx_review` (`review_status`,`active`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__moderation_reviews'))
		{
			$query = "CREATE TABLE `#__moderation_reviews` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `log_id` bigint(20) unsigned NOT NULL DEFAULT '0',
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `value` tinyint(4) NOT NULL DEFAULT '0',
			  `active` tinyint(2) NOT NULL DEFAULT '1',
			  `created` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_one_per_reviewer` (`log_id`,`user_id`),
			  KEY `idx_user_id` (`user_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__moderation_wallets'))
		{
			$query = "CREATE TABLE `#__moderation_wallets` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `item_type` varchar(100) NOT NULL DEFAULT '',
			  `credits` int(11) NOT NULL DEFAULT '0',
			  `credits_expire` datetime DEFAULT NULL,
			  `last_granted` datetime DEFAULT NULL,
			  `willing` tinyint(2) NOT NULL DEFAULT '1',
			  `total_moderations` int(11) NOT NULL DEFAULT '0',
			  `expired` int(11) NOT NULL DEFAULT '0',
			  `up_moderations` int(11) NOT NULL DEFAULT '0',
			  `down_moderations` int(11) NOT NULL DEFAULT '0',
			  `last_review` datetime DEFAULT NULL,
			  `reviews_fair` int(11) NOT NULL DEFAULT '0',
			  `reviews_unfair` int(11) NOT NULL DEFAULT '0',
			  `up_fair` int(11) NOT NULL DEFAULT '0',
			  `up_unfair` int(11) NOT NULL DEFAULT '0',
			  `down_fair` int(11) NOT NULL DEFAULT '0',
			  `down_unfair` int(11) NOT NULL DEFAULT '0',
			  `reviews_voted_with` int(11) NOT NULL DEFAULT '0',
			  `reviews_voted_alone` int(11) NOT NULL DEFAULT '0',
			  `tokens` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_user_type` (`user_id`,`item_type`),
			  KEY `idx_type_credits` (`item_type`,`credits`),
			  KEY `idx_expire` (`credits_expire`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__moderation_grants'))
		{
			$query = "CREATE TABLE `#__moderation_grants` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `item_type` varchar(100) NOT NULL DEFAULT '',
			  `grantor` varchar(100) NOT NULL DEFAULT '',
			  `eligible` int(11) NOT NULL DEFAULT '0',
			  `granted` int(11) NOT NULL DEFAULT '0',
			  `credits_issued` int(11) NOT NULL DEFAULT '0',
			  `credits_expired` int(11) NOT NULL DEFAULT '0',
			  `credits_spent` int(11) NOT NULL DEFAULT '0',
			  `note` varchar(255) DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_type_created` (`item_type`,`created`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__moderation_activities'))
		{
			$query = "CREATE TABLE `#__moderation_activities` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `item_type` varchar(100) NOT NULL DEFAULT '',
			  `day` date NOT NULL,
			  `count` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_user_type_day` (`user_id`,`item_type`,`day`),
			  KEY `idx_day` (`day`)
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
		$tables = array('activities', 'grants', 'wallets', 'reviews', 'logs', 'reasons');

		foreach ($tables as $table)
		{
			if ($this->db->tableExists('#__moderation_' . $table))
			{
				$this->db->setQuery("DROP TABLE IF EXISTS `#__moderation_" . $table . "`;");
				$this->db->query();
			}
		}
	}
}
