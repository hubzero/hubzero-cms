#
# @package      hubzero-cms
# @file         installation/sql/mysql/hubzero.sql
# @author       Nicholas J. Kisseberth <nkissebe@purdue.edu>
# @copyright    Copyright (c) 2010-2012 Purdue University. All rights reserved.
# @license      http://www.gnu.org/licenses/gpl2.html GPLv2
#
# Copyright (c) 2010-2012 Purdue University
# All rights reserved.
#
# This file is free software: you can redistribute it and/or modify it
# under the terms of the GNU General Public License as published by the
# Free Software Foundation, either version 2 of the License, or (at your
# option) any later version.
#
# This file is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
# HUBzero is a registered trademark of Purdue University.
#
# This file incorporates work covered by the following copyright and  
# permission notice:  
#
#    $Id: joomla.sql 12384 2009-06-28 03:02:34Z ian $
#    @copyright      Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
#    @license                GNU/GPL, see LICENSE.php
#    Joomla! is free software. This version may have been modified pursuant
#    to the GNU General Public License, and as distributed it includes or
#    is derivative of works licensed under the GNU General Public License or
#    other free or open source software licenses.
#    See COPYRIGHT.php for copyright notices and details.
#

CREATE TABLE `app` (
  `appname` varchar(80) NOT NULL DEFAULT '',
  `geometry` varchar(9) NOT NULL DEFAULT '',
  `depth` smallint(5) unsigned NOT NULL DEFAULT '16',
  `hostreq` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userreq` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeout` int(10) unsigned NOT NULL DEFAULT '0',
  `command` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `display` (
  `hostname` varchar(40) NOT NULL DEFAULT '',
  `dispnum` int(10) unsigned DEFAULT '0',
  `geometry` varchar(9) NOT NULL DEFAULT '',
  `depth` smallint(5) unsigned NOT NULL DEFAULT '16',
  `sessnum` bigint(20) unsigned DEFAULT '0',
  `vncpass` varchar(16) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT '',
  KEY `hostname` (`hostname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `domainclass` (
  `class` tinyint(4) NOT NULL DEFAULT '0',
  `country` varchar(4) NOT NULL,
  `domain` varchar(64) NOT NULL,
  `name` tinytext NOT NULL,
  `state` varchar(4) NOT NULL,
  PRIMARY KEY (`domain`),
  KEY `class` (`class`) USING BTREE,
  KEY `domain` (`domain`,`class`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `domainclasses` (
  `class` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `fileperm` (
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fileuser` varchar(32) NOT NULL DEFAULT '',
  `fwhost` varchar(40) NOT NULL DEFAULT '',
  `fwport` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cookie` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`sessnum`,`fileuser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `host` (
  `hostname` varchar(40) NOT NULL DEFAULT '',
  `provisions` bigint(20) unsigned NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT '',
  `uses` smallint(5) unsigned NOT NULL DEFAULT '0',
  `portbase` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`hostname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `hosttype` (
  `name` varchar(40) NOT NULL DEFAULT '',
  `value` bigint(20) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ipusers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `user` tinytext NOT NULL,
  `ntimes` smallint(6) NOT NULL,
  `from` datetime DEFAULT NULL,
  `to` datetime DEFAULT NULL,
  `orgtype` varchar(4) NOT NULL,
  `countryresident` char(2) NOT NULL,
  `countrycitizen` char(2) NOT NULL,
  `countryip` char(2) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `job` (
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `jobid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `superjob` bigint(20) unsigned NOT NULL DEFAULT '0',
  `username` varchar(32) NOT NULL DEFAULT '',
  `event` varchar(40) NOT NULL DEFAULT '',
  `ncpus` smallint(5) unsigned NOT NULL DEFAULT '0',
  `venue` varchar(80) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `heartbeat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` smallint(2) NOT NULL DEFAULT '1',
  UNIQUE KEY `jobid` (`jobid`),
  KEY `start` (`start`),
  KEY `heartbeat` (`heartbeat`),
  KEY `start_2` (`start`),
  KEY `heartbeat_2` (`heartbeat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `joblog` (
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `job` int(10) unsigned NOT NULL DEFAULT '0',
  `superjob` bigint(20) unsigned NOT NULL DEFAULT '0',
  `event` varchar(40) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `walltime` double unsigned DEFAULT '0',
  `cputime` double unsigned DEFAULT '0',
  `ncpus` smallint(5) unsigned NOT NULL DEFAULT '0',
  `status` smallint(5) unsigned DEFAULT '0',
  `venue` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`sessnum`,`job`,`event`,`venue`),
  KEY `sessnum` (`sessnum`),
  KEY `event` (`event`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__abuse_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) DEFAULT NULL,
  `referenceid` int(11) DEFAULT '0',
  `report` text NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` int(3) DEFAULT '0',
  `subject` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__answers_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(15) DEFAULT NULL,
  `helpful` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__answers_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(250) DEFAULT NULL,
  `question` text,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` varchar(50) DEFAULT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `anonymous` tinyint(2) NOT NULL DEFAULT '0',
  `email` tinyint(2) DEFAULT '0',
  `helpful` int(11) DEFAULT '0',
  `reward` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `question` (`question`),
  FULLTEXT KEY `subject` (`subject`),
  FULLTEXT KEY `#__answers_questions_question_subject_ftidx` (`question`,`subject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__answers_questions_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qid` int(11) NOT NULL DEFAULT '0',
  `expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `voter` int(11) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__answers_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qid` int(11) NOT NULL DEFAULT '0',
  `answer` text,
  `created_by` varchar(50) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `helpful` int(11) NOT NULL DEFAULT '0',
  `nothelpful` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `anonymous` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `answer` (`answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__answers_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `questionid` int(11) NOT NULL DEFAULT '0',
  `tagid` int(11) NOT NULL DEFAULT '0',
  `taggerid` varchar(200) DEFAULT NULL,
  `taggedon` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__auth_domain` (
  `authenticator` varchar(255) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `params` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__auth_link` (
  `auth_domain_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `params` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__author_assoc` (
  `subtable` varchar(50) NOT NULL DEFAULT '',
  `subid` int(11) NOT NULL DEFAULT '0',
  `authorid` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`subtable`,`subid`,`authorid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__author_role_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__author_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__author_stats` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `authorid` int(11) NOT NULL,
  `tool_users` bigint(20) DEFAULT NULL,
  `andmore_users` bigint(20) DEFAULT NULL,
  `total_users` bigint(20) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__banner` (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL DEFAULT '0',
  `type` varchar(30) NOT NULL DEFAULT 'banner',
  `name` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `imptotal` int(11) NOT NULL DEFAULT '0',
  `impmade` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  `imageurl` varchar(100) NOT NULL DEFAULT '',
  `clickurl` varchar(200) NOT NULL DEFAULT '',
  `date` datetime DEFAULT NULL,
  `showBanner` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editor` varchar(50) DEFAULT NULL,
  `custombannercode` text,
  `catid` int(10) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tags` text NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`bid`),
  KEY `viewbanner` (`showBanner`),
  KEY `idx_banner_catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__bannerclient` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `contact` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `extrainfo` text NOT NULL,
  `checked_out` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out_time` time DEFAULT NULL,
  `editor` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__bannertrack` (
  `track_date` date NOT NULL,
  `track_type` int(10) unsigned NOT NULL,
  `banner_id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__billboard_collection` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__billboards` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `header` varchar(255) DEFAULT NULL,
  `text` text,
  `learn_more_text` varchar(255) DEFAULT NULL,
  `learn_more_target` varchar(255) DEFAULT NULL,
  `learn_more_class` varchar(255) DEFAULT NULL,
  `learn_more_location` varchar(255) DEFAULT NULL,
  `background_img` varchar(255) DEFAULT NULL,
  `padding` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `css` text,
  `published` tinyint(1) DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `checked_out` int(11) DEFAULT '0',
  `checked_out_time` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__blog_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) DEFAULT '0',
  `content` text,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) DEFAULT '0',
  `anonymous` tinyint(2) DEFAULT '0',
  `parent` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__blog_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `content` text,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) DEFAULT '0',
  `state` tinyint(2) DEFAULT '0',
  `publish_up` datetime DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime DEFAULT '0000-00-00 00:00:00',
  `params` tinytext,
  `group_id` int(11) DEFAULT '0',
  `hits` int(11) DEFAULT '0',
  `allow_comments` tinyint(2) DEFAULT '0',
  `scope` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__cart` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `itemid` int(11) NOT NULL DEFAULT '0',
  `type` varchar(20) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `selections` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `section` varchar(50) NOT NULL DEFAULT '',
  `image_position` varchar(30) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editor` varchar(50) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cat_idx` (`section`,`published`,`access`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__citations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(200) DEFAULT NULL,
  `affiliated` int(3) DEFAULT NULL,
  `fundedby` int(3) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `address` varchar(250) DEFAULT NULL,
  `author` text,
  `booktitle` varchar(250) DEFAULT NULL,
  `chapter` varchar(250) DEFAULT NULL,
  `cite` varchar(250) DEFAULT NULL,
  `edition` varchar(250) DEFAULT NULL,
  `editor` varchar(250) DEFAULT NULL,
  `eprint` varchar(250) DEFAULT NULL,
  `howpublished` varchar(250) DEFAULT NULL,
  `institution` varchar(250) DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `journal` varchar(250) DEFAULT NULL,
  `key` varchar(250) DEFAULT NULL,
  `location` varchar(250) DEFAULT NULL,
  `month` varchar(50) DEFAULT NULL,
  `note` text,
  `number` varchar(50) DEFAULT NULL,
  `organization` varchar(250) DEFAULT NULL,
  `pages` varchar(250) DEFAULT NULL,
  `publisher` varchar(250) DEFAULT NULL,
  `series` varchar(250) DEFAULT NULL,
  `school` varchar(250) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `volume` int(11) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `doi` varchar(250) DEFAULT NULL,
  `ref_type` varchar(50) DEFAULT NULL,
  `date_submit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_accept` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_publish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `software_use` int(3) DEFAULT NULL,
  `res_edu` int(3) DEFAULT NULL,
  `exp_list_exp_data` int(3) DEFAULT NULL,
  `exp_data` int(3) DEFAULT NULL,
  `notes` text,
  `published` int(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__citations_assoc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT '0',
  `oid` int(11) DEFAULT '0',
  `type` varchar(50) DEFAULT NULL,
  `tbl` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__citations_authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT '0',
  `author` varchar(64) DEFAULT NULL,
  `authorid` int(11) DEFAULT '0',
  `uidNumber` int(11) DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `givenName` varchar(255) NOT NULL DEFAULT '',
  `middleName` varchar(255) NOT NULL DEFAULT '',
  `surname` varchar(255) NOT NULL DEFAULT '',
  `organization` varchar(255) NOT NULL DEFAULT '',
  `org_dept` varchar(255) NOT NULL DEFAULT '',
  `orgtype` varchar(255) NOT NULL DEFAULT '',
  `countryresident` char(2) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `ip` varchar(40) NOT NULL DEFAULT '',
  `host` varchar(64) NOT NULL DEFAULT '',
  `countrySHORT` char(2) NOT NULL DEFAULT '',
  `countryLONG` varchar(64) NOT NULL DEFAULT '',
  `ipREGION` varchar(128) NOT NULL DEFAULT '',
  `ipCITY` varchar(128) NOT NULL DEFAULT '',
  `ipLATITUDE` double DEFAULT NULL,
  `ipLONGITUDE` double DEFAULT NULL,
  `in_network` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cid_auth_authid_uid` (`cid`,`author`,`authorid`,`uidNumber`),
  KEY `authorid` (`authorid`),
  KEY `uidNumber` (`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__citations_secondary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `sec_cits_cnt` int(11) DEFAULT NULL,
  `search_string` tinytext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__citations_sponsors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sponsor` varchar(150) DEFAULT NULL,
  `link` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__citations_sponsors_assoc` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT NULL,
  `sid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__citations_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `type_title` varchar(255) DEFAULT NULL,
  `type_desc` text,
  `type_export` varchar(255) DEFAULT NULL,
  `fields` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referenceid` varchar(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `comment` text,
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `added_by` int(11) DEFAULT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `anonymous` tinyint(2) NOT NULL DEFAULT '0',
  `email` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `question` (`comment`),
  FULLTEXT KEY `subject` (`referenceid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `menuid` int(11) unsigned NOT NULL DEFAULT '0',
  `parent` int(11) unsigned NOT NULL DEFAULT '0',
  `admin_menu_link` varchar(255) NOT NULL DEFAULT '',
  `admin_menu_alt` varchar(255) NOT NULL DEFAULT '',
  `option` varchar(50) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `admin_menu_img` varchar(255) NOT NULL DEFAULT '',
  `iscore` tinyint(4) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `enabled` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `parent_option` (`parent`,`option`(32))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__contact_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `con_position` varchar(255) DEFAULT NULL,
  `address` text,
  `suburb` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postcode` varchar(100) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `misc` mediumtext,
  `image` varchar(255) DEFAULT NULL,
  `imagepos` varchar(20) DEFAULT NULL,
  `email_to` varchar(255) DEFAULT NULL,
  `default_con` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `mobile` varchar(255) NOT NULL DEFAULT '',
  `webpage` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `title_alias` varchar(255) NOT NULL DEFAULT '',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `sectionid` int(11) unsigned NOT NULL DEFAULT '0',
  `mask` int(11) unsigned NOT NULL DEFAULT '0',
  `catid` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `images` text NOT NULL,
  `urls` text NOT NULL,
  `attribs` text NOT NULL,
  `version` int(11) unsigned NOT NULL DEFAULT '1',
  `parentid` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `access` int(11) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `metadata` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_section` (`sectionid`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`),
  KEY `#__content_state_idx` (`state`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `introtext` (`introtext`,`fulltext`),
  FULLTEXT KEY `#__content_title_introtext_fulltext_ftidx` (`title`,`introtext`,`fulltext`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__content_frontpage` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__content_rating` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `rating_sum` int(11) unsigned NOT NULL DEFAULT '0',
  `rating_count` int(11) unsigned NOT NULL DEFAULT '0',
  `lastip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__core_acl_aro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_value` varchar(240) NOT NULL DEFAULT '0',
  `value` varchar(240) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `#__section_value_value_aro` (`section_value`(100),`value`(100)),
  KEY `#__gacl_hidden_aro` (`hidden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__core_acl_aro_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `#__gacl_parent_id_aro_groups` (`parent_id`),
  KEY `#__gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__core_acl_aro_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(230) NOT NULL DEFAULT '0',
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`acl_id`,`section_value`,`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__core_acl_aro_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(230) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `#__gacl_value_aro_sections` (`value`),
  KEY `#__gacl_hidden_aro_sections` (`hidden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__core_acl_groups_aro_map` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(240) NOT NULL DEFAULT '',
  `aro_id` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `group_id_aro_id_groups_aro_map` (`group_id`,`section_value`,`aro_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__core_log_items` (
  `time_stamp` date NOT NULL DEFAULT '0000-00-00',
  `item_table` varchar(50) NOT NULL DEFAULT '',
  `item_id` int(11) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__core_log_searches` (
  `search_term` varchar(128) NOT NULL DEFAULT '',
  `hits` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__document_resource_rel` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `#__document_resource_rel_document_id_resource_id_uidx` (`document_id`,`resource_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__document_text_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `body` text,
  `hash` char(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `#__document_text_data_hash_uidx` (`hash`),
  FULLTEXT KEY `#__document_text_data_body_ftidx` (`body`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__doi_mapping` (
  `local_revision` int(11) NOT NULL,
  `doi_label` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `alias` varchar(30) DEFAULT NULL,
  `versionid` int(11) DEFAULT '0',
  `doi` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__event_registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `institution` varchar(100) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(10) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `submitted` datetime DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__events` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  `adresse_info` varchar(120) NOT NULL DEFAULT '',
  `contact_info` varchar(120) NOT NULL DEFAULT '',
  `extra_info` varchar(240) NOT NULL DEFAULT '',
  `color_bar` varchar(8) NOT NULL DEFAULT '',
  `useCatColor` tinyint(1) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `mask` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(100) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_zone` varchar(5) DEFAULT NULL,
  `images` text NOT NULL,
  `reccurtype` tinyint(1) NOT NULL DEFAULT '0',
  `reccurday` varchar(4) NOT NULL DEFAULT '',
  `reccurweekdays` varchar(20) NOT NULL DEFAULT '',
  `reccurweeks` varchar(10) NOT NULL DEFAULT '',
  `approved` tinyint(1) NOT NULL DEFAULT '1',
  `announcement` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `access` int(11) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) NOT NULL DEFAULT '0',
  `registerby` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text,
  `restricted` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `content` (`content`),
  FULLTEXT KEY `#__events_title_content_ftidx` (`title`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `#__events_categories` (
  `id` int(12) NOT NULL DEFAULT '0',
  `color` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `#__events_config` (
  `param` varchar(100) DEFAULT NULL,
  `value` tinytext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `#__events_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) DEFAULT '0',
  `alias` varchar(100) NOT NULL,
  `title` varchar(250) NOT NULL,
  `pagetext` text,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) DEFAULT '0',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT '0',
  `ordering` int(2) DEFAULT '0',
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `#__events_respondent_race_rel` (
  `respondent_id` int(11) DEFAULT NULL,
  `race` varchar(255) DEFAULT NULL,
  `tribal_affiliation` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `#__events_respondents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL DEFAULT '0',
  `registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `affiliation` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `position_description` varchar(50) DEFAULT NULL,
  `highest_degree` varchar(10) DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `disability_needs` tinyint(4) DEFAULT NULL,
  `dietary_needs` varchar(500) DEFAULT NULL,
  `attending_dinner` tinyint(4) DEFAULT NULL,
  `abstract` text,
  `comment` text,
  `arrival` varchar(50) DEFAULT NULL,
  `departure` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `#__faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `alias` varchar(200) DEFAULT NULL,
  `params` text,
  `fulltxt` text,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) DEFAULT '0',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT '0',
  `checked_out` int(11) DEFAULT '0',
  `checked_out_time` datetime DEFAULT '0000-00-00 00:00:00',
  `state` int(3) DEFAULT '0',
  `access` tinyint(3) DEFAULT '0',
  `hits` int(11) DEFAULT '0',
  `version` int(11) DEFAULT '0',
  `section` int(11) NOT NULL DEFAULT '0',
  `category` int(11) DEFAULT '0',
  `helpful` int(11) NOT NULL DEFAULT '0',
  `nothelpful` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `#__faq_title_introtext_fulltext_ftidx` (`title`,`params`,`fulltxt`),
  FULLTEXT KEY `introtext` (`params`),
  FULLTEXT KEY `fulltxt` (`fulltxt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__faq_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `alias` varchar(200) DEFAULT NULL,
  `description` varchar(255) DEFAULT '',
  `section` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `access` tinyint(3) NOT NULL DEFAULT '0',
  `asset_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__faq_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) NOT NULL DEFAULT '0',
  `content` text,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `anonymous` tinyint(2) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `helpful` int(11) NOT NULL DEFAULT '0',
  `nothelpful` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__faq_helpful_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) DEFAULT '0',
  `ip` varchar(15) DEFAULT NULL,
  `vote` varchar(10) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__feature_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `objectid` int(11) DEFAULT NULL,
  `featured` datetime DEFAULT '0000-00-00 00:00:00',
  `tbl` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT '',
  `org` varchar(100) DEFAULT '',
  `quote` text,
  `picture` varchar(250) DEFAULT '',
  `date` datetime DEFAULT '0000-00-00 00:00:00',
  `publish_ok` tinyint(1) DEFAULT '0',
  `contact_ok` tinyint(1) DEFAULT '0',
  `notes` text,
  `short_quote` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__focus_area_resource_type_rel` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `focus_area_id` int(11) NOT NULL,
  `resource_type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__focus_areas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL,
  `mandatory_depth` int(11) DEFAULT NULL,
  `multiple_depth` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__forum_attachments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL DEFAULT '0',
  `post_id` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__forum_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `description` text,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(2) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `section_id` int(11) NOT NULL DEFAULT '0',
  `closed` tinyint(2) NOT NULL DEFAULT '0',
  `asset_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__forum_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `comment` text,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `sticky` tinyint(2) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `hits` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(2) NOT NULL DEFAULT '0',
  `anonymous` tinyint(2) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `last_activity` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `asset_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `question` (`comment`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__forum_sections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(2) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `asset_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__groups` (
  `id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__incremental_registration_group_label_rel` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__incremental_registration_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hours` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__incremental_registration_labels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__incremental_registration_options` (
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `popover_text` text NOT NULL,
  `award_per` int(11) NOT NULL,
  `test_group` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__incremental_registration_popover_recurrence` (
  `idx` int(11) NOT NULL,
  `hours` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `applied` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `withdrawn` datetime DEFAULT '0000-00-00 00:00:00',
  `cover` text,
  `resumeid` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '1',
  `reason` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(150) NOT NULL DEFAULT '',
  `ordernum` int(11) NOT NULL DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_employers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `subscriptionid` int(11) NOT NULL DEFAULT '0',
  `companyName` varchar(250) DEFAULT '',
  `companyLocation` varchar(250) DEFAULT '',
  `companyWebsite` varchar(250) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_openings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT '0',
  `employerid` int(11) NOT NULL DEFAULT '0',
  `code` int(11) NOT NULL DEFAULT '0',
  `title` varchar(200) NOT NULL DEFAULT '',
  `companyName` varchar(200) NOT NULL DEFAULT '',
  `companyLocation` varchar(200) DEFAULT '',
  `companyLocationCountry` varchar(100) DEFAULT '',
  `companyWebsite` varchar(200) DEFAULT '',
  `description` text,
  `addedBy` int(11) NOT NULL DEFAULT '0',
  `editedBy` int(11) DEFAULT '0',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `edited` datetime DEFAULT '0000-00-00 00:00:00',
  `status` int(3) NOT NULL DEFAULT '0',
  `type` int(3) NOT NULL DEFAULT '0',
  `closedate` datetime DEFAULT '0000-00-00 00:00:00',
  `opendate` datetime DEFAULT '0000-00-00 00:00:00',
  `startdate` datetime DEFAULT '0000-00-00 00:00:00',
  `applyExternalUrl` varchar(250) DEFAULT '',
  `applyInternal` int(3) DEFAULT '0',
  `contactName` varchar(100) DEFAULT '',
  `contactEmail` varchar(100) DEFAULT '',
  `contactPhone` varchar(100) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_prefs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0',
  `category` varchar(20) NOT NULL DEFAULT 'resume',
  `filters` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_resumes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` varchar(100) DEFAULT NULL,
  `filename` varchar(100) DEFAULT NULL,
  `main` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_seekers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '0',
  `lookingfor` varchar(255) DEFAULT '',
  `tagline` varchar(255) DEFAULT '',
  `linkedin` varchar(255) DEFAULT '',
  `url` varchar(255) DEFAULT '',
  `updated` datetime DEFAULT '0000-00-00 00:00:00',
  `sought_cid` int(11) DEFAULT '0',
  `sought_type` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_shortlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp` int(11) NOT NULL DEFAULT '0',
  `seeker` int(11) NOT NULL DEFAULT '0',
  `category` varchar(11) NOT NULL DEFAULT 'resume',
  `jobid` int(11) DEFAULT '0',
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL,
  `category` varchar(11) NOT NULL DEFAULT '',
  `total_viewed` int(11) DEFAULT '0',
  `total_shared` int(11) DEFAULT '0',
  `viewed_today` int(11) DEFAULT '0',
  `lastviewed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) DEFAULT NULL,
  `description` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__licenses_tools` (
  `license_id` int(11) DEFAULT '0',
  `tool_id` int(11) DEFAULT '0',
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__licenses_users` (
  `license_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  PRIMARY KEY (`license_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__market_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL DEFAULT '0',
  `category` varchar(50) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `action` varchar(50) DEFAULT NULL,
  `log` text,
  `market_value` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menutype` varchar(75) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `link` text,
  `type` varchar(50) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `parent` int(11) unsigned NOT NULL DEFAULT '0',
  `componentid` int(11) unsigned NOT NULL DEFAULT '0',
  `sublevel` int(11) DEFAULT '0',
  `ordering` int(11) DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pollid` int(11) NOT NULL DEFAULT '0',
  `browserNav` tinyint(4) DEFAULT '0',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `utaccess` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `lft` int(11) unsigned NOT NULL DEFAULT '0',
  `rgt` int(11) unsigned NOT NULL DEFAULT '0',
  `home` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `componentid` (`componentid`,`menutype`,`published`,`access`),
  KEY `menutype` (`menutype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__menu_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menutype` varchar(75) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `menutype` (`menutype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__messages` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id_from` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id_to` int(10) unsigned NOT NULL DEFAULT '0',
  `folder_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` int(11) NOT NULL DEFAULT '0',
  `priority` int(1) unsigned NOT NULL DEFAULT '0',
  `subject` text NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `useridto_state` (`user_id_to`,`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__messages_cfg` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `cfg_name` varchar(100) NOT NULL DEFAULT '',
  `cfg_value` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `idx_user_var_name` (`user_id`,`cfg_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__migration_backlinks` (
  `itemid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` text NOT NULL,
  `sefurl` text NOT NULL,
  `newurl` text NOT NULL,
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `position` varchar(50) DEFAULT NULL,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `module` varchar(50) DEFAULT NULL,
  `numnews` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `showtitle` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `params` text NOT NULL,
  `iscore` tinyint(4) NOT NULL DEFAULT '0',
  `client_id` tinyint(4) NOT NULL DEFAULT '0',
  `control` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`,`access`),
  KEY `newsfeeds` (`module`,`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__modules_menu` (
  `moduleid` int(11) NOT NULL DEFAULT '0',
  `menuid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`moduleid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__myhub` (
  `uid` int(11) NOT NULL,
  `prefs` varchar(200) DEFAULT NULL,
  `modified` datetime DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__myhub_params` (
  `uid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `params` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__newsfeeds` (
  `catid` int(11) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `link` text NOT NULL,
  `filename` varchar(200) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `numarticles` int(11) unsigned NOT NULL DEFAULT '1',
  `cache_time` int(11) unsigned NOT NULL DEFAULT '3600',
  `checked_out` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `rtl` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__order_items` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `oid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `itemid` int(11) NOT NULL DEFAULT '0',
  `price` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `selections` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__orders` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `type` varchar(20) DEFAULT NULL,
  `total` int(11) DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `details` text,
  `email` varchar(150) DEFAULT NULL,
  `ordered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status_changed` datetime DEFAULT '0000-00-00 00:00:00',
  `notes` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__password_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` char(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__password_character_class` (
  `flag` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(32) NOT NULL,
  `regex` char(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__password_rule` (
  `class` char(255) DEFAULT NULL,
  `description` char(255) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `failuremsg` char(255) DEFAULT NULL,
  `grp` char(32) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `rule` char(255) DEFAULT NULL,
  `value` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__plugin_params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) DEFAULT '0',
  `folder` varchar(100) DEFAULT NULL,
  `element` varchar(100) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `element` varchar(100) NOT NULL DEFAULT '',
  `folder` varchar(100) NOT NULL DEFAULT '',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `iscore` tinyint(3) NOT NULL DEFAULT '0',
  `client_id` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_folder` (`published`,`client_id`,`access`,`folder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__poll_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pollid` int(11) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pollid` (`pollid`,`text`(1))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__poll_date` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vote_id` int(11) NOT NULL DEFAULT '0',
  `poll_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__poll_menu` (
  `pollid` int(11) NOT NULL DEFAULT '0',
  `menuid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pollid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__polls` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `voters` int(9) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `access` int(11) NOT NULL DEFAULT '0',
  `lag` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__profile_completion_awards` (
  `user_id` int(11) NOT NULL,
  `name` tinyint(4) NOT NULL DEFAULT '0',
  `orgtype` tinyint(4) NOT NULL DEFAULT '0',
  `organization` tinyint(4) NOT NULL DEFAULT '0',
  `countryresident` tinyint(4) NOT NULL DEFAULT '0',
  `countryorigin` tinyint(4) NOT NULL DEFAULT '0',
  `gender` tinyint(4) NOT NULL DEFAULT '0',
  `url` tinyint(4) NOT NULL DEFAULT '0',
  `reason` tinyint(4) NOT NULL DEFAULT '0',
  `race` tinyint(4) NOT NULL DEFAULT '0',
  `phone` tinyint(4) NOT NULL DEFAULT '0',
  `picture` tinyint(4) NOT NULL DEFAULT '0',
  `opted_out` tinyint(4) NOT NULL DEFAULT '0',
  `logins` int(11) NOT NULL DEFAULT '1',
  `invocations` int(11) NOT NULL DEFAULT '0',
  `last_bothered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `bothered_times` int(11) NOT NULL DEFAULT '0',
  `edited_profile` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectid` int(11) NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `referenceid` varchar(255) NOT NULL DEFAULT '0',
  `managers_only` tinyint(2) DEFAULT '0',
  `admin` tinyint(2) DEFAULT '0',
  `commentable` tinyint(2) NOT NULL DEFAULT '0',
  `state` tinyint(2) NOT NULL DEFAULT '0',
  `recorded` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activity` varchar(255) NOT NULL DEFAULT '',
  `highlighted` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(255) DEFAULT NULL,
  `class` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `activityid` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(2) NOT NULL DEFAULT '0',
  `parent_activity` int(11) DEFAULT '0',
  `anonymous` tinyint(2) DEFAULT '0',
  `admin` tinyint(2) DEFAULT '0',
  `tbl` varchar(50) NOT NULL DEFAULT 'blog',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_microblog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blogentry` varchar(255) DEFAULT NULL,
  `posted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `posted_by` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(2) DEFAULT '0',
  `params` tinytext,
  `projectid` int(11) NOT NULL DEFAULT '0',
  `activityid` int(11) NOT NULL DEFAULT '0',
  `managers_only` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`blogentry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_owners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectid` int(11) NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `groupid` int(11) DEFAULT '0',
  `invited_name` varchar(100)  DEFAULT NULL,
  `invited_email` varchar(100) DEFAULT NULL,
  `invited_code` varchar(10) DEFAULT NULL,
  `added` datetime NOT NULL,
  `lastvisit` datetime DEFAULT NULL,
  `prev_visit` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `num_visits` int(11) NOT NULL DEFAULT '0',
  `role` int(11) NOT NULL DEFAULT '0',
  `native` int(11) NOT NULL DEFAULT '0',
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_todo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectid` int(11) NOT NULL DEFAULT '0',
  `todolist` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `duedate` datetime DEFAULT NULL,
  `closed` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `assigned_to` int(11) DEFAULT '0',
  `closed_by` int(11) DEFAULT '0',
  `priority` int(11) DEFAULT '0',
  `activityid` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `milestone` tinyint(1) NOT NULL DEFAULT '0',
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `details` text,
  `content` varchar(255) NOT NULL,
  `color` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(150) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(30) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `picture` varchar(255) DEFAULT '',
  `about` text,
  `state` int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '1',
  `provisioned` int(11) NOT NULL DEFAULT '0',
  `private` int(11) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `owned_by_user` int(11) NOT NULL DEFAULT '0',
  `created_by_user` int(11) NOT NULL,
  `owned_by_group` int(11) DEFAULT '0',
  `modified_by` int(11) DEFAULT '0',
  `setup_stage` int(11) NOT NULL DEFAULT '0',
  `params` text,
  `admin_notes` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__recent_tools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `tool` varchar(200) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__recommendation` (
  `fromID` int(11) NOT NULL,
  `toID` int(11) NOT NULL,
  `contentScore` float unsigned zerofill DEFAULT NULL,
  `tagScore` float unsigned zerofill DEFAULT NULL,
  `titleScore` float unsigned zerofill DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fromID`,`toID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__redirection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cpt` int(11) NOT NULL DEFAULT '0',
  `oldurl` varchar(100) NOT NULL DEFAULT '',
  `newurl` varchar(150) NOT NULL DEFAULT '',
  `dateadd` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `newurl` (`newurl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_assoc` (
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `child_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `grouping` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `text` text,
  `title` varchar(100) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `apps_only` tinyint(3) NOT NULL DEFAULT '0',
  `main` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `agreement` tinyint(2) NOT NULL DEFAULT '0',
  `info` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `comment` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anonymous` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_sponsors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_stats` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `resid` bigint(20) NOT NULL,
  `restype` int(11) DEFAULT NULL,
  `users` bigint(20) DEFAULT NULL,
  `jobs` bigint(20) DEFAULT NULL,
  `avg_wall` int(20) DEFAULT NULL,
  `tot_wall` int(20) DEFAULT NULL,
  `avg_cpu` int(20) DEFAULT NULL,
  `tot_cpu` int(20) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT '-1',
  `processed_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `res_stats` (`resid`,`restype`,`datetime`,`period`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_stats_clusters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cluster` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(32) NOT NULL DEFAULT '',
  `uidNumber` int(11) NOT NULL DEFAULT '0',
  `toolname` varchar(80) NOT NULL DEFAULT '',
  `resid` int(11) NOT NULL DEFAULT '0',
  `clustersize` varchar(255) NOT NULL DEFAULT '',
  `cluster_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cluster_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `institution` varchar(255) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cluster` (`cluster`),
  KEY `username` (`username`),
  KEY `uidNumber` (`uidNumber`),
  KEY `toolname` (`toolname`),
  KEY `resid` (`resid`),
  KEY `clustersize` (`clustersize`),
  KEY `cluster_start` (`cluster_start`),
  KEY `cluster_end` (`cluster_end`),
  KEY `institution` (`institution`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_stats_tools` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `resid` bigint(20) NOT NULL,
  `restype` int(11) NOT NULL,
  `users` bigint(20) DEFAULT NULL,
  `sessions` bigint(20) DEFAULT NULL,
  `simulations` bigint(20) DEFAULT NULL,
  `jobs` bigint(20) DEFAULT NULL,
  `avg_wall` double unsigned DEFAULT '0',
  `tot_wall` double unsigned DEFAULT '0',
  `avg_cpu` double unsigned DEFAULT '0',
  `tot_cpu` double unsigned DEFAULT '0',
  `avg_view` double unsigned DEFAULT '0',
  `tot_view` double unsigned DEFAULT '0',
  `avg_wait` double unsigned DEFAULT '0',
  `tot_wait` double unsigned DEFAULT '0',
  `avg_cpus` int(20) DEFAULT NULL,
  `tot_cpus` int(20) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT '-1',
  `processed_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_stats_tools_tops` (
  `top` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  `valfmt` tinyint(4) NOT NULL DEFAULT '0',
  `size` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`top`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_stats_tools_topvals` (
  `id` bigint(20) NOT NULL,
  `top` tinyint(4) NOT NULL DEFAULT '0',
  `rank` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `value` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_stats_tools_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `resid` bigint(20) NOT NULL,
  `restype` int(11) NOT NULL,
  `user` varchar(32) NOT NULL DEFAULT '',
  `sessions` bigint(20) DEFAULT NULL,
  `simulations` bigint(20) DEFAULT NULL,
  `jobs` bigint(20) DEFAULT NULL,
  `tot_wall` double unsigned DEFAULT '0',
  `tot_cpu` double unsigned DEFAULT '0',
  `tot_view` double unsigned DEFAULT '0',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT '-1',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resourceid` int(11) DEFAULT NULL,
  `tagid` int(11) DEFAULT NULL,
  `strength` tinyint(3) DEFAULT '0',
  `taggerid` int(11) DEFAULT '0',
  `taggedon` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_taxonomy_audience` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL DEFAULT '0',
  `versionid` int(11) DEFAULT '0',
  `level0` tinyint(2) NOT NULL DEFAULT '0',
  `level1` tinyint(2) NOT NULL DEFAULT '0',
  `level2` tinyint(2) NOT NULL DEFAULT '0',
  `level3` tinyint(2) NOT NULL DEFAULT '0',
  `level4` tinyint(2) NOT NULL DEFAULT '0',
  `level5` tinyint(2) NOT NULL DEFAULT '0',
  `comments` varchar(255) DEFAULT '',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `addedBy` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_taxonomy_audience_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(11) NOT NULL DEFAULT '0',
  `title` varchar(100) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(100) DEFAULT NULL,
  `type` varchar(200) NOT NULL DEFAULT '',
  `category` int(11) NOT NULL DEFAULT '0',
  `description` tinytext,
  `contributable` int(2) DEFAULT '1',
  `customFields` text,
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `type` int(11) NOT NULL DEFAULT '0',
  `logical_type` int(11) NOT NULL DEFAULT '0',
  `introtext` text NOT NULL,
  `fulltxt` text NOT NULL,
  `footertext` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `published` int(1) NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(11) NOT NULL DEFAULT '0',
  `hits` int(11) NOT NULL DEFAULT '0',
  `path` varchar(200) NOT NULL DEFAULT '',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `standalone` tinyint(1) NOT NULL DEFAULT '0',
  `group_owner` varchar(250) NOT NULL DEFAULT '',
  `group_access` text,
  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `times_rated` int(11) NOT NULL DEFAULT '0',
  `params` text,
  `attribs` text,
  `alias` varchar(100) NOT NULL DEFAULT '',
  `ranking` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `introtext` (`introtext`,`fulltxt`),
  FULLTEXT KEY `#__resources_title_introtext_fulltext_ftidx` (`title`,`introtext`,`fulltxt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__screenshots` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `versionid` int(11) DEFAULT '0',
  `title` varchar(127) DEFAULT '',
  `ordering` int(11) DEFAULT '0',
  `filename` varchar(100) NOT NULL,
  `resourceid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `image` text NOT NULL,
  `scope` varchar(50) NOT NULL DEFAULT '',
  `image_position` varchar(30) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_scope` (`scope`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__selected_quotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT '0',
  `fullname` varchar(100) DEFAULT '',
  `org` varchar(200) DEFAULT '',
  `miniquote` varchar(200) DEFAULT '',
  `short_quote` text,
  `quote` text,
  `picture` varchar(250) DEFAULT '',
  `date` datetime DEFAULT '0000-00-00 00:00:00',
  `flash_rotation` tinyint(1) DEFAULT '0',
  `notable_quotes` tinyint(1) DEFAULT '1',
  `notes` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__session` (
  `username` varchar(150) DEFAULT '',
  `time` varchar(14) DEFAULT '',
  `session_id` varchar(200) NOT NULL DEFAULT '0',
  `guest` tinyint(4) DEFAULT '1',
  `userid` int(11) DEFAULT '0',
  `usertype` varchar(50) DEFAULT '',
  `gid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `client_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `data` longtext,
  `ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`session_id`) USING BTREE,
  KEY `whosonline` (`guest`,`usertype`),
  KEY `userid` (`userid`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__session_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clientid` tinyint(4) DEFAULT NULL,
  `session_id` char(64) DEFAULT NULL,
  `psid` char(64) DEFAULT NULL,
  `rsid` char(64) DEFAULT NULL,
  `ssid` char(64) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `authenticator` char(64) DEFAULT NULL,
  `source` char(64) DEFAULT NULL,
  `ip` char(64) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `teaser` varchar(255) DEFAULT NULL,
  `description` text,
  `notes` text,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `published_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__stats_agents` (
  `agent` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__stats_tops` (
  `id` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  `valfmt` tinyint(4) NOT NULL DEFAULT '0',
  `size` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__stats_topvals` (
  `top` tinyint(4) NOT NULL DEFAULT '0',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT '1',
  `rank` smallint(6) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `value` bigint(20) NOT NULL DEFAULT '0',
  KEY `top` (`top`),
  KEY `top_2` (`top`,`rank`),
  KEY `top_3` (`top`,`datetime`),
  KEY `top_4` (`top`,`datetime`,`rank`),
  KEY `top_5` (`top`,`datetime`,`period`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__store` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(127) NOT NULL DEFAULT '',
  `price` int(11) NOT NULL DEFAULT '0',
  `description` text,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `available` int(1) NOT NULL DEFAULT '0',
  `params` text,
  `special` int(11) DEFAULT '0',
  `type` int(11) DEFAULT '1',
  `category` varchar(127) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_acl_acos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(100) DEFAULT NULL,
  `foreign_key` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_acl_aros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(100) DEFAULT NULL,
  `foreign_key` int(11) DEFAULT '0',
  `alias` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_acl_aros_acos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aro_id` int(11) DEFAULT '0',
  `aco_id` int(11) DEFAULT '0',
  `action_create` int(3) DEFAULT '0',
  `action_read` int(3) DEFAULT '0',
  `action_update` int(3) DEFAULT '0',
  `action_delete` int(3) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` int(11) DEFAULT '0',
  `category` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket` int(11) NOT NULL DEFAULT '0',
  `comment` text,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` varchar(50) DEFAULT NULL,
  `changelog` text,
  `access` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_resolutions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticketid` int(11) DEFAULT NULL,
  `tagid` int(11) DEFAULT NULL,
  `strength` tinyint(3) DEFAULT '0',
  `taggerid` int(11) DEFAULT '0',
  `taggedon` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) DEFAULT '0',
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `login` varchar(200) DEFAULT NULL,
  `severity` varchar(30) DEFAULT NULL,
  `owner` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `summary` varchar(250) DEFAULT NULL,
  `report` text,
  `resolved` varchar(50) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `os` varchar(50) DEFAULT NULL,
  `browser` varchar(50) DEFAULT NULL,
  `ip` varchar(200) DEFAULT NULL,
  `hostname` varchar(200) DEFAULT NULL,
  `uas` varchar(250) DEFAULT NULL,
  `referrer` varchar(250) DEFAULT NULL,
  `cookies` tinyint(3) NOT NULL DEFAULT '0',
  `instances` int(11) NOT NULL DEFAULT '1',
  `section` int(11) NOT NULL DEFAULT '1',
  `type` tinyint(3) NOT NULL DEFAULT '0',
  `group` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(100) DEFAULT NULL,
  `raw_tag` varchar(100) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `description` text,
  `admin` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `description` (`description`),
  FULLTEXT KEY `#__tags_raw_tag_alias_description_ftidx` (`raw_tag`,`alias`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tags_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` int(11) DEFAULT '0',
  `tagid` int(11) DEFAULT '0',
  `priority` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tags_object` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `objectid` int(11) DEFAULT NULL,
  `tagid` int(11) DEFAULT NULL,
  `strength` tinyint(3) DEFAULT '0',
  `taggerid` int(11) DEFAULT '0',
  `taggedon` datetime DEFAULT '0000-00-00 00:00:00',
  `tbl` varchar(255) DEFAULT NULL,
  `label` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `#__tags_object_objectid_tbl_idx` (`objectid`,`tbl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tags_substitute` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL DEFAULT '0',
  `tag` varchar(100) DEFAULT NULL,
  `raw_tag` varchar(100) DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__templates_menu` (
  `template` varchar(255) NOT NULL DEFAULT '',
  `menuid` int(11) NOT NULL DEFAULT '0',
  `client_id` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`menuid`,`client_id`,`template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `toolname` varchar(64) NOT NULL DEFAULT '',
  `title` varchar(127) NOT NULL DEFAULT '',
  `version` varchar(15) DEFAULT NULL,
  `description` text,
  `fulltxt` text,
  `license` text,
  `toolaccess` varchar(15) DEFAULT NULL,
  `codeaccess` varchar(15) DEFAULT NULL,
  `wikiaccess` varchar(15) DEFAULT NULL,
  `published` tinyint(1) DEFAULT '0',
  `state` int(15) DEFAULT NULL,
  `priority` int(15) DEFAULT '3',
  `team` text,
  `registered` datetime DEFAULT NULL,
  `registered_by` varchar(31) DEFAULT NULL,
  `mw` varchar(31) DEFAULT NULL,
  `vnc_geometry` varchar(31) DEFAULT NULL,
  `ticketid` int(15) DEFAULT NULL,
  `state_changed` datetime DEFAULT '0000-00-00 00:00:00',
  `revision` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `toolname` (`toolname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_authors` (
  `toolname` varchar(50) NOT NULL DEFAULT '',
  `revision` int(15) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT '0',
  `version_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`toolname`,`revision`,`uid`,`version_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_groups` (
  `cn` varchar(255) NOT NULL DEFAULT '',
  `toolid` int(11) NOT NULL DEFAULT '0',
  `role` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cn`,`toolid`,`role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `text` text,
  `title` varchar(100) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_statusviews` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ticketid` varchar(15) NOT NULL DEFAULT '',
  `uid` varchar(31) NOT NULL DEFAULT '',
  `viewed` datetime DEFAULT '0000-00-00 00:00:00',
  `elapsed` int(11) DEFAULT '500000',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_version` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `toolname` varchar(64) NOT NULL DEFAULT '',
  `instance` varchar(31) NOT NULL DEFAULT '',
  `title` varchar(127) NOT NULL DEFAULT '',
  `description` text,
  `fulltxt` text,
  `version` varchar(15) DEFAULT NULL,
  `revision` int(11) DEFAULT NULL,
  `toolaccess` varchar(15) DEFAULT NULL,
  `codeaccess` varchar(15) DEFAULT NULL,
  `wikiaccess` varchar(15) DEFAULT NULL,
  `state` int(15) DEFAULT NULL,
  `released_by` varchar(31) DEFAULT NULL,
  `released` datetime DEFAULT NULL,
  `unpublished` datetime DEFAULT NULL,
  `exportControl` varchar(16) DEFAULT NULL,
  `license` text,
  `vnc_geometry` varchar(31) DEFAULT NULL,
  `vnc_depth` int(11) DEFAULT NULL,
  `vnc_timeout` int(11) DEFAULT NULL,
  `vnc_command` varchar(100) DEFAULT NULL,
  `mw` varchar(31) DEFAULT NULL,
  `toolid` int(11) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `toolname` (`toolname`,`instance`),
  KEY `instance` (`instance`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_version_alias` (
  `tool_version_id` int(11) NOT NULL,
  `alias` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_version_hostreq` (
  `tool_version_id` int(11) NOT NULL,
  `hostreq` varchar(255) NOT NULL,
  UNIQUE KEY `toolid` (`tool_version_id`,`hostreq`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_version_middleware` (
  `tool_version_id` int(11) NOT NULL,
  `middleware` varchar(255) NOT NULL,
  UNIQUE KEY `toolid` (`tool_version_id`,`middleware`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_version_tracperm` (
  `tool_version_id` int(11) NOT NULL,
  `tracperm` varchar(64) NOT NULL,
  UNIQUE KEY `toolid` (`tool_version_id`,`tracperm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__trac_group_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `trac_project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trac_action` (`group_id`,`action`,`trac_project_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__trac_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__trac_projects` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__trac_user_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `trac_project_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trac_action` (`user_id`,`action`,`trac_project_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__user_roles` (
  `user_id` int(11) NOT NULL,
  `role` varchar(20) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `#__user_roles_role_user_id_group_id_uidx` (`role`,`user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(150) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `usertype` varchar(25) NOT NULL DEFAULT '',
  `block` tinyint(4) NOT NULL DEFAULT '0',
  `sendEmail` tinyint(4) DEFAULT '0',
  `gid` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `registerDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activation` varchar(100) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usertype` (`usertype`),
  KEY `idx_name` (`name`),
  KEY `gid_block` (`gid`,`block`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_password` (
  `user_id` int(11) NOT NULL,
  `passhash` char(127) NOT NULL,
  `shadowExpire` int(11) DEFAULT NULL,
  `shadowFlag` int(11) DEFAULT NULL,
  `shadowInactive` int(11) DEFAULT NULL,
  `shadowLastChange` int(11) DEFAULT NULL,
  `shadowMax` int(11) DEFAULT NULL,
  `shadowMin` int(11) DEFAULT NULL,
  `shadowWarning` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_password_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `passhash` char(32) NOT NULL,
  `action` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `invalidated` datetime DEFAULT NULL,
  `invalidated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `balance` int(11) NOT NULL DEFAULT '0',
  `earnings` int(11) NOT NULL DEFAULT '0',
  `credit` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_points_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `points` int(11) DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `alias` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_points_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `category` varchar(50) NOT NULL DEFAULT '',
  `alias` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `unitprice` float(6,2) DEFAULT '0.00',
  `pointsprice` int(11) DEFAULT '0',
  `currency` varchar(50) DEFAULT 'points',
  `maxunits` int(11) DEFAULT '0',
  `minunits` int(11) DEFAULT '0',
  `unitsize` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `restricted` int(11) DEFAULT '0',
  `ordering` int(11) DEFAULT '0',
  `params` text,
  `unitmeasure` varchar(200) NOT NULL DEFAULT '',
  `changed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_points_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `serviceid` int(11) NOT NULL DEFAULT '0',
  `units` int(11) NOT NULL DEFAULT '1',
  `status` int(11) NOT NULL DEFAULT '0',
  `pendingunits` int(11) DEFAULT '0',
  `pendingpayment` float(6,2) DEFAULT '0.00',
  `totalpaid` float(6,2) DEFAULT '0.00',
  `installment` int(11) DEFAULT '0',
  `contact` varchar(20) DEFAULT '',
  `code` varchar(10) DEFAULT '',
  `usepoints` tinyint(2) DEFAULT '0',
  `notes` text,
  `added` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_tracperms` (
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `type` varchar(20) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `category` varchar(50) DEFAULT NULL,
  `referenceid` int(11) DEFAULT '0',
  `amount` int(11) DEFAULT '0',
  `balance` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `#__users_transactions_referenceid_categroy_type_idx` (`referenceid`,`category`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__vote_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referenceid` int(11) NOT NULL DEFAULT '0',
  `voted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `voter` int(11) DEFAULT NULL,
  `helpful` varchar(11) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `#__vote_log_referenceid_idx` (`referenceid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__weblinks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL DEFAULT '0',
  `sid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(250) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(250) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `approved` tinyint(1) NOT NULL DEFAULT '1',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `catid` (`catid`,`published`,`archived`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageid` int(11) DEFAULT '0',
  `filename` varchar(255) DEFAULT NULL,
  `description` tinytext,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageid` int(11) NOT NULL DEFAULT '0',
  `version` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `ctext` text,
  `chtml` text,
  `rating` tinyint(1) NOT NULL DEFAULT '0',
  `anonymous` tinyint(1) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid` int(11) DEFAULT '0',
  `action` varchar(50) DEFAULT NULL,
  `comments` text,
  `actorid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_math` (
  `inputhash` varchar(32) NOT NULL DEFAULT '',
  `outputhash` varchar(32) NOT NULL DEFAULT '',
  `conservativeness` tinyint(4) NOT NULL,
  `html` text,
  `mathml` text,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inputhash` (`inputhash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pagename` varchar(100) DEFAULT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `times_rated` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `scope` varchar(255) NOT NULL,
  `params` tinytext,
  `ranking` float DEFAULT '0',
  `authors` varchar(255) DEFAULT NULL,
  `access` tinyint(2) DEFAULT '0',
  `group_cn` varchar(255) DEFAULT NULL,
  `state` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_page_author` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0',
  `page_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_page_metrics` (
  `pageid` int(11) NOT NULL DEFAULT '0',
  `pagename` varchar(100) DEFAULT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `visitors` int(11) NOT NULL DEFAULT '0',
  `visits` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pageid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageid` int(11) NOT NULL DEFAULT '0',
  `version` int(11) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `minor_edit` int(1) NOT NULL DEFAULT '0',
  `pagetext` text,
  `pagehtml` text,
  `approved` int(1) NOT NULL DEFAULT '0',
  `summary` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `#__wiki_version_pageid_idx` (`pageid`),
  FULLTEXT KEY `pagetext` (`pagetext`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wish_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wish` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `referenceid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(150) NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` int(3) NOT NULL DEFAULT '0',
  `public` int(3) NOT NULL DEFAULT '1',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wishlist_implementation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wishid` int(11) NOT NULL DEFAULT '0',
  `version` int(11) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `minor_edit` int(1) NOT NULL DEFAULT '0',
  `pagetext` text,
  `pagehtml` text,
  `approved` int(1) NOT NULL DEFAULT '0',
  `summary` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `pagetext` (`pagetext`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wishlist_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wishlist` int(11) DEFAULT '0',
  `subject` varchar(200) NOT NULL,
  `about` text,
  `proposed_by` int(11) DEFAULT '0',
  `granted_by` int(11) DEFAULT '0',
  `assigned` int(11) DEFAULT '0',
  `granted_vid` int(11) DEFAULT '0',
  `proposed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `granted` datetime DEFAULT '0000-00-00 00:00:00',
  `status` int(3) NOT NULL DEFAULT '0',
  `due` datetime DEFAULT '0000-00-00 00:00:00',
  `anonymous` int(3) DEFAULT '0',
  `ranking` int(11) DEFAULT '0',
  `points` int(11) DEFAULT '0',
  `private` int(3) DEFAULT '0',
  `accepted` int(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `#__wishlist_item_wishlist_idx` (`wishlist`),
  FULLTEXT KEY `#__wishlist_item_subject_about_ftidx` (`subject`,`about`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wishlist_ownergroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wishlist` int(11) DEFAULT '0',
  `groupid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wishlist_owners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wishlist` int(11) DEFAULT '0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `type` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wishlist_vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wishid` int(11) DEFAULT '0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `voted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `importance` int(3) DEFAULT '0',
  `effort` int(3) DEFAULT '0',
  `due` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `#__wishlist_vote_wishid_idx` (`wishid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xdomain_users` (
  `domain_id` int(11) NOT NULL,
  `domain_username` varchar(150) NOT NULL DEFAULT '',
  `uidNumber` int(11) DEFAULT NULL,
  PRIMARY KEY (`domain_id`,`domain_username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xdomains` (
  `domain_id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`domain_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xfavorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT '0',
  `oid` int(11) DEFAULT '0',
  `tbl` varchar(250) DEFAULT NULL,
  `faved` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups` (
  `gidNumber` int(11) NOT NULL AUTO_INCREMENT,
  `cn` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `published` tinyint(3) DEFAULT '0',
  `type` tinyint(3) DEFAULT '0',
  `access` tinyint(3) DEFAULT '0',
  `public_desc` text,
  `private_desc` text,
  `restrict_msg` text,
  `join_policy` tinyint(3) DEFAULT '0',
  `privacy` tinyint(3) DEFAULT '0',
  `discussion_email_autosubscribe` tinyint(3) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `overview_type` int(11) DEFAULT NULL,
  `overview_content` text,
  `plugins` text,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`gidNumber`),
  FULLTEXT KEY `#__xgroups_cn_description_public_desc_ftidx` (`cn`,`description`,`public_desc`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_applicants` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_events` (
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

CREATE TABLE `#__xgroups_inviteemails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `gidNumber` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_invitees` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid` int(11) DEFAULT '0',
  `action` varchar(50) DEFAULT NULL,
  `comments` text,
  `actorid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_managers` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_member_roles` (
  `role` int(11) DEFAULT NULL,
  `uidNumber` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_memberoption` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `optionname` varchar(100) DEFAULT NULL,
  `optionvalue` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_members` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` varchar(100) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `content` text,
  `porder` int(11) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `privacy` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_pages_hits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uidNumber` int(11) NOT NULL,
  `gidNumber` int(11) NOT NULL,
  `reason` text,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `role` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_tracperm` (
  `group_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  UNIQUE KEY `id` (`group_id`,`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xmessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) DEFAULT '0',
  `message` mediumtext,
  `subject` varchar(250) DEFAULT NULL,
  `component` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `group_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xmessage_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(20) NOT NULL DEFAULT '',
  `element` int(11) unsigned NOT NULL DEFAULT '0',
  `description` mediumtext,
  KEY `id` (`id`),
  KEY `class` (`class`),
  KEY `element` (`element`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xmessage_component` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component` varchar(50) NOT NULL DEFAULT '',
  `action` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xmessage_notify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT '0',
  `method` varchar(250) DEFAULT NULL,
  `type` varchar(250) DEFAULT NULL,
  `priority` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xmessage_recipient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `expires` datetime DEFAULT '0000-00-00 00:00:00',
  `actionid` int(11) DEFAULT '0',
  `state` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xmessage_seen` (
  `mid` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `whenseen` datetime DEFAULT '0000-00-00 00:00:00',
  KEY `mid` (`mid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xorganization_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(150) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xorganizations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xpoll_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pollid` int(4) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pollid` (`pollid`,`text`(1))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xpoll_date` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vote_id` int(11) NOT NULL DEFAULT '0',
  `poll_id` int(11) NOT NULL DEFAULT '0',
  `voter_ip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xpoll_menu` (
  `pollid` int(11) NOT NULL DEFAULT '0',
  `menuid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pollid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xpolls` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL DEFAULT '',
  `voters` int(9) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `access` int(11) NOT NULL DEFAULT '0',
  `lag` int(11) NOT NULL DEFAULT '0',
  `open` tinyint(1) NOT NULL DEFAULT '0',
  `opened` date DEFAULT NULL,
  `closed` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles` (
  `uidNumber` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(150) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `registerDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `gidNumber` varchar(11) NOT NULL DEFAULT '',
  `homeDirectory` varchar(255) NOT NULL DEFAULT '',
  `loginShell` varchar(255) NOT NULL DEFAULT '',
  `ftpShell` varchar(255) NOT NULL DEFAULT '',
  `userPassword` varchar(255) NOT NULL DEFAULT '',
  `gid` varchar(255) NOT NULL DEFAULT '',
  `orgtype` varchar(255) NOT NULL DEFAULT '',
  `organization` varchar(255) NOT NULL DEFAULT '',
  `countryresident` char(2) NOT NULL DEFAULT '',
  `countryorigin` char(2) NOT NULL DEFAULT '',
  `gender` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `reason` text NOT NULL,
  `mailPreferenceOption` int(11) NOT NULL DEFAULT '0',
  `usageAgreement` int(11) NOT NULL DEFAULT '0',
  `jobsAllowed` int(11) NOT NULL DEFAULT '0',
  `modifiedDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `emailConfirmed` int(11) NOT NULL DEFAULT '0',
  `regIP` varchar(255) NOT NULL DEFAULT '',
  `regHost` varchar(255) NOT NULL DEFAULT '',
  `nativeTribe` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(255) NOT NULL DEFAULT '',
  `proxyPassword` varchar(255) NOT NULL DEFAULT '',
  `proxyUidNumber` varchar(255) NOT NULL DEFAULT '',
  `givenName` varchar(255) NOT NULL DEFAULT '',
  `middleName` varchar(255) NOT NULL DEFAULT '',
  `surname` varchar(255) NOT NULL DEFAULT '',
  `picture` varchar(255) NOT NULL DEFAULT '',
  `vip` int(11) NOT NULL DEFAULT '0',
  `public` tinyint(2) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `note` text NOT NULL,
  `shadowExpire` int(11) DEFAULT NULL,
  `locked` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uidNumber`),
  KEY `username` (`username`),
  FULLTEXT KEY `author` (`givenName`,`surname`),
  FULLTEXT KEY `#__xprofiles_name_ftidx` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_admin` (
  `uidNumber` int(11) NOT NULL,
  `admin` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`admin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_bio` (
  `uidNumber` int(11) NOT NULL,
  `bio` text,
  PRIMARY KEY (`uidNumber`),
  FULLTEXT KEY `#__xprofiles_bio_bio_ftidx` (`bio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_disability` (
  `uidNumber` int(11) NOT NULL,
  `disability` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`disability`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_edulevel` (
  `uidNumber` int(11) NOT NULL,
  `edulevel` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`edulevel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_hispanic` (
  `uidNumber` int(11) NOT NULL,
  `hispanic` varchar(255) NOT NULL,
  PRIMARY KEY (`uidNumber`,`hispanic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_host` (
  `uidNumber` int(11) NOT NULL,
  `host` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`host`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_manager` (
  `uidNumber` int(11) NOT NULL,
  `manager` varchar(255) NOT NULL,
  PRIMARY KEY (`uidNumber`,`manager`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_race` (
  `uidNumber` int(11) NOT NULL,
  `race` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`race`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_role` (
  `uidNumber` int(11) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uidNumber` int(11) DEFAULT NULL,
  `tagid` int(11) DEFAULT NULL,
  `taggerid` int(11) DEFAULT '0',
  `taggedon` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xsession` (
  `session_id` varchar(200) NOT NULL DEFAULT '0',
  `ip` varchar(15) DEFAULT NULL,
  `host` varchar(128) DEFAULT NULL,
  `domain` varchar(128) DEFAULT NULL,
  `signed` tinyint(3) DEFAULT '0',
  `countrySHORT` char(2) DEFAULT NULL,
  `countryLONG` varchar(64) DEFAULT NULL,
  `ipREGION` varchar(128) DEFAULT NULL,
  `ipCITY` varchar(128) DEFAULT NULL,
  `ipLATITUDE` double DEFAULT NULL,
  `ipLONGITUDE` double DEFAULT NULL,
  `bot` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`session_id`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__ysearch_plugin_weights` (
  `plugin` varchar(20) NOT NULL,
  `weight` float NOT NULL,
  PRIMARY KEY (`plugin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__ysearch_site_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `link` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `#__ysearch_site_map_title_description_ftidx` (`title`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `orgtypes` (
  `name` varchar(64) NOT NULL,
  `orgtype` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`orgtype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `session` (
  `sessnum` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '',
  `remoteip` varchar(40) NOT NULL DEFAULT '',
  `exechost` varchar(40) NOT NULL DEFAULT '',
  `dispnum` int(10) unsigned DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `accesstime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeout` int(11) DEFAULT '86400',
  `appname` varchar(80) NOT NULL DEFAULT '',
  `sessname` varchar(100) NOT NULL DEFAULT '',
  `sesstoken` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`sessnum`),
  UNIQUE KEY `sessnum` (`sessnum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `sessionlog` (
  `sessnum` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '',
  `remoteip` varchar(40) NOT NULL DEFAULT '',
  `remotehost` varchar(40) NOT NULL DEFAULT '',
  `exechost` varchar(40) NOT NULL DEFAULT '',
  `dispnum` int(10) unsigned DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `appname` varchar(80) NOT NULL DEFAULT '',
  `walltime` double unsigned DEFAULT '0',
  `viewtime` double unsigned DEFAULT '0',
  `cputime` double unsigned DEFAULT '0',
  `status` smallint(5) unsigned DEFAULT '0',
  PRIMARY KEY (`sessnum`),
  UNIQUE KEY `sessnum` (`sessnum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `sessionpriv` (
  `privid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `privilege` varchar(40) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `privid` (`privid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `summary_andmore` (
  `id` tinyint(4) NOT NULL DEFAULT '0',
  `label` varchar(255) NOT NULL DEFAULT 'no_name',
  `plot` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `summary_andmore_vals` (
  `colid` tinyint(4) NOT NULL DEFAULT '0',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT '1',
  `rowid` tinyint(4) NOT NULL DEFAULT '0',
  `valfmt` tinyint(4) NOT NULL DEFAULT '0',
  `value` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `summary_misc` (
  `id` tinyint(4) NOT NULL DEFAULT '0',
  `label` varchar(255) NOT NULL DEFAULT 'no_name',
  `plot` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `summary_misc_vals` (
  `colid` tinyint(4) NOT NULL DEFAULT '0',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT '1',
  `rowid` tinyint(4) NOT NULL DEFAULT '0',
  `valfmt` tinyint(4) NOT NULL DEFAULT '0',
  `value` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `summary_simusage` (
  `id` tinyint(4) NOT NULL DEFAULT '0',
  `label` varchar(255) NOT NULL DEFAULT 'no_name',
  `plot` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `summary_simusage_vals` (
  `colid` tinyint(4) NOT NULL DEFAULT '0',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT '1',
  `rowid` tinyint(4) NOT NULL DEFAULT '0',
  `valfmt` tinyint(4) NOT NULL DEFAULT '0',
  `value` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `summary_user` (
  `id` tinyint(4) NOT NULL DEFAULT '0',
  `label` varchar(255) NOT NULL DEFAULT 'no_name',
  `plot` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `user_map` (
  `countryLONG` varchar(64) NOT NULL,
  `countrySHORT` char(2) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000',
  `ipCITY` varchar(128) NOT NULL,
  `ipLAT` double DEFAULT NULL,
  `ipLONG` double DEFAULT NULL,
  `ipREGION` varchar(128) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ip`),
  KEY `ip` (`ip`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `view` (
  `viewid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `username` varchar(32) NOT NULL DEFAULT '',
  `remoteip` varchar(40) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `heartbeat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `viewid` (`viewid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `viewlog` (
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `username` varchar(32) NOT NULL DEFAULT '',
  `remoteip` varchar(40) NOT NULL DEFAULT '',
  `remotehost` varchar(40) NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `duration` float unsigned DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `viewperm` (
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `viewuser` varchar(32) NOT NULL DEFAULT '',
  `viewtoken` varchar(32) NOT NULL DEFAULT '',
  `geometry` varchar(9) NOT NULL DEFAULT '0',
  `fwhost` varchar(40) NOT NULL DEFAULT '',
  `fwport` smallint(5) unsigned NOT NULL DEFAULT '0',
  `vncpass` varchar(16) NOT NULL DEFAULT '',
  `readonly` varchar(4) NOT NULL DEFAULT 'Yes',
  PRIMARY KEY (`sessnum`,`viewuser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE ALGORITHM = UNDEFINED DEFINER = CURRENT_USER SQL SECURITY DEFINER VIEW `#__resource_contributors_view` AS
    select `m`.`uidNumber` AS `uidNumber`, count(`AA`.`authorid`) AS `count`
    from ((`#__xprofiles` `m`
        left join `#__author_assoc` `AA` ON (((`AA`.`authorid` = `m`.`uidNumber`)
            and (`AA`.`subtable` = _utf8'resources'))))
        join `#__resources` `R` ON (((`R`.`id` = `AA`.`subid`)
            and (`R`.`published` = 1)
            and (`R`.`standalone` = 1))))
    where (`m`.`public` = 1) group by `m`.`uidNumber`;

CREATE ALGORITHM = UNDEFINED DEFINER = CURRENT_USER SQL SECURITY DEFINER VIEW `#__wiki_contributors_view` AS
    select `m`.`uidNumber` AS `uidNumber`, count(`w`.`id`) AS `count`
    from (`#__xprofiles` `m`
        left join `#__wiki_page` `w` ON (((`w`.`access` <> 1)
            and ((`w`.`created_by` = `m`.`uidNumber`)
            or ((`m`.`username` <> _utf8'')
            and (`w`.`authors` like concat(_utf8'%', `m`.`username`, _utf8'%')))))))
    where ((`m`.`public` = 1) and (`w`.`id` is not null)) group by `m`.`uidNumber`;

CREATE ALGORITHM = UNDEFINED DEFINER = CURRENT_USER SQL SECURITY DEFINER VIEW `#__contributor_ids_view` AS
    select `#__resource_contributors_view`.`uidNumber` AS `uidNumber`
    from `#__resource_contributors_view` 
    union select `#__wiki_contributors_view`.`uidNumber` AS `uidNumber`
    from`#__wiki_contributors_view`;

CREATE ALGORITHM = UNDEFINED DEFINER = CURRENT_USER SQL SECURITY DEFINER VIEW `#__contributors_view` AS
    select `c`.`uidNumber` AS `uidNumber`,
        coalesce(`r`.`count`, 0) AS `resource_count`,
        coalesce(`w`.`count`, 0) AS `wiki_count`,
        (coalesce(`w`.`count`, 0) + coalesce(`r`.`count`, 0)) AS `total_count`
    from ((`#__contributor_ids_view` `c`
        left join `#__resource_contributors_view` `r` ON ((`r`.`uidNumber` = `c`.`uidNumber`)))
        left join `#__wiki_contributors_view` `w` ON ((`w`.`uidNumber` = `c`.`uidNumber`)));

INSERT INTO `#__components` VALUES (1, 'Banners', '', 0, 0, '', 'Banner Management', 'com_banners', 0, 'js/ThemeOffice/component.png', 0, 'track_impressions=0\ntrack_clicks=0\ntag_prefix=\n\n', 1);
INSERT INTO `#__components` VALUES (2, 'Banners', '', 0, 1, 'option=com_banners', 'Active Banners', 'com_banners', 1, 'js/ThemeOffice/edit.png', 0, '', 1);
INSERT INTO `#__components` VALUES (3, 'Clients', '', 0, 1, 'option=com_banners&c=client', 'Manage Clients', 'com_banners', 2, 'js/ThemeOffice/categories.png', 0, '', 1);
INSERT INTO `#__components` VALUES (4, 'Web Links', 'option=com_weblinks', 0, 0, '', 'Manage Weblinks', 'com_weblinks', 0, 'js/ThemeOffice/component.png', 0, 'show_comp_description=1\ncomp_description=\nshow_link_hits=1\nshow_link_description=1\nshow_other_cats=1\nshow_headings=1\nshow_page_title=1\nlink_target=0\nlink_icons=\n\n', 1);
INSERT INTO `#__components` VALUES (5, 'Links', '', 0, 4, 'option=com_weblinks', 'View existing weblinks', 'com_weblinks', 1, 'js/ThemeOffice/edit.png', 0, '', 1);
INSERT INTO `#__components` VALUES (6, 'Categories', '', 0, 4, 'option=com_categories&section=com_weblinks', 'Manage weblink categories', '', 2, 'js/ThemeOffice/categories.png', 0, '', 1);
INSERT INTO `#__components` VALUES (7, 'Contacts', 'option=com_contact', 0, 0, '', 'Edit contact details', 'com_contact', 0, 'js/ThemeOffice/component.png', 1, 'contact_icons=0\nicon_address=\nicon_email=\nicon_telephone=\nicon_fax=\nicon_misc=\nshow_headings=1\nshow_position=1\nshow_email=0\nshow_telephone=1\nshow_mobile=1\nshow_fax=1\nbannedEmail=\nbannedSubject=\nbannedText=\nsession=1\ncustomReply=0\n\n', 1);
INSERT INTO `#__components` VALUES (8, 'Contacts', '', 0, 7, 'option=com_contact', 'Edit contact details', 'com_contact', 0, 'js/ThemeOffice/edit.png', 1, '', 1);
INSERT INTO `#__components` VALUES (9, 'Categories', '', 0, 7, 'option=com_categories&section=com_contact_details', 'Manage contact categories', '', 2, 'js/ThemeOffice/categories.png', 1, 'contact_icons=0\nicon_address=\nicon_email=\nicon_telephone=\nicon_fax=\nicon_misc=\nshow_headings=1\nshow_position=1\nshow_email=0\nshow_telephone=1\nshow_mobile=1\nshow_fax=1\nbannedEmail=\nbannedSubject=\nbannedText=\nsession=1\ncustomReply=0\n\n', 1);
INSERT INTO `#__components` VALUES (10, 'Polls', 'option=com_poll', 0, 0, 'option=com_poll', 'Manage Polls', 'com_poll', 0, 'js/ThemeOffice/component.png', 0, '', 1);
INSERT INTO `#__components` VALUES (11, 'News Feeds', 'option=com_newsfeeds', 0, 0, '', 'News Feeds Management', 'com_newsfeeds', 0, 'js/ThemeOffice/component.png', 0, '', 1);
INSERT INTO `#__components` VALUES (12, 'Feeds', '', 0, 11, 'option=com_newsfeeds', 'Manage News Feeds', 'com_newsfeeds', 1, 'js/ThemeOffice/edit.png', 0, 'show_headings=1\nshow_name=1\nshow_articles=1\nshow_link=1\nshow_cat_description=1\nshow_cat_items=1\nshow_feed_image=1\nshow_feed_description=1\nshow_item_description=1\nfeed_word_count=0\n\n', 1);
INSERT INTO `#__components` VALUES (13, 'Categories', '', 0, 11, 'option=com_categories&section=com_newsfeeds', 'Manage Categories', '', 2, 'js/ThemeOffice/categories.png', 0, '', 1);
INSERT INTO `#__components` VALUES (14, 'User', 'option=com_user', 0, 0, '', '', 'com_user', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (15, 'Search', 'option=com_search', 0, 0, 'option=com_search', 'Search Statistics', 'com_search', 0, 'js/ThemeOffice/component.png', 1, 'enabled=0\n\n', 1);
INSERT INTO `#__components` VALUES (16, 'Categories', '', 0, 1, 'option=com_categories&section=com_banner', 'Categories', '', 3, '', 1, '', 1);
INSERT INTO `#__components` VALUES (17, 'Wrapper', 'option=com_wrapper', 0, 0, '', 'Wrapper', 'com_wrapper', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (18, 'Mail To', '', 0, 0, '', '', 'com_mailto', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (19, 'Media Manager', '', 0, 0, 'option=com_media', 'Media Manager', 'com_media', 0, '', 1, 'upload_extensions=bmp,csv,doc,epg,gif,ico,jpg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls,BMP,CSV,DOC,EPG,GIF,ICO,JPG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS\nupload_maxsize=10000000\nfile_path=images\nimage_path=images/stories\nrestrict_uploads=1\ncheck_mime=1\nimage_extensions=bmp,gif,jpg,png\nignore_extensions=\nupload_mime=image/jpeg,image/gif,image/png,image/bmp,application/x-shockwave-flash,application/msword,application/excel,application/pdf,application/powerpoint,text/plain,application/x-zip\nupload_mime_illegal=text/html', 1);
INSERT INTO `#__components` VALUES (20, 'Articles', 'option=com_content', 0, 0, '', '', 'com_content', 0, '', 1, 'show_noauth=0\nshow_title=1\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=1\nshow_create_date=1\nshow_modify_date=1\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=1\nshow_pdf_icon=1\nshow_print_icon=1\nshow_email_icon=1\nshow_hits=1\nfeed_summary=0\n\n', 1);
INSERT INTO `#__components` VALUES (21, 'Configuration Manager', '', 0, 0, '', 'Configuration', 'com_config', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (22, 'Installation Manager', '', 0, 0, '', 'Installer', 'com_installer', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (23, 'Language Manager', '', 0, 0, '', 'Languages', 'com_languages', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (24, 'Mass mail', '', 0, 0, '', 'Mass Mail', 'com_massmail', 0, '', 1, 'mailSubjectPrefix=\nmailBodySuffix=\n\n', 1);
INSERT INTO `#__components` VALUES (25, 'Menu Editor', '', 0, 0, '', 'Menu Editor', 'com_menus', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (27, 'Messaging', '', 0, 0, '', 'Messages', 'com_messages', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (28, 'Modules Manager', '', 0, 0, '', 'Modules', 'com_modules', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (29, 'Plugin Manager', '', 0, 0, '', 'Plugins', 'com_plugins', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (30, 'Template Manager', '', 0, 0, '', 'Templates', 'com_templates', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (31, 'User Manager', '', 0, 0, '', 'Users', 'com_users', 0, '', 1, 'allowUserRegistration=1\nnew_usertype=Registered\nuseractivation=1\nfrontend_userparams=1\n\n', 1);
INSERT INTO `#__components` VALUES (32, 'Cache Manager', '', 0, 0, '', 'Cache', 'com_cache', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (33, 'Control Panel', '', 0, 0, '', 'Control Panel', 'com_cpanel', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (34,'Answers','option=com_answers',0,0,'option=com_answers','Answers','com_answers',0,'js/ThemeOffice/component.png',0,'infolink=/kb/points\nnotify_users=\n\n',1);
INSERT INTO `#__components` VALUES (102,'Contribute','option=com_contribute',0,0,'','','com_contribute',0,'js/Themeoffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (38,'Events','option=com_events',0,0,'option=com_events','Events','com_events',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (39,'Manage Events','',0,38,'option=com_events','Manage Events','com_events',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (40,'Manage Events Categories','',0,38,'option=com_events&task=cats','Manage Events Categories','com_events',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (41,'Edit Config','',0,38,'option=com_events&task=configure','Edit Config','com_events',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (42,'Groups','option=com_groups',0,0,'option=com_groups','Groups','com_groups',0,'js/ThemeOffice/component.png',0,'uploadpath=/site/groups\niconpath=/components/com_groups/assets/img/icons\njoin_policy=0\nprivacy=0\nauto_approve=1\ndisplay_system_users=no\nemail_comment_processing=1\nemail_member_groupsidcussionemail_autosignup=0\nintro_mygroups=1\nintro_interestinggroups=1\nintro_populargroups=1\n\n',1);
INSERT INTO `#__components` VALUES (35,'Topics','option=com_topics',0,0,'','','com_topics',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (36,'Usage','option=com_usage',0,0,'option=com_usage','Usage','com_usage',0,'js/ThemeOffice/component.png',0,'statsDBDriver=mysql\nstatsDBHost=localhost\nstatsDBPort=\nstatsDBUsername=\nstatsDBPassword=\nstatsDBDatabase=\nstatsDBPrefix=\nmapsApiKey=\nstats_path=/site/stats\nmaps_path=/site/stats/maps\nplots_path=/site/stats/plots\ncharts_path=/site/stats/plots\n\n',1);
INSERT INTO `#__components` VALUES (37,'Citations','option=com_citations',0,0,'option=com_citations','Citations','com_citations',0,'js/ThemeOffice/component.png',0,'citation_label=number\ncitation_rollover=no\ncitation_sponsors=yes\ncitation_import=1\ncitation_bulk_import=1\ncitation_download=1\ncitation_batch_download=1\ncitation_download_exclude=\ncitation_coins=1\ncitation_openurl=1\ncitation_url=url\ncitation_custom_url=\ncitation_cited=0\ncitation_cited_single=\ncitation_cited_multiple=\ncitation_show_tags=no\ncitation_allow_tags=no\ncitation_show_badges=no\ncitation_allow_badges=no\ncitation_format=\n\n',1);
INSERT INTO `#__components` VALUES (48,'Feedback','option=com_feedback',0,0,'option=com_feedback','Feedback','com_feedback',0,'js/ThemeOffice/component.png',0,'defaultpic=/components/com_feedback/images/contributor.gif\nuploadpath=/site/quotes\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nblacklist=\nbadwords=viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, texas holdem, fisting\n\n',1);
INSERT INTO `#__components` VALUES (57,'Support','option=com_support',0,0,'option=com_support','Support','com_support',0,'js/ThemeOffice/component.png',0,'feed_summary=0\nseverities=critical,major,normal,minor,trivial\nwebpath=/site/tickets\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz\ngroup=\nemails={config.mailfrom}\nblacklist=\nbadwords=viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, texas holdem, fisting\nemail_processing=1\n\n',1);
INSERT INTO `#__components` VALUES (59,'Messages','',0,57,'option=com_support&controller=messages','Messages','com_support',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (60,'Resolutions','',0,57,'option=com_support&controller=resolutions','Resolutions','com_support',3,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (62,'Tickets','',0,57,'option=com_support&controller=tickets','Tickets','com_support',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (45,'WhatsNew','option=com_whatsnew',0,0,'','','com_whatsnew',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (46,'XPoll','option=com_xpoll',0,0,'option=com_xpoll','XPoll','com_xpoll',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (51,'Contribtool','option=com_contribtool',0,0,'option=com_contribtool','Contribtool','com_contribtool',0,'js/ThemeOffice/component.png',0,'contribtool_on=1\ncontribtool_redirect=/home\nadmingroup=apps\ndefault_mw=narwhal\ndefault_vnc=780x600\ndeveloper_site=Forge\nproject_path=/tools/\ninvokescript_dir=/apps/\nadminscript_dir=/apps/bin\naddreposcript_dir=/usr/bin\ndev_suffix=_dev\ngroup_prefix=app-\nsourcecodePath=/www/myhub/site/protected/source\nlearn_url=http://rappture.org/wiki/FAQ_UpDownloadSrc\nrappture_url=http://rappture.org\ndemo_url=\ndoi_service=\nusedoi=0\ndoi_prefix=\nnew_doi=0\ndoi_newservice=\ndoi_shoulder=\ndoi_newprefix=\ndoi_publisher=\ndoi_resolve=http://dx.doi.org/\ndoi_verify=http://n2t.net/ezid/id/\nexec_pu=1\nscreenshot_edit=1\ndownloadable_on=0\nauto_addrepo=1\n\n',1);
INSERT INTO `#__components` VALUES (52,'Knowledgebase','option=com_kb',0,0,'option=com_kb','Knowledgebase','com_kb',0,'js/ThemeOffice/component.png',0,'show_date=2\nallow_comments=1\nclose_comments=year\nfeeds_enabled=1\nfeed_entries=partial\n\n',1);
INSERT INTO `#__components` VALUES (67,'Resources','option=com_resources',0,0,'option=com_resources','Resources','com_resources',0,'js/ThemeOffice/component.png',0,'autoapprove=0\nautoapproved_users=nikki\ncc_license=1\ncc_license_custom=0\nemail_when_approved=0\ndefaultpic=/components/com_resources/images/resource_thumb.gif\ntagstool=screenshots,poweredby,bio,credits,citations,sponsoredby,references,publications\ntagsothr=bio,credits,citations,sponsoredby,references,publications\naccesses=Public,Registered,Special,Protected,Private\nwebpath=/site/resources\ntoolpath=/site/resources/tools\nuploadpath=/site/resources\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz\ndoi=\naboutdoi=\nsupportedtag=\nsupportedlink=\nbrowsetags=on\ngoogle_id=\nshow_authors=1\nshow_assocs=1\nshow_ranking=0\nshow_rating=1\nshow_date=3\nshow_metadata=1\nshow_citation=1\nshow_audience=0\naudiencelink=\n\n',1);
INSERT INTO `#__components` VALUES (68,'Types','',0,67,'option=com_resources&controller=types','Types','com_resources',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (69,'Orphans','',0,67,'option=com_resources&task=orphans','Orphans','com_resources',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (70,'Entries','',0,67,'option=com_resources&controller=items','Entries','com_resources',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (47,'Tags','option=com_tags',0,0,'option=com_tags','Tags','com_tags',0,'js/ThemeOffice/component.png',0,'focus_area_01=\nfocus_area_02=\nfocus_area_03=\nfocus_area_04=\nfocus_area_05=\nfocus_area_06=\nfocus_area_07=\nfocus_area_08=\nfocus_area_09=\nfocus_area_10=\n\n',1);
INSERT INTO `#__components` VALUES (77,'Hosts','option=com_tools&controller=hosts',0,75,'option=com_tools&controller=hosts','Hosts','com_tools',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (75,'Tools','option=com_tools',0,0,'option=com_tools','','com_tools',0,'js/ThemeOffice/component.png',0,'mw_on=1\nmw_redirect=/home\nstopRedirect=index.php?option=com_members&task=myaccount\nmwDBDriver=mysql\nmwDBHost=localhost\nmwDBPort=\nmwDBUsername=\nmwDBPassword=\nmwDBDatabase=\nmwDBPrefix=\nshareable=1\nwarn_multiples=0\nstoragehost=tcp://localhost:300\nshow_storage=1\ncontribtool_on=0\nadmingroup=apps\ndefault_mw=narwhal\ndefault_vnc=780x600\ndeveloper_site=nanoFORGE\nproject_path=/projects/app-\ninvokescript_dir=/apps\ndev_suffix=_dev\ngroup_prefix=app-\ndemo_url=\nusedoi=1\ndoi_service=http://dir1.lib.purdue.edu:8080/axis/services/CreateHandleService?wsdl\nexec_pu=1\nscreenshot_edit=0\n\n',1);
INSERT INTO `#__components` VALUES (80,'Members','option=com_members',0,0,'option=com_members','Members','com_members',0,'js/ThemeOffice/component.png',0,'privacy=1\nbankAccounts=0\ndefaultpic=/components/com_members/assets/img/profile.gif\nwebpath=/site/members\nhomedir=/home\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nuser_messaging=1\nemployeraccess=0\nshadowMax=120\nshadowMin=0\nshadowWarning=7\nhubHomeDir=/home/core\n\n',1);
INSERT INTO `#__components` VALUES (94,'Store','option=com_store',0,0,'option=com_store','Store','com_store',0,'js/ThemeOffice/component.png',0,'store_enabled=1\nwebpath=/site/store\nhubaddress_ln1=\nhubaddress_ln2=\nhubaddress_ln3=\nhubaddress_ln4=\nhubaddress_ln5=\nhubemail=\nhubphone=\nheadertext_ln1=\nheadertext_ln2=\nfootertext=\nreceipt_title=Your Order at HUB Store\nreceipt_note=Thank You for contributing to our HUB!\n\n',1);
INSERT INTO `#__components` VALUES (65,'Wishlists','option=com_wishlist',0,0,'option=com_wishlist','Wishlists','com_wishlist',0,'js/ThemeOffice/component.png',0,'categories=general, resource, group, user\ngroup=hubdev\nbanking=0\nallow_advisory=0\nvotesplit=0\nwebpath=/site/wishlist\nshow_percentage_granted=0\n\n',1);
INSERT INTO `#__components` VALUES (66,'Features','option=com_features',0,0,'','','com_features',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (58,'Stats','',0,57,'option=com_support&controller=stats','Stats','com_support',6,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (76,'Pipeline','option=com_tools&controller=pipeline',0,75,'option=com_tools&controller=pipeline','Pipeline','com_tools',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (74,'Blog','option=com_blog',0,0,'option=com_blog','Blog','com_blog',0,'js/ThemeOffice/component.png',0,'title=\nuploadpath=/site/blog\ncleanintro=1\nintrolength=300\nshow_authors=1\nallow_comments=1\nfeeds_enabled=1\nfeed_entries=partial\nshow_date=3\n\n',1);
INSERT INTO `#__components` VALUES (61,'Tag/Group','',0,57,'option=com_support&controller=taggroups','Tag/Group','com_support',5,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (87,'Jobs','option=com_jobs',0,0,'option=com_jobs','Jobs','com_jobs',0,'js/ThemeOffice/component.png',0,'component_enabled=1\nindustry=\nadmingroup=\nspecialgroup=jobsadmin\nautoapprove=1\ndefaultsort=category\njobslimit=25\nallowsubscriptions=1\nusonly=0\nbanking=0\npromoline=For a limited time: FREE Employer Services Basic subscription\ninfolink=kb/jobs\npremium_infolink=\n\n',1);
INSERT INTO `#__components` VALUES (88,'Categories','',0,87,'option=com_jobs&controller=categories','Categores','com_jobs',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (89,'Types','',0,87,'option=com_jobs&controller=types','Types','com_jobs',3,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (90,'Jobs','',0,87,'option=com_jobs','Jobs','com_jobs',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (91,'Services','option=com_services',0,0,'option=com_services','Services & Subscriptions','com_services',0,'js/ThemeOffice/component.png',0,'autoapprove=1',1);
INSERT INTO `#__components` VALUES (92,'Services','option=com_services',0,91,'option=com_services&controller=services','Services','com_services',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (93,'Subscriptions','option=com_services',0,91,'option=com_services&controller=subscriptions','Subscriptions','com_services',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (43,'System','',0,42,'option=com_groups&controller=system','System','com_groups',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (44,'Manage','',0,42,'option=com_groups&controller=manage','Manage','com_groups',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (79,'Search','',0,0,'option=com_ysearch&task=configure','YSearch Management','com_ysearch',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (63,'ACL','',0,57,'option=com_support&controller=acl','ACL','com_support',7,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (53,'Categories','option=com_kb&controller=categories',0,52,'option=com_kb&controller=categories','Categories','com_kb',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (56,'Wiki','option=com_wiki',0,0,'option=com_wiki','Wiki','com_wiki',0,'js/ThemeOffice/component.png',0,'subpage_separator=/\nhomepage=MainPage\nmax_pagename_length=100\nfilepath=/site/wiki\nmathpath=/site/wiki/math\ntmppath=/site/wiki/tmp\nmaxAllowed=40000000\nimg_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz\n\n',1);
INSERT INTO `#__components` VALUES (64,'Abuse Reports','',0,57,'option=com_support&controller=abusereports','Abuse Reports','com_support',4,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (55,'Forum','option=com_forum',0,0,'','','com_forum',0,'',0,'',1);
INSERT INTO `#__components` VALUES (71,'Roles','',0,67,'option=com_resources&controller=roles','Roles','com_resources',3,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (72,'Licenses','',0,67,'option=com_resources&controller=licenses','Licenses','com_resources',4,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (73,'Plugins','',0,67,'option=com_resources&controller=plugins','Plugins','com_resources',5,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (97,'Register','option=com_register',0,0,'option=com_register','Register','com_register',0,'js/Themeoffice/component.png',0,'LoginReturn=\nConfirmationReturn=\npasswordMeter=1\nregistrationUsername=RRUU\nregistrationPassword=RRUU\nregistrationConfirmPassword=RRUU\nregistrationFullname=RRUU\nregistrationEmail=RRUU\nregistrationConfirmEmail=RRUU\nregistrationURL=HOHO\nregistrationPhone=HOHO\nregistrationEmployment=HOHO\nregistrationOrganization=HOHO\nregistrationCitizenship=HHHH\nregistrationResidency=HHHH\nregistrationSex=HHHH\nregistrationDisability=HHHH\nregistrationHispanic=HHHH\nregistrationRace=HHHH\nregistrationInterests=HOHO\nregistrationReason=HOHO\nregistrationOptIn=HOHO\nregistrationCAPTCHA=RHHH\nregistrationTOU=RHHH\n\n',1);
INSERT INTO `#__components` VALUES (78,'Host Types','option=com_tools&controller=hosttypes',0,75,'option=com_tools&controller=hosttypes','Host Types','com_tools',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (54,'Articles','option=com_kb&controller=articles',0,52,'option=com_kb&controller=articles','Articles','com_kb',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (81,'Members','option=com_members&controller=members',0,80,'option=com_members&controller=members','Members','com_members',1,'js/ThemeOffice/component.png',0,'ldapProfileMirror=1\ndefaultpic=/components/com_members/images/profile.gif\nwebpath=/site/members\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nuser_messaging=1\nprivacy=1\naccess_org=0\naccess_orgtype=0\naccess_email=2\naccess_url=0\naccess_phone=2\naccess_tags=0\naccess_bio=0\naccess_countryorigin=0\naccess_countryresident=0\naccess_gender=0\naccess_race=2\naccess_hispanic=2\naccess_disability=2\naccess_optin=2\nemployeraccess=0\n\n',1);
INSERT INTO `#__components` VALUES (82,'Messaging','option=com_members&controller=messages',0,80,'option=com_members&controller=messages','Messaging','com_members',2,'js/ThemeOffice/component.png',0,'ldapProfileMirror=1\ndefaultpic=/components/com_members/images/profile.gif\nwebpath=/site/members\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nuser_messaging=1\nprivacy=1\naccess_org=0\naccess_orgtype=0\naccess_email=2\naccess_url=0\naccess_phone=2\naccess_tags=0\naccess_bio=0\naccess_countryorigin=0\naccess_countryresident=0\naccess_gender=0\naccess_race=2\naccess_hispanic=2\naccess_disability=2\naccess_optin=2\nemployeraccess=0\n\n',1);
INSERT INTO `#__components` VALUES (83,'Points','option=com_members&controller=points',0,80,'option=com_members&controller=points','Points','com_members',3,'js/ThemeOffice/component.png',0,'ldapProfileMirror=1\ndefaultpic=/components/com_members/images/profile.gif\nwebpath=/site/members\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nuser_messaging=1\nprivacy=1\naccess_org=0\naccess_orgtype=0\naccess_email=2\naccess_url=0\naccess_phone=2\naccess_tags=0\naccess_bio=0\naccess_countryorigin=0\naccess_countryresident=0\naccess_gender=0\naccess_race=2\naccess_hispanic=2\naccess_disability=2\naccess_optin=2\nemployeraccess=0\n\n',1);
INSERT INTO `#__components` VALUES (84,'Plugins','option=com_members&controller=plugins',0,80,'option=com_members&controller=plugins','Plugins','com_members',4,'js/ThemeOffice/component.png',0,'ldapProfileMirror=1\ndefaultpic=/components/com_members/images/profile.gif\nwebpath=/site/members\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nuser_messaging=1\nprivacy=1\naccess_org=0\naccess_orgtype=0\naccess_email=2\naccess_url=0\naccess_phone=2\naccess_tags=0\naccess_bio=0\naccess_countryorigin=0\naccess_countryresident=0\naccess_gender=0\naccess_race=2\naccess_hispanic=2\naccess_disability=2\naccess_optin=2\nemployeraccess=0\n\n',1);
INSERT INTO `#__components` VALUES (49,'Submitted Quotes','option=com_feedback&type=submitted',0,48,'option=com_feedback&type=submitted','Submitted Quotes','com_feedback',1,'js/ThemeOffice/component.png',0,'defaultpic=/components/com_feedback/images/contributor.gif\nuploadpath=/site/quotes\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nblacklist=\nbadwords=viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, texas holdem, fisting\n\n',1);
INSERT INTO `#__components` VALUES (50,'Selected Quotes','option=com_feedback&type=selected',0,48,'option=com_feedback&type=selected','Selected Quotes','com_feedback',2,'js/ThemeOffice/component.png',0,'defaultpic=/components/com_feedback/images/contributor.gif\nuploadpath=/site/quotes\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nblacklist=\nbadwords=viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, texas holdem, fisting\n\n',1);
INSERT INTO `#__components` VALUES (98,'Config','option=com_register&controller=config',0,97,'option=com_register&controller=config','Config','com_register',1,'js/Themeoffice/component.png',0,'registrationUsername=RRUU\nregistrationPassword=RRUU\nregistrationConfirmPassword=RRUU\nregistrationFullname=RRUU\nregistrationEmail=RRUU\nregistrationConfirmEmail=RRUU\nregistrationURL=HOHO\nregistrationPhone=HOHO\nregistrationEmployment=HOHO\nregistrationOrganization=HOHO\nregistrationCitizenship=HHHH\nregistrationResidency=HHHH\nregistrationSex=HHHH\nregistrationDisability=HHHH\nregistrationHispanic=HHHH\nregistrationRace=HHHH\nregistrationInterests=HOHO\nregistrationReason=HOHO\nregistrationOptIn=HOHO\nregistrationTOU=RHHH',1);
INSERT INTO `#__components` VALUES (99,'Organizations','option=com_register&controller=organizations',0,97,'option=com_register&controller=organizations','Organizations','com_register',2,'js/Themeoffice/component.png',0,'registrationUsername=RRUU\nregistrationPassword=RRUU\nregistrationConfirmPassword=RRUU\nregistrationFullname=RRUU\nregistrationEmail=RRUU\nregistrationConfirmEmail=RRUU\nregistrationURL=HOHO\nregistrationPhone=HOHO\nregistrationEmployment=HOHO\nregistrationOrganization=HOHO\nregistrationCitizenship=HHHH\nregistrationResidency=HHHH\nregistrationSex=HHHH\nregistrationDisability=HHHH\nregistrationHispanic=HHHH\nregistrationRace=HHHH\nregistrationInterests=HOHO\nregistrationReason=HOHO\nregistrationOptIn=HOHO\nregistrationTOU=RHHH',1);
INSERT INTO `#__components` VALUES (100,'Employer Types','option=com_register&controller=employers',0,97,'option=com_register&controller=employers','Employer Types','com_register',3,'js/Themeoffice/component.png',0,'registrationUsername=RRUU\nregistrationPassword=RRUU\nregistrationConfirmPassword=RRUU\nregistrationFullname=RRUU\nregistrationEmail=RRUU\nregistrationConfirmEmail=RRUU\nregistrationURL=HOHO\nregistrationPhone=HOHO\nregistrationEmployment=HOHO\nregistrationOrganization=HOHO\nregistrationCitizenship=HHHH\nregistrationResidency=HHHH\nregistrationSex=HHHH\nregistrationDisability=HHHH\nregistrationHispanic=HHHH\nregistrationRace=HHHH\nregistrationInterests=HOHO\nregistrationReason=HOHO\nregistrationOptIn=HOHO\nregistrationTOU=RHHH',1);
INSERT INTO `#__components` VALUES (101,'Incremental','option=com_register&controller=incrememntal',0,97,'option=com_register&controller=incrememntal','Incremental','com_register',4,'js/Themeoffice/component.png',0,'registrationUsername=RRUU\nregistrationPassword=RRUU\nregistrationConfirmPassword=RRUU\nregistrationFullname=RRUU\nregistrationEmail=RRUU\nregistrationConfirmEmail=RRUU\nregistrationURL=HOHO\nregistrationPhone=HOHO\nregistrationEmployment=HOHO\nregistrationOrganization=HOHO\nregistrationCitizenship=HHHH\nregistrationResidency=HHHH\nregistrationSex=HHHH\nregistrationDisability=HHHH\nregistrationHispanic=HHHH\nregistrationRace=HHHH\nregistrationInterests=HOHO\nregistrationReason=HOHO\nregistrationOptIn=HOHO\nregistrationTOU=RHHH',1);
INSERT INTO `#__components` VALUES (95,'Orders','option=com_store&controller=orders',0,94,'option=com_store&controller=orders','Orders','com_store',1,'js/ThemeOffice/component.png',0,'store_enabled=1\nwebpath=/site/store\nhubaddress_ln1=\nhubaddress_ln2=\nhubaddress_ln3=\nhubaddress_ln4=\nhubaddress_ln5=\nhubemail=\nhubphone=\nheadertext_ln1=\nheadertext_ln2=\nfootertext=\nreceipt_title=Your Order at HUB Store\nreceipt_note=Thank You for contributing to our HUB!\n\n',1);
INSERT INTO `#__components` VALUES (96,'Items','option=com_store&controller=items',0,94,'option=com_store&controller=items','Items','com_store',2,'js/ThemeOffice/component.png',0,'store_enabled=1\nwebpath=/site/store\nhubaddress_ln1=\nhubaddress_ln2=\nhubaddress_ln3=\nhubaddress_ln4=\nhubaddress_ln5=\nhubemail=\nhubphone=\nheadertext_ln1=\nheadertext_ln2=\nfootertext=\nreceipt_title=Your Order at HUB Store\nreceipt_note=Thank You for contributing to our HUB!\n\n',1);
INSERT INTO `#__components` VALUES (85,'Projects','option=com_projects',0,0,'option=com_projects','Projects','com_projects',0,'../components/com_hub/images/hubzero-component.png',0,'grantinfo=1\nconfirm_step=1\nedit_settings=1\nrestricted_data=2\napprove_restricted=0\nprivacylink=/legal/privacy\nHIPAAlink=/legal/privacy\nFERPAlink=/legal/privacy\ncreatorgroup=\nadmingroup=projectsadmin\nsdata_group=hipaa_reviewers\nginfo_group=sps_reviewers\nmin_name_length=5\nmax_name_length=25\nreserved_names=clone, temp, test, view, edit, setup, start, deleteimg, intro, features, verify, register, autocomplete, showcount, edit, suspend, reinstate, review, analytics, reports, about, feedback, share, authorize\nwebpath=/srv/projects\noffroot=1\ngitpath=/usr/bin/git\ngitclone=/site/projects/clone/.git\nmaxUpload=10000000\ndefaultQuota=.5\npremiumQuota=1\napproachingQuota=90\npubQuota=1\npremiumPubQuota=20\nimagepath=/site/projects\ndefaultpic=/components/com_projects/assets/img/project.png\nimg_maxAllowed=40000000\nimg_file_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nmessaging=1\nprivacy=1\nlimit=25\nsidebox_limit=3\ngroup_prefix=pr-\nuse_alias=1\ndocumentation=/kb/projects\n\n',1);
INSERT INTO `#__components` VALUES (86,'System','option=com_system',0,0,'option=com_system','System','com_system',0,'',0,'geodb_driver=mysql\ngeodb_host=localhost\ngeodb_port=\ngeodb_user=\ngeodb_password=\ngeodb_database=\ngeodb_prefix=\nldap_primary=ldap://127.0.0.1\nldap_secondary=\nldap_basedn=\nldap_searchdn=\nldap_searchpw=\nldap_managerdn=\nldap_managerpw=\nldap_tls=0\n\n',1);
INSERT INTO `#__components` VALUES (131,'Billboards','option=com_billboards',0,0,'option=com_billboards','Billboards','com_billboards',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (132,'Billboards','option=com_billboards&task=billboards',0,131,'option=com_billboards&task=billboards','Billboards','com_billboards',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (133,'Collections','option=com_billboards&task=collections',0,131,'option=com_billboards&task=collections','Collections','com_billboards',1,'js/ThemeOffice/component.png',0,'',1);

INSERT INTO `#__groups` VALUES (0, 'Public');
INSERT INTO `#__groups` VALUES (1, 'Registered');
INSERT INTO `#__groups` VALUES (2, 'Special');

INSERT INTO `#__plugins` VALUES (1,'Authentication - Joomla','joomla','authentication',0,6,0,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (2,'Authentication - LDAP','ldap','authentication',0,7,0,1,0,0,'0000-00-00 00:00:00','host=\nport=389\nuse_ldapV3=0\nnegotiate_tls=0\nno_referrals=0\nauth_method=bind\nbase_dn=\nsearch_string=\nusers_dn=\nusername=\npassword=\nldap_fullname=fullName\nldap_email=mail\nldap_uid=uid\n\n');
INSERT INTO `#__plugins` VALUES (3,'Authentication - GMail','gmail','authentication',0,9,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (4,'Authentication - OpenID','openid','authentication',0,8,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (5,'User - Joomla!','joomla','user',0,0,1,0,0,0,'0000-00-00 00:00:00','autoregister=1\n\n');
INSERT INTO `#__plugins` VALUES (6,'Search - Content','content','search',0,1,1,1,0,0,'0000-00-00 00:00:00','search_limit=50\nsearch_content=1\nsearch_uncategorised=1\nsearch_archived=1\n\n');
INSERT INTO `#__plugins` VALUES (7,'Search - Contacts','contacts','search',0,3,1,1,0,0,'0000-00-00 00:00:00','search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (8,'Search - Categories','categories','search',0,4,1,0,0,0,'0000-00-00 00:00:00','search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (9,'Search - Sections','sections','search',0,5,1,0,0,0,'0000-00-00 00:00:00','search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (10,'Search - Newsfeeds','newsfeeds','search',0,6,1,0,0,0,'0000-00-00 00:00:00','search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (11,'Search - Weblinks','weblinks','search',0,2,1,1,0,0,'0000-00-00 00:00:00','search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (12,'Content - Pagebreak','pagebreak','content',0,10000,1,1,0,0,'0000-00-00 00:00:00','enabled=1\ntitle=1\nmultipage_toc=1\nshowall=1\n\n');
INSERT INTO `#__plugins` VALUES (13,'Content - Rating','vote','content',0,4,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (14,'Content - Email Cloaking','emailcloak','content',0,5,1,0,0,0,'0000-00-00 00:00:00','mode=1\n\n');
INSERT INTO `#__plugins` VALUES (15,'Content - Code Hightlighter (GeSHi)','geshi','content',0,5,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (16,'Content - Load Module','loadmodule','content',0,6,1,0,0,0,'0000-00-00 00:00:00','enabled=1\nstyle=0\n\n');
INSERT INTO `#__plugins` VALUES (17,'Content - Page Navigation','pagenavigation','content',0,2,1,1,0,0,'0000-00-00 00:00:00','position=1\n\n');
INSERT INTO `#__plugins` VALUES (18,'Editor - No Editor','none','editors',0,0,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (19,'Editor - TinyMCE','tinymce','editors',0,0,1,1,0,0,'0000-00-00 00:00:00','mode=advanced\nskin=0\ncompressed=0\ncleanup_startup=0\ncleanup_save=2\nentity_encoding=raw\nlang_mode=0\nlang_code=en\ntext_direction=ltr\ncontent_css=1\ncontent_css_custom=\nrelative_urls=1\nnewlines=0\ninvalid_elements=applet\nextended_elements=\ntoolbar=top\ntoolbar_align=left\nhtml_height=550\nhtml_width=750\nelement_path=1\nfonts=1\npaste=1\nsearchreplace=1\ninsertdate=1\nformat_date=%Y-%m-%d\ninserttime=1\nformat_time=%H:%M:%S\ncolors=1\ntable=1\nsmilies=1\nmedia=1\nhr=1\ndirectionality=1\nfullscreen=1\nstyle=1\nlayer=1\nxhtmlxtras=1\nvisualchars=1\nnonbreaking=1\ntemplate=0\nadvimage=1\nadvlink=1\nautosave=1\ncontextmenu=1\ninlinepopups=1\nsafari=1\ncustom_plugin=\ncustom_button=\n\n');
INSERT INTO `#__plugins` VALUES (20,'Editor - XStandard Lite 2.0','xstandard','editors',0,0,0,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (21,'Editor Button - Image','image','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (22,'Editor Button - Pagebreak','pagebreak','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (23,'Editor Button - Readmore','readmore','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (24,'XML-RPC - Joomla','joomla','xmlrpc',0,7,0,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (25,'XML-RPC - Blogger API','blogger','xmlrpc',0,7,0,1,0,0,'0000-00-00 00:00:00','catid=1\nsectionid=0\n\n');
INSERT INTO `#__plugins` VALUES (26,'XML-RPC - MetaWeblog API','metaweblog','xmlrpc',0,7,0,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (27,'System - SEF','sef','system',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (28,'System - Debug','debug','system',0,2,1,0,0,0,'0000-00-00 00:00:00','queries=1\nmemory=1\nlangauge=1\n\n');
INSERT INTO `#__plugins` VALUES (29,'System - Legacy','legacy','system',0,3,0,1,0,0,'0000-00-00 00:00:00','route=0\n\n');
INSERT INTO `#__plugins` VALUES (30,'System - Cache','cache','system',0,4,0,1,0,0,'0000-00-00 00:00:00','browsercache=0\ncachetime=15\n\n');
INSERT INTO `#__plugins` VALUES (31,'System - Log','log','system',0,5,0,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (32,'System - Remember Me','remember','system',0,6,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (33,'System - Backlink','backlink','system',0,7,0,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (34,'System - Mootools Upgrade','mtupgrade','system',0,8,0,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (35,'Content - xHubTags','xhubtags','content',0,7,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (36,'Groups - Forum','forum','groups',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (37,'Groups - Resources','resources','groups',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (38,'Groups - Members','members','groups',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (39,'Groups - Wiki','wiki','groups',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (40,'Members - Messages','messages','members',0,11,1,0,0,0,'0000-00-00 00:00:00','default_method=email\n\n');
INSERT INTO `#__plugins` VALUES (41,'Members - Usage','usage','members',0,9,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (42,'Members - Contributions - Topics','topics','members',0,8,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (43,'Members - Contributions - Resources','resources','members',0,7,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (44,'Members - Groups','groups','members',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (45,'Members - Favorites','favorites','members',0,10,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (46,'Members - Points','points','members',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (47,'Members - Contributions','contributions','members',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (48,'Middleware - About','resource','mw',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (49,'Middleware - Questions','questions','mw',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (50,'Tags - Groups','groups','tags',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (51,'Tags - Support','support','tags',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (52,'Tags - Topics','topics','tags',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (53,'Tags - Answers','answers','tags',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (54,'Tags - Events','events','tags',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (55,'Tags - Members','members','tags',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (56,'Tags - Resources','resources','tags',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (57,'Resources - Share','share','resources',0,8,1,0,0,0,'0000-00-00 00:00:00','icons_limit=3\nshare_facebook=1\nshare_twitter=1\nshare_google=1\nshare_digg=1\nshare_technorati=1\nshare_delicious=1\nshare_reddit=0\nshare_email=0\nshare_print=0\n\n');
INSERT INTO `#__plugins` VALUES (58,'Resources - Favorite','favorite','resources',0,7,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (59,'Resources - Versions','versions','resources',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (60,'Resources - Reviews','reviews','resources',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (61,'Resources - Questions','questions','resources',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (62,'Resources - Wishlist','wishlist','resources',0,9,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (63,'Resources - Usage','usage','resources',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (64,'Resources - Related','related','resources',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (65,'Resources - Recommendations','recommendations','resources',0,2,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (66,'Resources - Citations','citations','resources',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (67,'Support - Comments','comments','support',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (68,'Support - Transfer','transfer','support',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (69,'Support - Wishlist','wishlist','support',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (70,'Support - Resources','resources','support',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (71,'Support - Answers','answers','support',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (72,'System - HUBzero','hubzero','system',0,8,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (73,'System - xFeed','xfeed','system',0,9,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (74,'Tag Editor - Auto complete','autocompleter','tageditor',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (75,'Usage - Region','region','usage',0,7,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (76,'Usage - Overview','overview','usage',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (77,'Usage - Chart','chart','usage',0,4,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (78,'Usage - Partners','partners','usage',0,2,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (79,'Usage - Domain Class','domainclass','usage',0,0,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (80,'Usage - Domains','domains','usage',0,5,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (81,'Usage - Tools','tools','usage',0,3,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (82,'Usage - Maps','maps','usage',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (83,'User - xHUB','xusers','user',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (84,'Whatsnew - Topics','topics','whatsnew',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (85,'Whatsnew - Resources','resources','whatsnew',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (86,'Whatsnew - Content','content','whatsnew',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (87,'Whatsnew - Events','events','whatsnew',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (88,'Whatsnew - Knowledge Base','kb','whatsnew',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (167,'Groups - User Group Enrollments','userenrollment','groups',0,12,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (91,'XMessage - RSS','rss','xmessage',0,4,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (92,'XMessage - Internal','internal','xmessage',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (93,'XMessage - SMS TXT','smstxt','xmessage',0,3,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (94,'XMessage - Instant Message','im','xmessage',0,2,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (95,'XMessage - Handler','handler','xmessage',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (96,'XMessage - Email','email','xmessage',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (175,'Projects - Todo','todo','projects',0,7,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (173,'User - LDAP','ldap','user',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (172,'Authentication - Linkedin','linkedin','authentication',0,4,1,0,0,0,'0000-00-00 00:00:00','api_key=6ctnex5mlf2l\napp_secret=XLRQe6rTg0q1vnw0\n\n');
INSERT INTO `#__plugins` VALUES (105,'Groups - Wishlist','wishlist','groups',0,8,1,0,0,0,'0000-00-00 00:00:00','limit=50');
INSERT INTO `#__plugins` VALUES (106,'Resource - Supporting Documents','supportingdocs','resources',0,11,1,0,0,0,'0000-00-00 00:00:00','display_limit=50');
INSERT INTO `#__plugins` VALUES (107,'Members - Resume','resume','members',0,12,1,0,0,0,'0000-00-00 00:00:00','limit=50');
INSERT INTO `#__plugins` VALUES (108,'Members - Usage Extended','usages','members',0,13,0,0,0,0,'0000-00-00 00:00:00','groups=usage_admin');
INSERT INTO `#__plugins` VALUES (109,'Members - Blog','blog','members',0,14,1,0,0,0,'0000-00-00 00:00:00','uploadpath=/site/members/{{uid}}/blog\nfeeds_enabled=0\nfeed_entries=partial');
INSERT INTO `#__plugins` VALUES (110,'Tags - Blogs','blogs','tags',0,9,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (171,'Authentication - Google','google','authentication',0,3,1,0,0,0,'0000-00-00 00:00:00','app_id=88386892289.apps.googleusercontent.com\napp_secret=j4WI8Hhg7hkEXaTdrlUknXQp\n\n');
INSERT INTO `#__plugins` VALUES (112,'Support - Blog','blog','support',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (113,'YSearch - Content','content','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (114,'YSearch - Increase weight of items with terms matching in their titles','weighttitle','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (115,'YSearch - Events','events','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (116,'YSearch - Knowledge Base','kb','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (117,'YSearch - Groups','groups','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (118,'YSearch - Members','members','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (119,'YSearch - Resources','resources','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (120,'YSearch - Topics','topics','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (121,'YSearch - Increase weight of items with contributors matching terms','weightcontributor','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (122,'YSearch - Wishlists','wishlists','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (123,'YSearch - Questions and Answers','questions','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (124,'YSearch - Increase relevance for tool results','weighttools','ysearch',0,0,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (125,'YSearch - Site Map','sitemap','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (126,'YSearch - Terms - Suffix Expansion','suffixes','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (127,'YSearch - Sort courses by date','sortcourses','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (128,'Groups - Blog','blog','groups',0,7,1,0,0,0,'0000-00-00 00:00:00','uploadpath=/site/groups/{{gid}}/blog\nposting=0\nfeeds_enabled=0\nfeed_entries=partial');
INSERT INTO `#__plugins` VALUES (130,'Groups - Usage','usage','groups',0,9,0,0,0,0,'0000-00-00 00:00:00','uploadpath=/site/groups/{{gid}}/blog\nposting=0\nfeeds_enabled=0\nfeed_entries=partial');
INSERT INTO `#__plugins` VALUES (131,'Groups - Messages','messages','groups',0,2,1,0,0,0,'0000-00-00 00:00:00','limit=50');
INSERT INTO `#__plugins` VALUES (133,'HUBzero - Wiki Parser','wikiparser','hubzero',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (134,'YSearch - Sort events by date','sortevents','ysearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (135,'HUBzero - Autocompleter','autocompleter','hubzero',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (136,'HUBzero - Wiki Editor Toolbar','wikieditortoolbar','hubzero',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (137,'Members - HTML Snippet','snippet','members',0,15,1,0,0,0,'0000-00-00 00:00:00','uploadpath=/site/members/{{uid}}/blog\nfeeds_enabled=0\nfeed_entries=partial');
INSERT INTO `#__plugins` VALUES (138,'Groups - Calendar','calendar','groups',0,10,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (174,'Projects - Notes','notes','projects',0,8,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (140,'YSearch - Documentation','documentation','ysearch',0,0,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (170,'Members - Account','account','members',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (169,'Authentication - Facebook','facebook','authentication',0,2,1,0,0,0,'0000-00-00 00:00:00','app_id=141761505963838\napp_secret=93b141e62fa6929cbf4eb3c167effab3\n\n');
INSERT INTO `#__plugins` VALUES (168,'Groups - User Group Enrollments','userenrollment','groups',0,13,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (149,'Resource - Usage New','usagenew','resources',0,12,1,0,0,0,'0000-00-00 00:00:00','period=14\nchart_path=/site/usage/chart_resources/\nmap_path=/site/usage/resource_maps/\ngroups=admin_test_group');
INSERT INTO `#__plugins` VALUES (150,'Middleware - ParticleVE','particleve','mw',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (151,'Support - CAPTCHA','captcha','support',0,7,1,0,0,0,'0000-00-00 00:00:00','modCaptcha=text\ncomCaptcha=image\nbgColor=#2c8007\ntextColor=#ffffff\nimageFunction=Adv\n');
INSERT INTO `#__plugins` VALUES (152,'System - Disable Cache','disablecache','system',0,9,1,0,0,0,'0000-00-00 00:00:00','definitions=/about/contact\nreenable_afterdispatch=0\n\n');
INSERT INTO `#__plugins` VALUES (153,'HUBzero - Image CAPTCHA','imagecaptcha','hubzero',0,4,1,0,0,0,'0000-00-00 00:00:00','bgColor=#2c8007\ntextColor=#ffffff\nimageFunction=Adv\n');
INSERT INTO `#__plugins` VALUES (154,'HUBzero - Math CAPTCHA','mathcaptcha','hubzero',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (155,'Groups - Member Options','memberoptions','groups',0,11,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (156,'Resources - About','about','resources',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (157,'Resources - About (tool)','abouttool','resources',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (158,'Support - KB Comments','kb','support',0,8,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (159,'Members - Dashboard','dashboard','members',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (160,'Members - Profile','profile','members',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (161,'Citation - Endnote','endnote','citation',0,0,1,0,0,0,'0000-00-00 00:00:00','custom_tags=badges-%=\\ntags-%<\ntitle_match_percent=85%\n\n');
INSERT INTO `#__plugins` VALUES (162,'Citation - Bibtex','bibtex','citation',0,0,1,0,0,0,'0000-00-00 00:00:00','title_match_percent=90%\n\n');
INSERT INTO `#__plugins` VALUES (163,'Citation - Default','default','citation',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (164,'System - JQuery','jquery','system',0,9,1,1,0,0,'0000-00-00 00:00:00','jquery=1\njqueryVersion=1.7.2\njquerycdnpath=//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js\njqueryui=1\njqueryuiVersion=1.8.6\njqueryuicdnpath=//ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js\njqueryuicss=0\njqueryuicsspath=/plugins/system/jquery/css/jquery-ui-1.8.6.custom.css\njquerytools=1\njquerytoolsVersion=1.2.5\njquerytoolscdnpath=http://cdn.jquerytools.org/1.2.5/all/jquery.tools.min.js\njqueryfb=1\njqueryfbVersion=2.0.4\njqueryfbcdnpath=//fancyapps.com/fancybox/\njqueryfbcss=0\njqueryfbcsspath=/plugins/system/jquery/css/jquery-fancybox-2.0.4.css\nactivateSite=1\nnoconflictSite=0\nactivateAdmin=0\nnoconflictAdmin=0\n\n');
INSERT INTO `#__plugins` VALUES (166,'Authentication - HUBzero','hubzero','authentication',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (176,'Projects - Blog','blog','projects',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (177,'Projects - Files','files','projects',0,3,1,0,0,0,'0000-00-00 00:00:00','display_limit=50\nmaxUpload=104857600\nmaxDownload=1048576\ntempPath=/site/projects/temp\n\n');
INSERT INTO `#__plugins` VALUES (178,'Projects - Team','team','projects',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (179,'Members - Projects','projects','members',0,17,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (180,'Groups - Projects','projects','groups',0,9,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (181,'Authentication - Pucas','pucas','authentication',0,5,1,0,0,0,'0000-00-00 00:00:00','domain=Purdue Career Account (CAS)\ndisplay_name=Purdue Career\n\n');

INSERT INTO `#__modules` VALUES (1, 'Main Menu', '', 1, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 1, 'menutype=mainmenu\nmoduleclass_sfx=_menu\n', 1, 0, '');
INSERT INTO `#__modules` VALUES (2, 'Login', '', 1, 'login', 0, '0000-00-00 00:00:00', 1, 'mod_login', 0, 0, 1, '', 1, 1, '');
INSERT INTO `#__modules` VALUES (3, 'Popular','',3,'cpanel',0,'0000-00-00 00:00:00',1,'mod_popular',0,2,1,'',0, 1, '');
INSERT INTO `#__modules` VALUES (4, 'Recent added Articles','',4,'cpanel',0,'0000-00-00 00:00:00',1,'mod_latest',0,2,1,'ordering=c_dsc\nuser_id=0\ncache=0\n\n',0, 1, '');
INSERT INTO `#__modules` VALUES (5, 'Menu Stats','',5,'cpanel',0,'0000-00-00 00:00:00',1,'mod_stats',0,2,1,'',0, 1, '');
INSERT INTO `#__modules` VALUES (6, 'Unread Messages','',1,'header',0,'0000-00-00 00:00:00',1,'mod_unread',0,2,1,'',1, 1, '');
INSERT INTO `#__modules` VALUES (7, 'Online Users','',2,'header',0,'0000-00-00 00:00:00',1,'mod_online',0,2,1,'',1, 1, '');
INSERT INTO `#__modules` VALUES (8, 'Toolbar','',1,'toolbar',0,'0000-00-00 00:00:00',1,'mod_toolbar',0,2,1,'',1, 1, '');
INSERT INTO `#__modules` VALUES (9, 'Quick Icons','',1,'icon',0,'0000-00-00 00:00:00',1,'mod_quickicon',0,2,1,'',1,1, '');
INSERT INTO `#__modules` VALUES (10, 'Logged in Users','',2,'cpanel',0,'0000-00-00 00:00:00',1,'mod_logged',0,2,1,'',0,1, '');
INSERT INTO `#__modules` VALUES (11, 'Footer', '', 0, 'footer', 0, '0000-00-00 00:00:00', 1, 'mod_footer', 0, 0, 1, '', 1, 1, '');
INSERT INTO `#__modules` VALUES (12, 'Admin Menu','', 1,'menu', 0,'0000-00-00 00:00:00', 1,'mod_menu', 0, 2, 1, '', 0, 1, '');
INSERT INTO `#__modules` VALUES (13, 'Admin SubMenu','', 1,'submenu', 0,'0000-00-00 00:00:00', 1,'mod_submenu', 0, 2, 1, '', 0, 1, '');
INSERT INTO `#__modules` VALUES (14, 'User Status','', 1,'status', 0,'0000-00-00 00:00:00', 1,'mod_status', 0, 2, 1, '', 0, 1, '');
INSERT INTO `#__modules` VALUES (15, 'Title','', 1,'title', 0,'0000-00-00 00:00:00', 1,'mod_title', 0, 2, 1, '', 0, 1, '');

INSERT INTO `#__templates_menu` VALUES ('hubbasic2012', '0', '0');
INSERT INTO `#__templates_menu` VALUES ('hubbasicadmin', '0', '1');

INSERT INTO `#__core_acl_aro_groups` VALUES (17,0,'ROOT',1,22,'ROOT');
INSERT INTO `#__core_acl_aro_groups` VALUES (28,17,'USERS',2,21,'USERS');
INSERT INTO `#__core_acl_aro_groups` VALUES (29,28,'Public Frontend',3,12,'Public Frontend');
INSERT INTO `#__core_acl_aro_groups` VALUES (18,29,'Registered',4,11,'Registered');
INSERT INTO `#__core_acl_aro_groups` VALUES (19,18,'Author',5,10,'Author');
INSERT INTO `#__core_acl_aro_groups` VALUES (20,19,'Editor',6,9,'Editor');
INSERT INTO `#__core_acl_aro_groups` VALUES (21,20,'Publisher',7,8,'Publisher');
INSERT INTO `#__core_acl_aro_groups` VALUES (30,28,'Public Backend',13,20,'Public Backend');
INSERT INTO `#__core_acl_aro_groups` VALUES (23,30,'Manager',14,19,'Manager');
INSERT INTO `#__core_acl_aro_groups` VALUES (24,23,'Administrator',15,18,'Administrator');
INSERT INTO `#__core_acl_aro_groups` VALUES (25,24,'Super Administrator',16,17,'Super Administrator');

INSERT INTO `#__core_acl_aro_sections` VALUES (10,'users',1,'Users',0);

INSERT INTO `#__xmessage_component` VALUES (1,'com_support','support_reply_submitted','Someone replies to a support ticket I submitted.');
INSERT INTO `#__xmessage_component` VALUES (2,'com_support','support_reply_assigned','Someone replies to a support ticket I am assigned to.');
INSERT INTO `#__xmessage_component` VALUES (3,'com_support','support_close_submitted','Someone closes a support ticket I submitted.');
INSERT INTO `#__xmessage_component` VALUES (4,'com_answers','answers_reply_submitted','Someone answers a question I submitted.');
INSERT INTO `#__xmessage_component` VALUES (5,'com_answers','answers_reply_comment','Someone replies to a comment I posted.');
INSERT INTO `#__xmessage_component` VALUES (6,'com_answers','answers_question_deleted','Someone deletes a question I replied to.');
INSERT INTO `#__xmessage_component` VALUES (7,'com_groups','groups_requests_membership','Someone requests membership to a group I manage.');
INSERT INTO `#__xmessage_component` VALUES (8,'com_groups','groups_requests_status','Someone is approved/denied membership to a group I manage.');
INSERT INTO `#__xmessage_component` VALUES (9,'com_groups','groups_cancels_membership','Someone cancels membership to a group I manage.');
INSERT INTO `#__xmessage_component` VALUES (10,'com_groups','groups_promoted_demoted','Someone promotes/demotes a member of a group I manage.');
INSERT INTO `#__xmessage_component` VALUES (11,'com_groups','groups_approved_denied','My membership request to a group is approved or denied.');
INSERT INTO `#__xmessage_component` VALUES (12,'com_groups','groups_status_changed','My membership status changes');
INSERT INTO `#__xmessage_component` VALUES (13,'com_groups','groups_cancelled_me','My membership to a group is cancelled.');
INSERT INTO `#__xmessage_component` VALUES (14,'com_groups','groups_changed','Someone changes the settings of a group I am a member of.');
INSERT INTO `#__xmessage_component` VALUES (15,'com_groups','groups_deleted','Someone deletes a group I am a member of.');
INSERT INTO `#__xmessage_component` VALUES (16,'com_resources','resources_submission_approved','A contribution I submitted is approved.');
INSERT INTO `#__xmessage_component` VALUES (17,'com_resources','resources_new_comment','Someone adds a review/comment to one of my contributions.');
INSERT INTO `#__xmessage_component` VALUES (18,'com_store','store_notifications','Shipping and other notifications about my purchases.');
INSERT INTO `#__xmessage_component` VALUES (19,'com_wishlist','wishlist_new_wish','Someone posted a wish on the Wish List I control.');
INSERT INTO `#__xmessage_component` VALUES (20,'com_wishlist','wishlist_status_changed','A wish I submitted got accepted/rejected/granted.');
INSERT INTO `#__xmessage_component` VALUES (21,'com_support','support_item_transferred','A support ticket/wish/question I submitted got transferred.');
INSERT INTO `#__xmessage_component` VALUES (22,'com_wishlist','wishlist_comment_posted','Someone commented on a wish I posted or am assigned to');
INSERT INTO `#__xmessage_component` VALUES (23,'com_groups','groups_invite','When you are invited to join a group.');
INSERT INTO `#__xmessage_component` VALUES (24,'com_contribtool','contribtool_status_changed','Tool development status has changed');
INSERT INTO `#__xmessage_component` VALUES (25,'com_contribtool','contribtool_new_message','New contribtool message is received');
INSERT INTO `#__xmessage_component` VALUES (26,'com_contribtool','contribtool_info_changed','Information about a tool I develop has changed');
INSERT INTO `#__xmessage_component` VALUES (27,'com_wishlist','wishlist_comment_thread','Someone replied to my comment or followed me in a discussion');
INSERT INTO `#__xmessage_component` VALUES (28,'com_wishlist','wishlist_new_owner','You were added as an administrator of a Wish List');
INSERT INTO `#__xmessage_component` VALUES (29,'com_wishlist','wishlist_wish_assigned','A wish has been assigned to me');
INSERT INTO `#__xmessage_component` VALUES (30,'com_groups','group_message','Messages from fellow group members');
INSERT INTO `#__xmessage_component` VALUES (31,'com_members','member_message','Messages from fellow site members');
INSERT INTO `#__xmessage_component` VALUES (32,'com_projects','projects_member_added','You were added or invited to a project');
INSERT INTO `#__xmessage_component` VALUES (33,'com_projects','projects_new_project_admin','Receive notifications about project(s) you monitor as an admin or reviewer');
INSERT INTO `#__xmessage_component` VALUES (34,'com_projects','projects_admin_message','Receive administrative messages about your project(s)');

INSERT INTO `#__ysearch_plugin_weights` VALUES ('content',0.8);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('events',0.8);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('groups',0.8);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('kb',0.8);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('members',0.8);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('resources',0.8);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('topics',1);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('weighttitle',1);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('sortrelevance',1);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('sortnewer',0.2);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('tagmod',1.3);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('weightcontributor',0.2);
