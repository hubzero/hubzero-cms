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
 * Create the submissions queue
 *
 * The ranking columns arrive with the table rather than after it. Adding four
 * columns to a populated queue and rewriting the triage screen around them
 * later costs a migration and a rewrite; carrying them now costs four columns
 * that start at zero.
 *
 * `popularity` is the public ranking. `editor_popularity` is the same sum
 * restricted to people who can publish, kept apart so editors can signal to
 * each other — "I would run this" — without moving what readers see.
 * `attention_needed` is the other half of that: a flag meaning a human has to
 * decide, whoever or whatever set it.
 **/
class Migration20260913240000ComStorySubmissions extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__story_submissions'))
		{
			$query = "CREATE TABLE `#__story_submissions` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `subject` varchar(255) NOT NULL DEFAULT '',
			  `body` text,
			  `url` varchar(2048) DEFAULT NULL,
			  `topic_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `section_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `email` varchar(255) NOT NULL DEFAULT '',
			  `ip` varchar(45) NOT NULL DEFAULT '',
			  `state` varchar(20) NOT NULL DEFAULT 'pending',
			  `note` varchar(255) NOT NULL DEFAULT '',
			  `popularity` float NOT NULL DEFAULT '0',
			  `editor_popularity` float NOT NULL DEFAULT '0',
			  `attention_needed` tinyint(2) NOT NULL DEFAULT '0',
			  `last_scored` datetime DEFAULT NULL,
			  `decided_by` int(11) NOT NULL DEFAULT '0',
			  `decided_at` datetime DEFAULT NULL,
			  `story_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `scope` varchar(100) NOT NULL DEFAULT 'site',
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `params` text,
			  PRIMARY KEY (`id`),
			  KEY `idx_state` (`state`),
			  KEY `idx_popularity` (`popularity`),
			  KEY `idx_editor_popularity` (`editor_popularity`),
			  KEY `idx_attention` (`attention_needed`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_scored` (`last_scored`)
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
		if ($this->db->tableExists('#__story_submissions'))
		{
			$this->db->setQuery("DROP TABLE `#__story_submissions`;");
			$this->db->query();
		}
	}
}
