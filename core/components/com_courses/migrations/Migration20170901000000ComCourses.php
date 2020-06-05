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
 * Migration script for installing courses tables
 **/
class Migration20170901000000ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__courses'))
		{
			$query = "CREATE TABLE `#__courses` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `alias` varchar(255) NOT NULL DEFAULT '',
			  `group_id` int(11) NOT NULL DEFAULT '0',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `state` tinyint(3) NOT NULL DEFAULT '0',
			  `type` tinyint(3) NOT NULL DEFAULT '0',
			  `access` tinyint(3) NOT NULL DEFAULT '0',
			  `blurb` text NOT NULL,
			  `description` text NOT NULL,
			  `logo` varchar(255) NOT NULL DEFAULT '',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `params` text NOT NULL,
			  `length` varchar(255) DEFAULT NULL,
			  `effort` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  FULLTEXT KEY `ftidx_alias_title_blurb` (`alias`,`title`,`blurb`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_offerings'))
		{
			$query = "CREATE TABLE `#__courses_offerings` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `course_id` int(11) NOT NULL DEFAULT '0',
			  `alias` varchar(255) NOT NULL DEFAULT '',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `term` varchar(255) NOT NULL DEFAULT '',
			  `state` tinyint(2) NOT NULL DEFAULT '1',
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `params` text NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_course_id` (`course_id`),
			  KEY `idx_state` (`state`),
			  KEY `idx_created_by` (`created_by`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_offering_sections'))
		{
			$query = "CREATE TABLE `#__courses_offering_sections` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `offering_id` int(11) NOT NULL DEFAULT '0',
			  `is_default` tinyint(2) NOT NULL DEFAULT '0',
			  `alias` varchar(255) NOT NULL DEFAULT '',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `state` tinyint(2) NOT NULL DEFAULT '1',
			  `start_date` datetime DEFAULT NULL,
			  `end_date` datetime DEFAULT NULL,
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `enrollment` tinyint(2) NOT NULL DEFAULT '0',
			  `grade_policy_id` int(11) NOT NULL DEFAULT '1',
			  `params` text NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_offering_id` (`offering_id`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_state` (`state`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_offering_section_dates'))
		{
			$query = "CREATE TABLE `#__courses_offering_section_dates` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `section_id` int(11) NOT NULL DEFAULT '0',
			  `scope` varchar(150) NOT NULL DEFAULT '',
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_section_id` (`section_id`),
			  KEY `idx_scope_id` (`scope_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_offering_section_codes'))
		{
			$query = "CREATE TABLE `#__courses_offering_section_codes` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `section_id` int(11) NOT NULL DEFAULT '0',
			  `code` varchar(10) NOT NULL DEFAULT '',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `expires` datetime DEFAULT NULL,
			  `redeemed` datetime DEFAULT NULL,
			  `redeemed_by` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_offering_section_badges'))
		{
			$query = "CREATE TABLE `#__courses_offering_section_badges` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `section_id` int(11) NOT NULL,
			  `published` int(1) NOT NULL DEFAULT '0',
			  `provider_name` varchar(255) NOT NULL DEFAULT 'passport',
			  `provider_badge_id` int(11) NOT NULL,
			  `img_url` varchar(255) NOT NULL DEFAULT '',
			  `criteria_id` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_offering_section_badge_criteria'))
		{
			$query = "CREATE TABLE `#__courses_offering_section_badge_criteria` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `text` text NOT NULL,
			  `section_badge_id` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_units'))
		{
			$query = "CREATE TABLE `#__courses_units` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `offering_id` int(11) NOT NULL DEFAULT '0',
			  `alias` varchar(250) NOT NULL,
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `description` longtext NOT NULL,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_offering_id` (`offering_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_announcements'))
		{
			$query = "CREATE TABLE `#__courses_announcements` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `offering_id` int(11) NOT NULL DEFAULT '0',
			  `content` text,
			  `priority` tinyint(2) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `section_id` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  `sticky` tinyint(2) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_offering_id` (`offering_id`),
			  KEY `idx_section_id` (`section_id`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_state` (`state`),
			  KEY `idx_priority` (`priority`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_assets'))
		{
			$query = "CREATE TABLE `#__courses_assets` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `content` mediumtext,
			  `type` varchar(255) NOT NULL DEFAULT '',
			  `subtype` varchar(255) NOT NULL DEFAULT 'file',
			  `url` varchar(255) NOT NULL DEFAULT '',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '1',
			  `course_id` int(11) NOT NULL DEFAULT '0',
			  `graded` tinyint(2) DEFAULT NULL,
			  `grade_weight` varchar(255) NOT NULL DEFAULT '',
			  `path` varchar(255) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`),
			  KEY `idx_course_id` (`course_id`),
			  KEY `idx_created_by` (`created_by`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_asset_views'))
		{
			$query = "CREATE TABLE `#__courses_asset_views` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `asset_id` int(11) NOT NULL,
			  `course_id` int(11) DEFAULT NULL,
			  `viewed` datetime NOT NULL,
			  `viewed_by` int(11) NOT NULL,
			  `ip` varchar(15) DEFAULT NULL,
			  `url` varchar(255) DEFAULT NULL,
			  `referrer` varchar(255) DEFAULT NULL,
			  `user_agent_string` varchar(255) DEFAULT NULL,
			  `session_id` varchar(200) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_asset_unity'))
		{
			$query = "CREATE TABLE `#__courses_asset_unity` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `member_id` int(11) NOT NULL,
			  `asset_id` int(11) NOT NULL,
			  `created` datetime NOT NULL,
			  `passed` tinyint(1) NOT NULL,
			  `details` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_asset_groups'))
		{
			$query = "CREATE TABLE `#__courses_asset_groups` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `unit_id` int(11) NOT NULL DEFAULT '0',
			  `alias` varchar(250) NOT NULL,
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `description` text NOT NULL,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `parent` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  `params` text NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_unit_id` (`unit_id`),
			  KEY `idx_created_by` (`created_by`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_asset_group_types'))
		{
			$query = "CREATE TABLE `#__courses_asset_group_types` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `alias` varchar(200) NOT NULL DEFAULT '',
			  `type` varchar(255) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_certificates'))
		{
			$query = "CREATE TABLE `#__courses_certificates` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `properties` text,
			  `course_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_log'))
		{
			$query = "CREATE TABLE `#__courses_log` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `scope` varchar(100) NOT NULL DEFAULT '',
			  `timestamp` datetime DEFAULT NULL,
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `action` varchar(50) NOT NULL DEFAULT '',
			  `comments` text NOT NULL,
			  `actor_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_grade_book'))
		{
			$query = "CREATE TABLE `#__courses_grade_book` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `member_id` int(11) NOT NULL,
			  `score` decimal(5,2) DEFAULT NULL,
			  `scope` varchar(255) NOT NULL DEFAULT 'asset',
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `override` decimal(5,2) DEFAULT NULL,
			  `score_recorded` datetime DEFAULT NULL,
			  `override_recorded` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_user_id_scope_scope_id` (`member_id`,`scope`,`scope_id`),
			  UNIQUE KEY `alternate_key` (`member_id`,`scope`,`scope_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_grade_policies'))
		{
			$query = "CREATE TABLE `#__courses_grade_policies` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `description` mediumtext,
			  `threshold` decimal(3,2) DEFAULT NULL,
			  `exam_weight` decimal(3,2) DEFAULT NULL,
			  `quiz_weight` decimal(3,2) DEFAULT NULL,
			  `homework_weight` decimal(3,2) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_members'))
		{
			$query = "CREATE TABLE `#__courses_members` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `course_id` int(11) NOT NULL DEFAULT '0',
			  `offering_id` int(11) NOT NULL DEFAULT '0',
			  `section_id` int(11) NOT NULL DEFAULT '0',
			  `role_id` int(11) NOT NULL DEFAULT '0',
			  `permissions` mediumtext NOT NULL,
			  `enrolled` datetime DEFAULT NULL,
			  `student` tinyint(2) NOT NULL DEFAULT '0',
			  `first_visit` datetime DEFAULT NULL,
			  `token` varchar(23) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`),
			  KEY `idx_offering_id` (`offering_id`),
			  KEY `idx_user_id` (`user_id`),
			  KEY `idx_role_id` (`role_id`),
			  KEY `idx_section_id` (`section_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_member_badges'))
		{
			$query = "CREATE TABLE `#__courses_member_badges` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `member_id` int(11) NOT NULL,
			  `section_badge_id` int(11) NOT NULL,
			  `earned` int(1) DEFAULT NULL,
			  `earned_on` datetime DEFAULT NULL,
			  `action` varchar(255) DEFAULT NULL,
			  `action_on` datetime DEFAULT NULL,
			  `validation_token` varchar(20) DEFAULT NULL,
			  `criteria_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_member_id` (`member_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_roles'))
		{
			$query = "CREATE TABLE `#__courses_roles` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `offering_id` int(11) NOT NULL DEFAULT '0',
			  `alias` varchar(150) NOT NULL,
			  `title` varchar(150) NOT NULL DEFAULT '',
			  `permissions` mediumtext NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_offering_id` (`offering_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_pages'))
		{
			$query = "CREATE TABLE `#__courses_pages` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `course_id` int(11) NOT NULL DEFAULT '0',
			  `offering_id` varchar(100) NOT NULL DEFAULT '0',
			  `section_id` int(11) NOT NULL DEFAULT '0',
			  `url` varchar(100) NOT NULL DEFAULT '',
			  `title` varchar(100) NOT NULL DEFAULT '',
			  `content` text NOT NULL,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `active` int(11) NOT NULL DEFAULT '0',
			  `privacy` varchar(10) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`),
			  KEY `idx_offering_id` (`offering_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_page_hits'))
		{
			$query = "CREATE TABLE `#__courses_page_hits` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `offering_id` int(11) NOT NULL DEFAULT '0',
			  `page_id` int(11) NOT NULL DEFAULT '0',
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `datetime` datetime DEFAULT NULL,
			  `ip` varchar(15) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`),
			  KEY `idx_offering_id` (`offering_id`),
			  KEY `idx_page_id` (`page_id`),
			  KEY `idx_user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_prerequisites'))
		{
			$query = "CREATE TABLE `#__courses_prerequisites` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `section_id` int(11) NOT NULL DEFAULT '0',
			  `item_scope` varchar(255) NOT NULL DEFAULT 'asset',
			  `item_id` int(11) NOT NULL DEFAULT '0',
			  `requisite_scope` varchar(255) NOT NULL DEFAULT 'asset',
			  `requisite_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_progress_factors'))
		{
			$query = "CREATE TABLE `#__courses_progress_factors` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `section_id` int(11) NOT NULL,
			  `asset_id` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_forms'))
		{
			$query = "CREATE TABLE `#__courses_forms` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `title` text,
			  `active` tinyint(4) NOT NULL DEFAULT '1',
			  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `asset_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_form_responses'))
		{
			$query = "CREATE TABLE `#__courses_form_responses` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `respondent_id` int(11) NOT NULL,
			  `question_id` int(11) NOT NULL,
			  `answer_id` int(11) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_respondent_id` (`respondent_id`),
			  KEY `idx_question_id` (`question_id`),
			  KEY `idx_answer_id` (`answer_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_form_respondents'))
		{
			$query = "CREATE TABLE `#__courses_form_respondents` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `deployment_id` int(11) NOT NULL,
			  `member_id` int(11) NOT NULL,
			  `started` timestamp NULL DEFAULT NULL,
			  `finished` timestamp NULL DEFAULT NULL,
			  `attempt` int(11) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`id`),
			  KEY `idx_member_id` (`member_id`),
			  KEY `idx_deployment_id` (`deployment_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_form_respondent_progress'))
		{
			$query = "CREATE TABLE `#__courses_form_respondent_progress` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `respondent_id` int(11) NOT NULL,
			  `question_id` int(11) NOT NULL,
			  `answer_id` int(11) NOT NULL,
			  `submitted` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_respondent_id_question_id` (`respondent_id`,`question_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_form_questions'))
		{
			$query = "CREATE TABLE `#__courses_form_questions` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `page` int(11) NOT NULL,
			  `version` int(11) NOT NULL,
			  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `left_dist` int(11) NOT NULL,
			  `top_dist` int(11) NOT NULL,
			  `height` int(11) NOT NULL,
			  `width` int(11) NOT NULL,
			  `form_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_form_deployments'))
		{
			$query = "CREATE TABLE `#__courses_form_deployments` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `form_id` int(11) NOT NULL,
			  `start_time` timestamp NULL DEFAULT NULL,
			  `end_time` timestamp NULL DEFAULT NULL,
			  `results_open` varchar(50) DEFAULT NULL,
			  `time_limit` int(11) DEFAULT NULL,
			  `crumb` varchar(20) NOT NULL,
			  `results_closed` varchar(50) DEFAULT NULL,
			  `user_id` int(11) NOT NULL,
			  `allowed_attempts` int(11) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_crumb` (`crumb`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_form_answers'))
		{
			$query = "CREATE TABLE `#__courses_form_answers` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `correct` tinyint(4) NOT NULL,
			  `left_dist` int(11) NOT NULL,
			  `top_dist` int(11) NOT NULL,
			  `question_id` int(11) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_question_id` (`question_id`)
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
		if ($this->db->tableExists('#__courses'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_offerings'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_offerings`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_offering_sections'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_offering_sections`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_offering_section_dates'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_offering_section_dates`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_offering_section_codes'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_offering_section_codes`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_offering_section_badges'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_offering_section_badges`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_offering_section_badge_criterias'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_offering_section_badge_criteria`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_units'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_units`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_announcements'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_announcements`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_assets'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_assets`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_asset_views'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_asset_views`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_asset_unity'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_asset_unity`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_asset_groups'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_asset_groups`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_asset_group_types'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_asset_group_types`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_certificates'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_certificates`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_log'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_log`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_grade_book'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_grade_book`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_grade_policies'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_grade_policies`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_members'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_members`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_member_badges'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_member_badges`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_roles'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_roles`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_pages'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_pages`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_page_hits'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_page_hits`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_prerequisites'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_prerequisites`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_progress_factors'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_progress_factors`;";
			$this->db->setQuery($query);
			$this->db->query();
		}


		if ($this->db->tableExists('#__courses_forms'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_forms`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_form_responses'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_form_responses`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_form_respondents'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_form_respondents`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_form_respondent_progress'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_form_respondent_progress`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_form_questions'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_form_questions`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_form_deployments'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_form_deployments`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_form_answers'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_form_answers`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
