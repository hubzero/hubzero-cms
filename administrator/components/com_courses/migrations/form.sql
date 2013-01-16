-- Create syntax for TABLE 'jos_courses_form_answers'
CREATE TABLE `jos_courses_form_answers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `correct` tinyint(4) NOT NULL,
  `left_dist` int(11) NOT NULL,
  `top_dist` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'jos_courses_form_deployments'
CREATE TABLE `jos_courses_form_deployments` (
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

-- Create syntax for TABLE 'jos_courses_form_questions'
CREATE TABLE `jos_courses_form_questions` (
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

-- Create syntax for TABLE 'jos_courses_form_respondent_progress'
CREATE TABLE `jos_courses_form_respondent_progress` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `respondent_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `jos_pdf_form_respondent_progress_respondent_id_question_id_uidx` (`respondent_id`,`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'jos_courses_form_respondents'
CREATE TABLE `jos_courses_form_respondents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `deployment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `started` timestamp NULL DEFAULT NULL,
  `finished` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'jos_courses_form_responses'
CREATE TABLE `jos_courses_form_responses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `respondent_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `jos_pdf_form_respones_respondent_id_idx` (`respondent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'jos_courses_forms'
CREATE TABLE `jos_courses_forms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` text,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for VIEW 'jos_courses_form_latest_responses_view'
CREATE ALGORITHM=UNDEFINED DEFINER=`myhub`@`localhost` SQL SECURITY DEFINER VIEW `jos_courses_form_latest_responses_view`
AS select
   `fre`.`id` AS `id`,
   `fre`.`respondent_id` AS `respondent_id`,
   `fre`.`question_id` AS `question_id`,
   `fre`.`answer_id` AS `answer_id`
from `jos_courses_form_responses` `fre`
where ((select count(0) from `jos_courses_form_responses` `frei`
where ((`frei`.`respondent_id` = `fre`.`respondent_id`) and (`frei`.`id` > `fre`.`id`))) < (select count(distinct `frei`.`question_id`) from `jos_courses_form_responses` `frei`
where (`frei`.`respondent_id` = `fre`.`respondent_id`)));