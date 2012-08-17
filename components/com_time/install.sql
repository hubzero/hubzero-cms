-- Create syntax for TABLE 'jos_time_auth_token'
CREATE TABLE `jos_time_auth_token` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'jos_time_hub_contacts'
CREATE TABLE `jos_time_hub_contacts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(255) DEFAULT '000-000-0000',
  `email` varchar(255) DEFAULT '',
  `role` varchar(255) DEFAULT '',
  `hub_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'jos_time_hubs'
CREATE TABLE `jos_time_hubs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `name_normalized` varchar(255) NOT NULL DEFAULT '',
  `liaison` varchar(255) DEFAULT NULL,
  `anniversary_date` date DEFAULT '0000-00-00',
  `support_level` varchar(255) DEFAULT 'Standard Support',
  `active` int(1) NOT NULL DEFAULT '1',
  `notes` blob,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'jos_time_records'
CREATE TABLE `jos_time_records` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` double NOT NULL,
  `date` date NOT NULL,
  `description` longtext,
  `billed` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'jos_time_reports'
CREATE TABLE `jos_time_reports` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `report_type` varchar(255) NOT NULL DEFAULT 'bill',
  `user_id` varchar(255) NOT NULL DEFAULT '',
  `time_stamp` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'jos_time_reports_records_assoc'
CREATE TABLE `jos_time_reports_records_assoc` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL,
  `record_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'jos_time_tasks'
CREATE TABLE `jos_time_tasks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `hub_id` int(11) NOT NULL,
  `start_date` date DEFAULT '0000-00-00',
  `end_date` date DEFAULT '0000-00-00',
  `active` int(1) NOT NULL DEFAULT '1',
  `description` blob,
  `priority` int(1) DEFAULT NULL,
  `assignee` int(11) DEFAULT NULL,
  `liaison` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'jos_time_users'
CREATE TABLE `jos_time_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `liaison` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Plugins */
INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
VALUES
	('Time - Overview', 'overview', 'time', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	('Time - Records', 'records', 'time', 0, 1, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	('Time - Tasks', 'tasks', 'time', 0, 2, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	('Time - Hubs', 'hubs', 'time', 0, 3, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	('Time - Reports', 'reports', 'time', 0, 4, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
	('Time - Ajax', 'ajax', 'time', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', '');