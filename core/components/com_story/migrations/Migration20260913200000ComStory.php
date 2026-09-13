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
 * Create the com_story content tables
 *
 * Stories and their text are separate: a front page lists thirty of them and
 * needs none of the bodies, and a body is the largest column in the schema.
 *
 * The scope columns are carried from the start although only 'site' is used.
 * They cost nothing now and are painful to add to a populated table later,
 * and a story feed per research group is the likeliest place this component
 * earns its keep.
 **/
class Migration20260913200000ComStory extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__story_sections'))
		{
			$query = "CREATE TABLE `#__story_sections` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `alias` varchar(255) NOT NULL DEFAULT '',
			  `description` text,
			  `scope` varchar(100) NOT NULL DEFAULT 'site',
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `access` tinyint(2) NOT NULL DEFAULT '1',
			  `state` tinyint(3) NOT NULL DEFAULT '1',
			  `asset_id` int(11) NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `params` text,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_scope_alias` (`scope`,`scope_id`,`alias`),
			  KEY `idx_state` (`state`),
			  KEY `idx_access` (`access`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__story_topics'))
		{
			$query = "CREATE TABLE `#__story_topics` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `alias` varchar(255) NOT NULL DEFAULT '',
			  `description` text,
			  `image` varchar(255) DEFAULT NULL,
			  `submittable` tinyint(2) NOT NULL DEFAULT '1',
			  `searchable` tinyint(2) NOT NULL DEFAULT '1',
			  `access` tinyint(2) NOT NULL DEFAULT '1',
			  `state` tinyint(3) NOT NULL DEFAULT '1',
			  `asset_id` int(11) NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_alias` (`alias`),
			  KEY `idx_parent` (`parent_id`),
			  KEY `idx_state` (`state`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__story_stories'))
		{
			$query = "CREATE TABLE `#__story_stories` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `section_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `topic_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `alias` varchar(255) NOT NULL DEFAULT '',
			  `kicker` varchar(255) DEFAULT NULL,
			  `state` tinyint(3) NOT NULL DEFAULT '0',
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `submitter_id` int(11) NOT NULL DEFAULT '0',
			  `submission_id` int(11) NOT NULL DEFAULT '0',
			  `discussion_id` int(11) NOT NULL DEFAULT '0',
			  `poll_id` int(11) NOT NULL DEFAULT '0',
			  `hits` int(11) NOT NULL DEFAULT '0',
			  `comment_count` int(11) NOT NULL DEFAULT '0',
			  `scope` varchar(100) NOT NULL DEFAULT 'site',
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `access` tinyint(2) NOT NULL DEFAULT '1',
			  `asset_id` int(11) NOT NULL DEFAULT '0',
			  `params` text,
			  PRIMARY KEY (`id`),
			  KEY `idx_published` (`state`,`publish_up`),
			  KEY `idx_section` (`section_id`,`state`,`publish_up`),
			  KEY `idx_topic` (`topic_id`,`state`,`publish_up`),
			  KEY `idx_alias` (`alias`),
			  KEY `idx_scoped` (`scope`,`scope_id`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_submitter` (`submitter_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__story_texts'))
		{
			$query = "CREATE TABLE `#__story_texts` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `story_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `intro` text,
			  `body` mediumtext,
			  `related` text,
			  `rendered` mediumtext,
			  `word_count` int(11) NOT NULL DEFAULT '0',
			  `body_length` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_story_id` (`story_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__story_storytopics'))
		{
			$query = "CREATE TABLE `#__story_storytopics` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `story_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `topic_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `weight` float NOT NULL DEFAULT '1',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_story_topic` (`story_id`,`topic_id`),
			  KEY `idx_topic_id` (`topic_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->addComponentEntry('Story');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('Story');

		foreach (array('storytopics', 'texts', 'stories', 'topics', 'sections') as $table)
		{
			if ($this->db->tableExists('#__story_' . $table))
			{
				$this->db->setQuery("DROP TABLE IF EXISTS `#__story_" . $table . "`;");
				$this->db->query();
			}
		}
	}
}
