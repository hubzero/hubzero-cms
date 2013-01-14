# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: dev20.hubzero.org (MySQL 5.1.63-0+squeeze1)
# Database: myhub
# Generation Time: 2012-11-08 18:35:22 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table jos_courses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses`;

CREATE TABLE `jos_courses` (
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



# Dump of table jos_courses_announcements
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_announcements`;

CREATE TABLE `jos_courses_announcements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) DEFAULT NULL,
  `announcements` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table jos_courses_asset_associations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_asset_associations`;

CREATE TABLE `jos_courses_asset_associations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `scope_id` int(11) NOT NULL DEFAULT '0',
  `scope` varchar(255) NOT NULL DEFAULT 'asset_group',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table jos_courses_asset_group_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_asset_group_types`;

CREATE TABLE `jos_courses_asset_group_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(200) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table jos_courses_asset_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_asset_groups`;

CREATE TABLE `jos_courses_asset_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(250) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table jos_courses_assets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_assets`;

CREATE TABLE `jos_courses_assets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table jos_courses_email
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_email`;

CREATE TABLE `jos_courses_email` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jos_courses_email_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_email_log`;

CREATE TABLE `jos_courses_email_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `eid` int(11) NOT NULL DEFAULT '0',
  `evid` int(11) NOT NULL DEFAULT '0',
  `to` varchar(100) NOT NULL DEFAULT '',
  `sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sent_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jos_courses_email_version
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_email_version`;

CREATE TABLE `jos_courses_email_version` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `eid` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jos_courses_enrollments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_enrollments`;

CREATE TABLE `jos_courses_enrollments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `enrollment_id` int(11) NOT NULL DEFAULT '0',
  `status` varchar(100) NOT NULL DEFAULT '',
  `fname` varchar(200) NOT NULL DEFAULT '',
  `lname` varchar(200) NOT NULL DEFAULT '',
  `email1` varchar(100) NOT NULL DEFAULT '',
  `email2` varchar(100) NOT NULL DEFAULT '',
  `hubaccount` varchar(100) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table jos_courses_events
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_events`;

CREATE TABLE `jos_courses_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) NOT NULL,
  `actorid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jos_courses_inviteemails
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_inviteemails`;

CREATE TABLE `jos_courses_inviteemails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `gidNumber` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jos_courses_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_log`;

CREATE TABLE `jos_courses_log` (
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



# Dump of table jos_courses_managers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_managers`;

CREATE TABLE `jos_courses_managers` (
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`course_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jos_courses_memberoption
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_memberoption`;

CREATE TABLE `jos_courses_memberoption` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `option_name` varchar(100) NOT NULL DEFAULT '',
  `option_value` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jos_courses_offering_members
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_offering_members`;

CREATE TABLE `jos_courses_offering_members` (
  `offering_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT '0',
  `permissions` mediumtext NOT NULL,
  PRIMARY KEY (`offering_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jos_courses_offerings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_offerings`;

CREATE TABLE `jos_courses_offerings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `term` varchar(255) NOT NULL DEFAULT '',
  `section` int(11) NOT NULL DEFAULT '1',
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table jos_courses_pages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_pages`;

CREATE TABLE `jos_courses_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offering_id` varchar(100) NOT NULL DEFAULT '0',
  `url` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `porder` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '0',
  `privacy` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jos_courses_page_hits
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_page_hits`;

CREATE TABLE `jos_courses_page_hits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jos_courses_roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_roles`;

CREATE TABLE `jos_courses_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(150) NOT NULL,
  `title` varchar(150) NOT NULL DEFAULT '',
  `permissions` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jos_courses_units
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_courses_units`;

CREATE TABLE `jos_courses_units` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(250) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
