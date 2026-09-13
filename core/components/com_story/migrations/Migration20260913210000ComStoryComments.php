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
 * Create the discussion and comment tables
 *
 * `parent` is the authoritative structure. `path` and `depth` are derived
 * from it and can be rebuilt from it at any time, which is the whole reason
 * they are safe to store: ORDER BY path gives tree order in one index scan
 * and LIKE 'prefix.%' selects a subtree, but neither is ground truth, so a
 * repair pass over `parent` can always put them right.
 *
 * The score columns arrive here unused. Phase 7 gives every comment a score
 * of 1 and nothing moves it; phase 8 fills them in. Carrying them from the
 * start keeps a populated table off the ALTER path later.
 **/
class Migration20260913210000ComStoryComments extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__story_discussions'))
		{
			$query = "CREATE TABLE `#__story_discussions` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `story_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `type` varchar(20) NOT NULL DEFAULT 'open',
			  `comment_status` varchar(20) NOT NULL DEFAULT 'enabled',
			  `comment_count` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `last_activity` datetime DEFAULT NULL,
			  `access` tinyint(2) NOT NULL DEFAULT '1',
			  `asset_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_story` (`story_id`),
			  KEY `idx_activity` (`last_activity`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__story_comments'))
		{
			$query = "CREATE TABLE `#__story_comments` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `discussion_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `parent` int(11) unsigned NOT NULL DEFAULT '0',
			  `path` varchar(255) NOT NULL DEFAULT '',
			  `depth` int(11) NOT NULL DEFAULT '0',
			  `subject` varchar(255) NOT NULL DEFAULT '',
			  `comment` text,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `anonymous` tinyint(2) NOT NULL DEFAULT '0',
			  `ip` varchar(45) NOT NULL DEFAULT '',
			  `state` tinyint(3) NOT NULL DEFAULT '1',
			  `closed` tinyint(2) NOT NULL DEFAULT '0',
			  `score` float NOT NULL DEFAULT '1',
			  `score_original` float NOT NULL DEFAULT '1',
			  `score_max` float NOT NULL DEFAULT '1',
			  `tweak` float NOT NULL DEFAULT '0',
			  `tweak_original` float NOT NULL DEFAULT '0',
			  `reason_id` int(11) NOT NULL DEFAULT '0',
			  `last_moderator_id` int(11) NOT NULL DEFAULT '0',
			  `karma_bonus` tinyint(2) NOT NULL DEFAULT '0',
			  `length` int(11) NOT NULL DEFAULT '0',
			  `signature` varchar(255) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`),
			  KEY `idx_discussion_path` (`discussion_id`,`path`),
			  KEY `idx_parent` (`parent`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_state` (`state`),
			  KEY `idx_score` (`score`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__story_preferences'))
		{
			$query = "CREATE TABLE `#__story_preferences` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `mode` varchar(20) NOT NULL DEFAULT 'threaded',
			  `sort` varchar(20) NOT NULL DEFAULT 'oldest',
			  `threshold` float NOT NULL DEFAULT '0',
			  `highlight_threshold` float NOT NULL DEFAULT '4',
			  `comment_limit` int(11) NOT NULL DEFAULT '100',
			  `comment_spill` int(11) NOT NULL DEFAULT '50',
			  `max_comment_size` int(11) NOT NULL DEFAULT '4096',
			  `hide_scores` tinyint(2) NOT NULL DEFAULT '0',
			  `reparent` tinyint(2) NOT NULL DEFAULT '1',
			  `hide_signatures` tinyint(2) NOT NULL DEFAULT '0',
			  `default_score` float NOT NULL DEFAULT '1',
			  `willing_to_moderate` tinyint(2) NOT NULL DEFAULT '1',
			  `bonus_long` float NOT NULL DEFAULT '0',
			  `bonus_short` float NOT NULL DEFAULT '0',
			  `length_long` int(11) NOT NULL DEFAULT '2000',
			  `length_short` int(11) NOT NULL DEFAULT '200',
			  `bonus_anonymous` float NOT NULL DEFAULT '0',
			  `bonus_new_user` float NOT NULL DEFAULT '0',
			  `new_user_percent` int(11) NOT NULL DEFAULT '10',
			  `bonus_karma` float NOT NULL DEFAULT '0',
			  `reason_adjustments` text,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_user` (`user_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__story_reads'))
		{
			$query = "CREATE TABLE `#__story_reads` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `discussion_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `last_comment_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `last_read` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_user_discussion` (`user_id`,`discussion_id`)
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
		foreach (array('#__story_reads', '#__story_preferences', '#__story_comments', '#__story_discussions') as $table)
		{
			if ($this->db->tableExists($table))
			{
				$this->db->setQuery("DROP TABLE `" . $table . "`;");
				$this->db->query();
			}
		}
	}
}
