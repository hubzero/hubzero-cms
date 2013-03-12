<?php

class ComCourses20130101 extends Migration{

	protected function up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `jos_courses` (
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
				`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created_by` int(11) NOT NULL DEFAULT '0',
				`params` text NOT NULL,
				PRIMARY KEY (`id`),
				FULLTEXT KEY `jos_xgroups_cn_description_public_desc_ftidx` (`alias`,`title`,`blurb`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_announcements` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`offering_id` int(11) NOT NULL DEFAULT '0',
				`content` text,
				`priority` tinyint(2) NOT NULL DEFAULT '0',
				`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created_by` int(11) NOT NULL DEFAULT '0',
				`section_id` int(11) NOT NULL DEFAULT '0',
				`state` tinyint(2) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `idx_offering_id` (`offering_id`),
				KEY `idx_section_id` (`section_id`),
				KEY `idx_created_by` (`created_by`),
				KEY `idx_state` (`state`),
				KEY `idx_priority` (`priority`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_asset_associations` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`asset_id` int(11) NOT NULL DEFAULT '0',
				`scope_id` int(11) NOT NULL DEFAULT '0',
				`scope` varchar(255) NOT NULL DEFAULT 'asset_group',
				`ordering` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `idx_asset_id` (`asset_id`),
				KEY `idx_scope_id` (`scope_id`),
				KEY `idx_scope` (`scope`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_asset_group_types` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`alias` varchar(200) NOT NULL DEFAULT '',
				`type` varchar(255) NOT NULL DEFAULT '',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_asset_groups` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`unit_id` int(11) NOT NULL DEFAULT '0',
				`alias` varchar(250) NOT NULL,
				`title` varchar(255) NOT NULL DEFAULT '',
				`description` varchar(255) NOT NULL DEFAULT '',
				`ordering` int(11) NOT NULL DEFAULT '0',
				`parent` int(11) NOT NULL DEFAULT '0',
				`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created_by` int(11) NOT NULL DEFAULT '0',
				`state` tinyint(2) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `idx_unit_id` (`unit_id`),
				KEY `idx_created_by` (`created_by`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_asset_views` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`asset_id` int(11) NOT NULL,
				`viewed` datetime NOT NULL,
				`viewed_by` int(11) NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_assets` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`title` varchar(255) NOT NULL DEFAULT '',
				`content` mediumtext,
				`type` varchar(255) NOT NULL DEFAULT '',
				`url` varchar(255) NOT NULL DEFAULT '',
				`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created_by` int(11) NOT NULL DEFAULT '0',
				`state` tinyint(2) NOT NULL DEFAULT '1',
				`course_id` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `idx_course_id` (`course_id`),
				KEY `idx_created_by` (`created_by`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_form_answers` (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`correct` tinyint(4) NOT NULL,
				`left_dist` int(11) NOT NULL,
				`top_dist` int(11) NOT NULL,
				`question_id` int(11) NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `id` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_form_deployments` (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`form_id` int(11) NOT NULL,
				`start_time` timestamp NULL DEFAULT NULL,
				`end_time` timestamp NULL DEFAULT NULL,
				`results_open` varchar(50) DEFAULT NULL,
				`time_limit` int(11) DEFAULT NULL,
				`crumb` varchar(20) NOT NULL,
				`results_closed` varchar(50) DEFAULT NULL,
				`user_id` int(11) NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `id` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_form_questions` (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`page` int(11) NOT NULL,
				`version` int(11) NOT NULL,
				`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`left_dist` int(11) NOT NULL,
				`top_dist` int(11) NOT NULL,
				`height` int(11) NOT NULL,
				`width` int(11) NOT NULL,
				`form_id` int(11) DEFAULT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `id` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_form_respondent_progress` (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`respondent_id` int(11) NOT NULL,
				`question_id` int(11) NOT NULL,
				`answer_id` int(11) NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `id` (`id`),
				UNIQUE KEY `jos_pdf_form_respondent_progress_respondent_id_question_id_uidx` (`respondent_id`,`question_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_form_respondents` (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`deployment_id` int(11) NOT NULL,
				`user_id` int(11) NOT NULL,
				`started` timestamp NULL DEFAULT NULL,
				`finished` timestamp NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `id` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_form_responses` (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`respondent_id` int(11) NOT NULL,
				`question_id` int(11) NOT NULL,
				`answer_id` int(11) NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `id` (`id`),
				KEY `jos_pdf_form_respones_respondent_id_idx` (`respondent_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_forms` (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`title` text,
				`active` tinyint(4) NOT NULL DEFAULT '1',
				`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				UNIQUE KEY `id` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_log` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`scope_id` int(11) NOT NULL DEFAULT '0',
				`scope` varchar(100) NOT NULL DEFAULT '',
				`timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`user_id` int(11) NOT NULL DEFAULT '0',
				`action` varchar(50) NOT NULL DEFAULT '',
				`comments` text NOT NULL,
				`actor_id` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_members` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(11) NOT NULL DEFAULT '0',
				`course_id` int(11) NOT NULL DEFAULT '0',
				`offering_id` int(11) NOT NULL DEFAULT '0',
				`section_id` int(11) NOT NULL DEFAULT '0',
				`role_id` int(11) NOT NULL DEFAULT '0',
				`permissions` mediumtext NOT NULL,
				`enrolled` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`student` tinyint(2) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `idx_offering_id` (`offering_id`),
				KEY `idx_user_id` (`user_id`),
				KEY `idx_role_id` (`role_id`),
				KEY `idx_section_id` (`section_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_offering_section_dates` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`section_id` int(11) NOT NULL DEFAULT '0',
				`scope` varchar(150) NOT NULL DEFAULT '',
				`scope_id` int(11) NOT NULL DEFAULT '0',
				`publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created_by` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_offering_sections` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`offering_id` int(11) NOT NULL DEFAULT '0',
				`alias` varchar(255) NOT NULL DEFAULT '',
				`title` varchar(255) NOT NULL DEFAULT '',
				`state` tinyint(2) NOT NULL DEFAULT '1',
				`start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created_by` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `idx_offering_id` (`offering_id`),
				KEY `idx_created_by` (`created_by`),
				KEY `idx_state` (`state`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_offerings` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`course_id` int(11) NOT NULL DEFAULT '0',
				`alias` varchar(255) NOT NULL DEFAULT '',
				`title` varchar(255) NOT NULL DEFAULT '',
				`term` varchar(255) NOT NULL DEFAULT '',
				`state` tinyint(2) NOT NULL DEFAULT '1',
				`publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created_by` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `idx_course_id` (`course_id`),
				KEY `idx_state` (`state`),
				KEY `idx_created_by` (`created_by`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_page_hits` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`offering_id` int(11) NOT NULL DEFAULT '0',
				`page_id` int(11) NOT NULL DEFAULT '0',
				`user_id` int(11) NOT NULL DEFAULT '0',
				`datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`ip` varchar(15) NOT NULL DEFAULT '',
				PRIMARY KEY (`id`),
				KEY `idx_offering_id` (`offering_id`),
				KEY `idx_page_id` (`page_id`),
				KEY `idx_user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_pages` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`offering_id` varchar(100) NOT NULL DEFAULT '0',
				`url` varchar(100) NOT NULL DEFAULT '',
				`title` varchar(100) NOT NULL DEFAULT '',
				`content` text NOT NULL,
				`porder` int(11) NOT NULL DEFAULT '0',
				`active` int(11) NOT NULL DEFAULT '0',
				`privacy` varchar(10) NOT NULL DEFAULT '',
				PRIMARY KEY (`id`),
				KEY `idx_offering_id` (`offering_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE `jos_courses_reviews` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`course_id` int(11) NOT NULL DEFAULT '0',
				`offering_id` int(11) NOT NULL DEFAULT '0',
				`rating` decimal(2,1) NOT NULL DEFAULT '0.0',
				`content` text NOT NULL,
				`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created_by` int(11) NOT NULL DEFAULT '0',
				`modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`modified_by` int(11) NOT NULL DEFAULT '0',
				`anonymous` tinyint(2) NOT NULL DEFAULT '0',
				`parent` int(11) NOT NULL DEFAULT '0',
				`access` tinyint(2) NOT NULL DEFAULT '0',
				`state` tinyint(2) NOT NULL DEFAULT '0',
				`positive` int(11) NOT NULL DEFAULT '0',
				`negative` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_roles` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`offering_id` int(11) NOT NULL DEFAULT '0',
				`alias` varchar(150) NOT NULL,
				`title` varchar(150) NOT NULL DEFAULT '',
				`permissions` mediumtext NOT NULL,
				PRIMARY KEY (`id`),
				KEY `idx_offering_id` (`offering_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_units` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`offering_id` int(11) NOT NULL DEFAULT '0',
				`alias` varchar(250) NOT NULL,
				`title` varchar(255) NOT NULL DEFAULT '',
				`description` longtext NOT NULL,
				`ordering` int(11) NOT NULL DEFAULT '0',
				`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created_by` int(11) NOT NULL DEFAULT '0',
				`state` tinyint(2) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `idx_offering_id` (`offering_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `jos_courses_offering_section_codes` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`section_id` int(11) NOT NULL DEFAULT '0',
				`code` varchar(10) NOT NULL DEFAULT '',
				`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created_by` int(11) NOT NULL DEFAULT '0',
				`expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`redeemed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`redeemed_by` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`myhub`@`localhost` SQL SECURITY DEFINER VIEW `jos_courses_form_latest_responses_view`
			AS SELECT
				 `fre`.`id` AS `id`,
				 `fre`.`respondent_id` AS `respondent_id`,
				 `fre`.`question_id` AS `question_id`,
				 `fre`.`answer_id` AS `answer_id`
			FROM `jos_courses_form_responses` `fre` where ((select count(0) from `jos_courses_form_responses` `frei` where ((`frei`.`respondent_id` = `fre`.`respondent_id`) and (`frei`.`id` > `fre`.`id`))) < (select count(distinct `frei`.`question_id`) from `jos_courses_form_responses` `frei` where (`frei`.`respondent_id` = `fre`.`respondent_id`)));

			INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Members - Courses','courses','members',0,16,1,0,0,0,'0000-00-00 00:00:00',''
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_plugins` WHERE `name` = 'Members - Courses');

			INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - Syllabus','syllabus','courses',0,1,0,0,0,0,'0000-00-00 00:00:00',''),
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_plugins` WHERE `name` = 'Courses - Syllabus');

			INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - Disucssions','forum','courses',0,2,1,0,0,0,'0000-00-00 00:00:00',''),
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_plugins` WHERE `name` = 'Courses - Disucssions');

			INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - My Progress','progress','courses',0,3,1,0,0,0,'0000-00-00 00:00:00',''),
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_plugins` WHERE `name` = 'Courses - My Progress');

			INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - Announcements','announcements','courses',0,4,1,0,0,0,'0000-00-00 00:00:00',''),
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_plugins` WHERE `name` = 'Courses - Announcements');

			INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - Dashboard','dashboard','courses',0,5,1,0,0,0,'0000-00-00 00:00:00',''),
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_plugins` WHERE `name` = 'Courses - Dashboard');

			INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - Course Overview','overview','courses',0,6,1,0,0,0,'0000-00-00 00:00:00',''),
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_plugins` WHERE `name` = 'Courses - Course Overview');

			INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - Course Reviews','reviews','courses',0,7,1,0,0,0,'0000-00-00 00:00:00',''),
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_plugins` WHERE `name` = 'Courses - Course Reviews');

			INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - Course Offerings','offerings','courses',0,8,1,0,0,0,'0000-00-00 00:00:00','');
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_plugins` WHERE `name` = 'Courses - Course Offerings');";

		$this->get('db')->exec($query);
	}

	protected function down()
	{
		$query = "CREATE TABLE IF NOT EXISTS `jos_courses`;
			DROP TABLE IF EXISTS `jos_courses_announcements`;
			DROP TABLE IF EXISTS `jos_courses_asset_associations`;
			DROP TABLE IF EXISTS `jos_courses_asset_group_types`;
			DROP TABLE IF EXISTS `jos_courses_asset_groups`;
			DROP TABLE IF EXISTS `jos_courses_assets`;
			DROP TABLE IF EXISTS `jos_courses_form_answers`;
			DROP TABLE IF EXISTS `jos_courses_form_deployments`;
			DROP TABLE IF EXISTS `jos_courses_form_questions`;
			DROP TABLE IF EXISTS `jos_courses_form_respondent_progress`;
			DROP TABLE IF EXISTS `jos_courses_form_respondents`;
			DROP TABLE IF EXISTS `jos_courses_form_responses`;
			DROP TABLE IF EXISTS `jos_courses_forms`;
			DROP TABLE IF EXISTS `jos_courses_log`;
			DROP TABLE IF EXISTS `jos_courses_members`;
			DROP TABLE IF EXISTS `jos_courses_offering_section_dates`;
			DROP TABLE IF EXISTS `jos_courses_offering_sections`;
			DROP TABLE IF EXISTS `jos_courses_offerings`;
			DROP TABLE IF EXISTS `jos_courses_page_hits`;
			DROP TABLE IF EXISTS `jos_courses_pages`;
			DROP TABLE IF EXISTS `jos_courses_roles`;
			DROP TABLE IF EXISTS `jos_courses_units`;
			DROP TABLE IF EXISTS `jos_courses_offering_section_codes`;
			DROP VIEW IF EXISTS `jos_courses_form_latest_responses_view`;

			DELETE FROM `jos_plugins` WHERE `name` = 'Members - Courses';
			DELETE FROM `jos_plugins` WHERE `name` = 'Courses - Syllabus';
			DELETE FROM `jos_plugins` WHERE `name` = 'Courses - Disucssions';
			DELETE FROM `jos_plugins` WHERE `name` = 'Courses - My Progress';
			DELETE FROM `jos_plugins` WHERE `name` = 'Courses - Announcements';
			DELETE FROM `jos_plugins` WHERE `name` = 'Courses - Dashboard';
			DELETE FROM `jos_plugins` WHERE `name` = 'Courses - Course Overview';
			DELETE FROM `jos_plugins` WHERE `name` = 'Courses - Course Reviews';
			DELETE FROM `jos_plugins` WHERE `name` = 'Courses - Course Offerings';";

		$this->get('db')->exec($query);
	}
}