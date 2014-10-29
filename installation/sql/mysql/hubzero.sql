#
# @package      hubzero-cms
# @file         installation/sql/mysql/hubzero.sql
# @author       Nicholas J. Kisseberth <nkissebe@purdue.edu>
# @copyright    Copyright (c) 2010-2013 Purdue University. All rights reserved.
# @license      http://www.gnu.org/licenses/gpl2.html GPLv2
#
# Copyright (c) 2010-2013 Purdue University
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

#
# Last Migration Applied: Migration20140925213032ComTools.php
#

SET NAMES 'utf8';
SET @@SESSION.sql_mode = '';

CREATE TABLE `app` (
  `appname` varchar(80) NOT NULL DEFAULT '',
  `geometry` varchar(9) NOT NULL DEFAULT '',
  `depth` smallint(5) unsigned NOT NULL DEFAULT '16',
  `hostreq` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userreq` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeout` int(10) unsigned NOT NULL DEFAULT '0',
  `command` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `display` (
  `hostname` varchar(40) NOT NULL DEFAULT '',
  `dispnum` int(10) unsigned DEFAULT '0',
  `geometry` varchar(9) NOT NULL DEFAULT '',
  `depth` smallint(5) unsigned NOT NULL DEFAULT '16',
  `sessnum` bigint(20) unsigned DEFAULT '0',
  `vncpass` varchar(16) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT '',
  KEY `idx_hostname` (`hostname`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `domainclass` (
  `class` tinyint(4) NOT NULL DEFAULT '0',
  `country` varchar(4) NOT NULL,
  `domain` varchar(64) NOT NULL,
  `name` tinytext NOT NULL,
  `state` varchar(4) NOT NULL,
  PRIMARY KEY (`domain`),
  KEY `idx_class` (`class`) USING BTREE,
  KEY `idx_domain_class` (`domain`,`class`) USING BTREE
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `domainclasses` (
  `class` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`class`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `fileperm` (
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fileuser` varchar(32) NOT NULL DEFAULT '',
  `fwhost` varchar(40) NOT NULL DEFAULT '',
  `fwport` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cookie` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`sessnum`,`fileuser`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `host` (
  `hostname` varchar(40) NOT NULL DEFAULT '',
  `provisions` bigint(20) unsigned NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT '',
  `uses` int(11) NOT NULL DEFAULT '0',
  `portbase` int(11) NOT NULL DEFAULT '0',
  `zone_id` int(11) DEFAULT NULL,
  `max_uses` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`hostname`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `hosttype` (
  `name` varchar(40) NOT NULL DEFAULT '',
  `value` bigint(20) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  `jobtoken` varchar(32) DEFAULT NULL,
  UNIQUE KEY `uidx_jobid` (`jobid`),
  KEY `idx_start` (`start`),
  KEY `idx_heartbeat` (`heartbeat`),
  KEY `idx_username_jobtoken` (`username`,`jobtoken`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  `zone_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sessnum`,`job`,`event`,`venue`),
  KEY `idx_sessnum` (`sessnum`),
  KEY `idx_event` (`event`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__abuse_reports` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(50) DEFAULT NULL,
  `referenceid` int(11) unsigned NOT NULL DEFAULT '0',
  `report` text NOT NULL,
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` tinyint(2) NOT NULL DEFAULT '0',
  `subject` varchar(150) DEFAULT NULL,
  `reviewed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reviewed_by` int(11) unsigned NOT NULL DEFAULT '0',
  `note` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_reviewed_by` (`reviewed_by`),
  KEY `idx_state` (`state`),
  KEY `idx_category_referenceid` (`category`,`referenceid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__announcements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(100) DEFAULT NULL,
  `scope_id` int(11) DEFAULT NULL,
  `content` text,
  `priority` tinyint(2) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(2) NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sticky` tinyint(2) NOT NULL DEFAULT '0',
  `email` tinyint(4) DEFAULT '0',
  `sent` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_scope_scope_id` (`scope`,`scope_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_state` (`state`),
  KEY `idx_priority` (`priority`),
  KEY `idx_sticky` (`sticky`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__answers_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `response_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `helpful` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_rid` (`response_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__answers_questions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(250) NOT NULL DEFAULT '',
  `question` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `anonymous` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `email` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `helpful` int(11) unsigned NOT NULL DEFAULT '0',
  `reward` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`),
  KEY `idx_created_by` (`created_by`),
  FULLTEXT KEY `ftidx_question` (`question`),
  FULLTEXT KEY `ftidx_subject` (`subject`),
  FULLTEXT KEY `ftidx_question_subject` (`question`,`subject`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__answers_questions_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(11) unsigned NOT NULL DEFAULT '0',
  `expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `voter` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_qid` (`question_id`),
  KEY `idx_voter` (`voter`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__answers_responses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(11) unsigned NOT NULL DEFAULT '0',
  `answer` text NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `helpful` int(11) unsigned NOT NULL DEFAULT '0',
  `nothelpful` int(11) unsigned NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `anonymous` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_qid` (`question_id`),
  KEY `idx_state` (`state`),
  KEY `idx_created_by` (`created_by`),
  FULLTEXT KEY `ftidx_answer` (`answer`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__auth_domain` (
  `authenticator` varchar(255) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `params` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__auth_link` (
  `auth_domain_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `params` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `linked_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__author_assoc` (
  `subtable` varchar(50) NOT NULL DEFAULT '',
  `subid` int(11) NOT NULL DEFAULT '0',
  `authorid` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`subtable`,`subid`,`authorid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__author_role_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__author_stats` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `authorid` int(11) NOT NULL,
  `tool_users` bigint(20) DEFAULT NULL,
  `andmore_users` bigint(20) DEFAULT NULL,
  `total_users` bigint(20) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__billboard_collection` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__blog_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) unsigned NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `anonymous` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `parent` int(11) unsigned NOT NULL DEFAULT '0',
  `state` tinyint(2) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_entry_id` (`entry_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_parent` (`parent`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__blog_entries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `state` tinyint(2) NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` tinytext NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `allow_comments` tinyint(2) NOT NULL DEFAULT '0',
  `scope` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_group_id` (`group_id`),
  KEY `idx_alias` (`alias`),
  FULLTEXT KEY `ftidx_title` (`title`),
  FULLTEXT KEY `ftidx_content` (`content`),
  FULLTEXT KEY `ftidx_title_content` (`title`,`content`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__cart` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `itemid` int(11) NOT NULL DEFAULT '0',
  `type` varchar(20) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `selections` text,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__cart_cart_items` (
  `crtId` int(16) NOT NULL,
  `sId` int(16) NOT NULL,
  `crtiQty` int(5) DEFAULT NULL,
  `crtiOldQty` int(5) DEFAULT NULL,
  `crtiPrice` decimal(10,2) DEFAULT NULL,
  `crtiOldPrice` decimal(10,2) DEFAULT NULL,
  `crtiName` varchar(255) DEFAULT NULL,
  `crtiAvailable` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`crtId`,`sId`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__cart_carts` (
  `crtId` int(16) NOT NULL AUTO_INCREMENT,
  `crtCreated` datetime DEFAULT NULL,
  `crtLastUpdated` datetime DEFAULT NULL,
  `uidNumber` int(16) DEFAULT NULL,
  PRIMARY KEY (`crtId`),
  UNIQUE KEY `uidx_uidNumber` (`uidNumber`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__cart_coupons` (
  `crtId` int(16) NOT NULL,
  `cnId` int(16) NOT NULL,
  `crtCnAdded` datetime DEFAULT NULL,
  `crtCnStatus` char(15) DEFAULT NULL
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__cart_memberships` (
  `crtmId` int(16) NOT NULL AUTO_INCREMENT,
  `pId` int(16) DEFAULT NULL,
  `crtId` int(16) DEFAULT NULL,
  `crtmExpires` datetime DEFAULT NULL,
  PRIMARY KEY (`crtmId`),
  UNIQUE KEY `uidx_pId_crtId` (`pId`,`crtId`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__cart_saved_addresses` (
  `saId` int(16) NOT NULL AUTO_INCREMENT,
  `uidNumber` int(16) NOT NULL,
  `saToFirst` char(100) NOT NULL,
  `saToLast` char(100) NOT NULL,
  `saAddress` char(255) NOT NULL,
  `saCity` char(25) NOT NULL,
  `saState` char(2) NOT NULL,
  `saZip` char(10) NOT NULL,
  PRIMARY KEY (`saId`),
  UNIQUE KEY `uidx_uidNumber_saToFirst_saToLast_saAddress_saZip` (`uidNumber`,`saToFirst`,`saToLast`,`saAddress`(100),`saZip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__cart_transaction_info` (
  `tId` int(16) NOT NULL,
  `tiShippingToFirst` char(100) DEFAULT NULL,
  `tiShippingToLast` char(100) DEFAULT NULL,
  `tiShippingAddress` char(255) DEFAULT NULL,
  `tiShippingCity` char(25) DEFAULT NULL,
  `tiShippingState` char(2) DEFAULT NULL,
  `tiShippingZip` char(10) DEFAULT NULL,
  `tiTotal` decimal(10,2) DEFAULT NULL,
  `tiSubtotal` decimal(10,2) DEFAULT NULL,
  `tiTax` decimal(10,2) DEFAULT NULL,
  `tiShipping` decimal(10,2) DEFAULT NULL,
  `tiShippingDiscount` decimal(10,2) DEFAULT NULL,
  `tiDiscounts` decimal(10,2) DEFAULT NULL,
  `tiItems` text,
  `tiPerks` text,
  `tiMeta` text,
  `tiCustomerStatus` char(15) DEFAULT 'unconfirmed',
  PRIMARY KEY (`tId`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__cart_transaction_items` (
  `tId` int(16) NOT NULL,
  `sId` int(16) NOT NULL,
  `tiQty` int(5) DEFAULT NULL,
  `tiPrice` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`tId`,`sId`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__cart_transaction_steps` (
  `tsId` int(16) NOT NULL AUTO_INCREMENT,
  `tId` int(16) NOT NULL,
  `tsStep` char(16) NOT NULL,
  `tsStatus` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`tsId`),
  UNIQUE KEY `uidx_tId_tsStep` (`tId`,`tsStep`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__cart_transactions` (
  `tId` int(16) NOT NULL AUTO_INCREMENT,
  `crtId` int(16) DEFAULT NULL,
  `tCreated` datetime DEFAULT NULL,
  `tLastUpdated` datetime DEFAULT NULL,
  `tStatus` char(32) DEFAULT NULL,
  PRIMARY KEY (`tId`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__citations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `published` int(3) NOT NULL DEFAULT '1',
  `affiliated` int(11) NOT NULL DEFAULT '0',
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
  `url` varchar(250) DEFAULT NULL,
  `volume` varchar(11) DEFAULT NULL,
  `year` varchar(4) DEFAULT NULL,
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
  `language` varchar(100) DEFAULT NULL,
  `accession_number` varchar(100) DEFAULT NULL,
  `short_title` varchar(250) DEFAULT NULL,
  `author_address` text,
  `keywords` text,
  `abstract` text,
  `call_number` varchar(100) DEFAULT NULL,
  `label` varchar(100) DEFAULT NULL,
  `research_notes` text,
  `params` text,
  `formatted` text,
  `format` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `ftidx_title_isbn_doi_abstract` (`title`,`isbn`,`doi`,`abstract`),
  FULLTEXT KEY `ftidx_title_isbn_doi_abstract_author_publisher` (`title`,`isbn`,`doi`,`abstract`,`author`,`publisher`),
  FULLTEXT KEY `ftidx_search` (`title`,`isbn`,`doi`,`abstract`,`author`,`publisher`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__citations_assoc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT '0',
  `oid` int(11) DEFAULT '0',
  `type` varchar(50) DEFAULT NULL,
  `tbl` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  UNIQUE KEY `uidx_cid_author_authorid_uidNumber` (`cid`,`author`,`authorid`,`uidNumber`),
  KEY `idx_authorid` (`authorid`),
  KEY `idx_uidNumber` (`uidNumber`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__citations_format` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `typeid` int(11) DEFAULT NULL,
  `style` varchar(50) DEFAULT NULL,
  `format` text,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__citations_secondary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `sec_cits_cnt` int(11) DEFAULT NULL,
  `search_string` tinytext,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__citations_sponsors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sponsor` varchar(150) DEFAULT NULL,
  `link` varchar(200) DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__citations_sponsors_assoc` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT NULL,
  `sid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__citations_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `type_title` varchar(255) DEFAULT NULL,
  `type_desc` text,
  `type_export` varchar(255) DEFAULT NULL,
  `fields` text,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__collections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL,
  `object_id` int(11) NOT NULL DEFAULT '0',
  `object_type` varchar(150) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '1',
  `access` tinyint(3) NOT NULL DEFAULT '0',
  `is_default` tinyint(2) NOT NULL DEFAULT '0',
  `description` mediumtext NOT NULL,
  `positive` int(11) NOT NULL DEFAULT '0',
  `negative` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_object_type_object_id` (`object_type`,`object_id`),
  KEY `idx_state` (`state`),
  KEY `idx_access` (`access`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__collections_assets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `state` tinyint(2) NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL DEFAULT 'file',
  `ordering` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_state` (`state`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__collections_following` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `follower_type` varchar(150) NOT NULL,
  `follower_id` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `following_type` varchar(150) NOT NULL DEFAULT '',
  `following_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_follower_type_follower_id` (`follower_type`,`follower_id`),
  KEY `idx_following_type_following_id` (`following_type`,`following_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__collections_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `url` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '1',
  `access` tinyint(2) NOT NULL DEFAULT '0',
  `positive` int(11) NOT NULL DEFAULT '0',
  `negative` int(11) NOT NULL DEFAULT '0',
  `type` varchar(150) NOT NULL DEFAULT '',
  `object_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`),
  KEY `idx_created_by` (`created_by`),
  FULLTEXT KEY `idx_fulltxt_title_description` (`title`,`description`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__collections_posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `collection_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `description` mediumtext NOT NULL,
  `original` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_collection_id` (`collection_id`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_original` (`original`),
  FULLTEXT KEY `idx_fulltxt_description` (`description`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__collections_votes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `voted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_item_id_user_id` (`item_id`,`user_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses` (
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
  FULLTEXT KEY `ftidx_alias_title_blurb` (`alias`,`title`,`blurb`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_announcements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) NOT NULL DEFAULT '0',
  `content` text,
  `priority` tinyint(2) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `section_id` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(2) NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sticky` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_offering_id` (`offering_id`),
  KEY `idx_section_id` (`section_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_state` (`state`),
  KEY `idx_priority` (`priority`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_asset_associations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `scope_id` int(11) NOT NULL DEFAULT '0',
  `scope` varchar(255) NOT NULL DEFAULT 'asset_group',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_asset_id` (`asset_id`),
  KEY `idx_scope_id` (`scope_id`),
  KEY `idx_scope` (`scope`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_asset_group_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(200) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_asset_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(250) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(2) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_unit_id` (`unit_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_asset_unity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `passed` tinyint(1) NOT NULL,
  `details` text,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_asset_views` (
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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_assets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` mediumtext,
  `type` varchar(255) NOT NULL DEFAULT '',
  `subtype` varchar(255) NOT NULL DEFAULT 'file',
  `url` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(2) NOT NULL DEFAULT '1',
  `course_id` int(11) NOT NULL DEFAULT '0',
  `graded` tinyint(2) DEFAULT NULL,
  `grade_weight` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_course_id` (`course_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_certificates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `properties` text,
  `course_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_form_answers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `correct` tinyint(4) NOT NULL,
  `left_dist` int(11) NOT NULL,
  `top_dist` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_question_id` (`question_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_form_deployments` (
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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_form_questions` (
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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_form_respondent_progress` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `respondent_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `submitted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_respondent_id_question_id` (`respondent_id`,`question_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_form_respondents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `deployment_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `started` timestamp NULL DEFAULT NULL,
  `finished` timestamp NULL DEFAULT NULL,
  `attempt` int(11) NOT NULL DEFAULT '1',
  `attempts` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_member_id` (`member_id`),
  KEY `idx_deployment_id` (`deployment_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_form_responses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `respondent_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_respondent_id` (`respondent_id`),
  KEY `idx_question_id` (`question_id`),
  KEY `idx_answer_id` (`answer_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_forms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` text,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `asset_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_grade_book` (
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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_grade_policies` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` mediumtext,
  `threshold` decimal(3,2) DEFAULT NULL,
  `exam_weight` decimal(3,2) DEFAULT NULL,
  `quiz_weight` decimal(3,2) DEFAULT NULL,
  `homework_weight` decimal(3,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scope_id` int(11) NOT NULL DEFAULT '0',
  `scope` varchar(100) NOT NULL DEFAULT '',
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `action` varchar(50) NOT NULL DEFAULT '',
  `comments` text NOT NULL,
  `actor_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_member_badges` (
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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_member_notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(255) NOT NULL DEFAULT '',
  `scope_id` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `content` mediumtext NOT NULL,
  `pos_x` int(11) NOT NULL DEFAULT '0',
  `pos_y` int(11) NOT NULL DEFAULT '0',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(2) NOT NULL DEFAULT '0',
  `timestamp` time NOT NULL DEFAULT '00:00:00',
  `section_id` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_scoped` (`scope`,`scope_id`),
  KEY `idx_createdby` (`created_by`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_members` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `course_id` int(11) NOT NULL DEFAULT '0',
  `offering_id` int(11) NOT NULL DEFAULT '0',
  `section_id` int(11) NOT NULL DEFAULT '0',
  `role_id` int(11) NOT NULL DEFAULT '0',
  `permissions` mediumtext NOT NULL,
  `enrolled` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `student` tinyint(2) NOT NULL DEFAULT '0',
  `first_visit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `token` varchar(23) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_offering_id` (`offering_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_role_id` (`role_id`),
  KEY `idx_section_id` (`section_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_offering_section_badge_criteria` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `section_badge_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_offering_section_badges` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `published` int(1) NOT NULL DEFAULT '0',
  `provider_name` varchar(255) NOT NULL DEFAULT 'passport',
  `provider_badge_id` int(11) NOT NULL,
  `img_url` varchar(255) NOT NULL DEFAULT '',
  `criteria_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_offering_section_codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `code` varchar(10) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `redeemed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `redeemed_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_offering_section_dates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `scope` varchar(150) NOT NULL DEFAULT '',
  `scope_id` int(11) NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_section_id` (`section_id`),
  KEY `idx_scope_id` (`scope_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_offering_sections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) NOT NULL DEFAULT '0',
  `is_default` tinyint(2) NOT NULL DEFAULT '0',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `state` tinyint(2) NOT NULL DEFAULT '1',
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `enrollment` tinyint(2) NOT NULL DEFAULT '0',
  `grade_policy_id` int(11) NOT NULL DEFAULT '1',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_offering_id` (`offering_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_state` (`state`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_offerings` (
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
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_course_id` (`course_id`),
  KEY `idx_state` (`state`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_page_hits` (
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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_pages` (
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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_prerequisites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `item_scope` varchar(255) NOT NULL DEFAULT 'asset',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `requisite_scope` varchar(255) NOT NULL DEFAULT 'asset',
  `requisite_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_progress_factors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_reviews` (
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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(150) NOT NULL,
  `title` varchar(150) NOT NULL DEFAULT '',
  `permissions` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_offering_id` (`offering_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__courses_units` (
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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__cron_jobs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `plugin` varchar(255) NOT NULL DEFAULT '',
  `event` varchar(255) NOT NULL DEFAULT '',
  `last_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `next_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `recurrence` varchar(50) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(3) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__document_resource_rel` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_id` (`id`),
  UNIQUE KEY `uidx_document_id_resource_id` (`document_id`,`resource_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__document_text_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `body` text,
  `hash` char(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_hash` (`hash`),
  FULLTEXT KEY `ftidx_body` (`body`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__doi_mapping` (
  `local_revision` int(11) NOT NULL,
  `doi_label` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `alias` varchar(30) DEFAULT NULL,
  `versionid` int(11) DEFAULT '0',
  `doi` varchar(50) DEFAULT NULL
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__email_bounces` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) DEFAULT NULL,
  `component` varchar(100) DEFAULT NULL,
  `object` varchar(100) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `reason` text,
  `date` datetime DEFAULT NULL,
  `resolved` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__events` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL DEFAULT '1',
  `calendar_id` int(11) DEFAULT NULL,
  `ical_uid` varchar(255) DEFAULT NULL,
  `scope` varchar(100) DEFAULT NULL,
  `scope_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  `adresse_info` varchar(120) NOT NULL DEFAULT '',
  `contact_info` varchar(120) NOT NULL DEFAULT '',
  `extra_info` varchar(240) NOT NULL DEFAULT '',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `allday` int(11) DEFAULT '0',
  `time_zone` varchar(5) DEFAULT NULL,
  `repeating_rule` varchar(150) DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '1',
  `registerby` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text,
  `restricted` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `ftidx_title` (`title`),
  FULLTEXT KEY `ftidx_content` (`content`),
  FULLTEXT KEY `ftidx_title_content` (`title`,`content`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__events_calendars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scope` varchar(100) DEFAULT NULL,
  `scope_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `color` varchar(100) DEFAULT NULL,
  `published` int(11) DEFAULT '1',
  `url` varchar(255) DEFAULT NULL,
  `readonly` tinyint(4) DEFAULT '0',
  `last_fetched` datetime DEFAULT NULL,
  `last_fetched_attempt` datetime DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__events_categories` (
  `id` int(12) NOT NULL DEFAULT '0',
  `color` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__events_config` (
  `param` varchar(100) DEFAULT NULL,
  `value` tinytext
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__events_respondent_race_rel` (
  `respondent_id` int(11) DEFAULT NULL,
  `race` varchar(255) DEFAULT NULL,
  `tribal_affiliation` varchar(255) DEFAULT NULL
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  KEY `idx_section` (`section`),
  KEY `idx_category` (`category`),
  KEY `idx_alias` (`alias`),
  FULLTEXT KEY `ftidx_title` (`title`),
  FULLTEXT KEY `ftidx_title_params_fulltxt` (`title`,`params`,`fulltxt`),
  FULLTEXT KEY `ftidx_params` (`params`),
  FULLTEXT KEY `ftidx_fulltxt` (`fulltxt`),
  FULLTEXT KEY `ftidx_title_fulltxt` (`title`,`fulltxt`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__faq_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `alias` varchar(200) DEFAULT NULL,
  `description` varchar(255) DEFAULT '',
  `section` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `access` tinyint(3) NOT NULL DEFAULT '0',
  `asset_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_alias` (`alias`),
  KEY `idx_section` (`section`),
  KEY `idx_state` (`state`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  `state` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_entry_id` (`entry_id`),
  KEY `idx_state` (`state`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__faq_helpful_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) DEFAULT '0',
  `ip` varchar(15) DEFAULT NULL,
  `vote` varchar(10) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_type_object_id` (`type`,`object_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__feedaggregator_feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `created` date DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `enabled` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__feedaggregator_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `created` int(20) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `feed_id` int(11) NOT NULL,
  `status` varchar(45) DEFAULT NULL,
  `description` text,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT '',
  `org` varchar(100) DEFAULT '',
  `quote` text,
  `picture` varchar(250) DEFAULT '',
  `date` datetime DEFAULT '0000-00-00 00:00:00',
  `publish_ok` tinyint(1) DEFAULT '0',
  `contact_ok` tinyint(1) DEFAULT '0',
  `notes` text,
  `short_quote` text,
  `miniquote` varchar(255) NOT NULL DEFAULT '',
  `admin_rating` tinyint(1) NOT NULL DEFAULT '0',
  `notable_quote` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__focus_area_resource_type_rel` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `focus_area_id` int(11) NOT NULL,
  `resource_type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__focus_areas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL,
  `mandatory_depth` int(11) DEFAULT NULL,
  `multiple_depth` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__forum_attachments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL DEFAULT '0',
  `post_id` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_filename_post_id` (`filename`,`post_id`),
  KEY `idx_parent` (`parent`),
  KEY `idx_filename_postid` (`filename`,`post_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  `scope` varchar(100) NOT NULL DEFAULT 'site',
  `scope_id` int(11) NOT NULL DEFAULT '0',
  `section_id` int(11) NOT NULL DEFAULT '0',
  `closed` tinyint(2) NOT NULL DEFAULT '0',
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `object_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_scope_scope_id` (`scope`,`scope_id`),
  KEY `idx_asset_id` (`asset_id`),
  KEY `idx_object_id` (`object_id`),
  KEY `idx_state` (`state`),
  KEY `idx_access` (`access`),
  KEY `idx_section_id` (`section_id`),
  KEY `idx_closed` (`closed`),
  KEY `idx_scoped` (`scope`,`scope_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  `scope` varchar(100) NOT NULL DEFAULT 'site',
  `scope_id` int(11) NOT NULL DEFAULT '0',
  `scope_sub_id` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(2) NOT NULL DEFAULT '0',
  `anonymous` tinyint(2) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `last_activity` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `object_id` int(11) NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `thread` int(11) NOT NULL DEFAULT '0',
  `closed` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_scope_scope_id` (`scope`,`scope_id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_access` (`access`),
  KEY `idx_asset_id` (`asset_id`),
  KEY `idx_object_id` (`object_id`),
  KEY `idx_state` (`state`),
  KEY `idx_sticky` (`sticky`),
  KEY `idx_parent` (`parent`),
  KEY `idx_scoped` (`scope`,`scope_id`),
  FULLTEXT KEY `ftidx_comment` (`comment`),
  FULLTEXT KEY `ftidx_comment_title` (`comment`,`title`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__forum_sections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(2) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `scope` varchar(100) NOT NULL DEFAULT 'site',
  `scope_id` int(11) NOT NULL DEFAULT '0',
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `object_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_scoped` (`scope`,`scope_id`),
  KEY `idx_asset_id` (`asset_id`),
  KEY `idx_object_id` (`object_id`),
  KEY `idx_access` (`access`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__incremental_registration_group_label_rel` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__incremental_registration_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hours` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__incremental_registration_labels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__incremental_registration_options` (
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `popover_text` text NOT NULL,
  `award_per` int(11) NOT NULL,
  `test_group` int(11) NOT NULL
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__incremental_registration_popover_recurrence` (
  `idx` int(11) NOT NULL,
  `hours` int(11) NOT NULL
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__item_comment_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_comment_id` (`comment_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__item_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL DEFAULT '0',
  `item_type` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `anonymous` tinyint(2) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `notify` tinyint(2) NOT NULL DEFAULT '0',
  `access` tinyint(2) NOT NULL DEFAULT '0',
  `state` tinyint(2) NOT NULL DEFAULT '0',
  `positive` int(11) NOT NULL DEFAULT '0',
  `negative` int(11) NOT NULL DEFAULT '0',
  `rating` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_item_type_item_id` (`item_type`,`item_id`),
  KEY `idx_parent` (`parent`),
  KEY `idx_state` (`state`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__item_votes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL DEFAULT '0',
  `item_type` varchar(255) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `vote` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_item_type_item_id` (`item_type`,`item_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(150) NOT NULL DEFAULT '',
  `ordernum` int(11) NOT NULL DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_employers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `subscriptionid` int(11) NOT NULL DEFAULT '0',
  `companyName` varchar(250) DEFAULT '',
  `companyLocation` varchar(250) DEFAULT '',
  `companyWebsite` varchar(250) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_prefs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0',
  `category` varchar(20) NOT NULL DEFAULT 'resume',
  `filters` text,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_resumes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` varchar(100) DEFAULT NULL,
  `filename` varchar(100) DEFAULT NULL,
  `main` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_shortlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp` int(11) NOT NULL DEFAULT '0',
  `seeker` int(11) NOT NULL DEFAULT '0',
  `category` varchar(11) NOT NULL DEFAULT 'resume',
  `jobid` int(11) DEFAULT '0',
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL,
  `category` varchar(11) NOT NULL DEFAULT '',
  `total_viewed` int(11) DEFAULT '0',
  `total_shared` int(11) DEFAULT '0',
  `viewed_today` int(11) DEFAULT '0',
  `lastviewed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) DEFAULT NULL,
  `description` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__licenses_tools` (
  `license_id` int(11) DEFAULT '0',
  `tool_id` int(11) DEFAULT '0',
  `created` datetime NOT NULL
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__licenses_users` (
  `license_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  PRIMARY KEY (`license_id`,`user_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__market_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL DEFAULT '0',
  `category` varchar(50) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `action` varchar(50) DEFAULT NULL,
  `log` text,
  `market_value` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__media_tracking` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(200) DEFAULT NULL,
  `ip_address` varchar(100) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `object_type` varchar(100) DEFAULT NULL,
  `object_duration` int(11) DEFAULT NULL,
  `current_position` int(11) DEFAULT NULL,
  `farthest_position` int(11) DEFAULT NULL,
  `current_position_timestamp` datetime DEFAULT NULL,
  `farthest_position_timestamp` datetime DEFAULT NULL,
  `completed` int(11) DEFAULT NULL,
  `total_views` int(11) DEFAULT NULL,
  `total_viewing_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_session_id` (`session_id`),
  KEY `idx_object_id` (`object_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__media_tracking_detailed` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(200) DEFAULT NULL,
  `ip_address` varchar(100) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `object_type` varchar(100) DEFAULT NULL,
  `object_duration` int(11) DEFAULT NULL,
  `current_position` int(11) DEFAULT NULL,
  `farthest_position` int(11) DEFAULT NULL,
  `current_position_timestamp` datetime DEFAULT NULL,
  `farthest_position_timestamp` datetime DEFAULT NULL,
  `completed` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__metrics_author_cluster` (
  `authorid` varchar(60) NOT NULL DEFAULT '0',
  `classes` int(11) DEFAULT '0',
  `users` int(11) DEFAULT '0',
  `schools` int(11) DEFAULT '0',
  PRIMARY KEY (`authorid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__metrics_ipgeo_cache` (
  `ip` int(10) NOT NULL DEFAULT '0',
  `countrySHORT` char(2) NOT NULL DEFAULT '',
  `countryLONG` varchar(64) NOT NULL DEFAULT '',
  `ipREGION` varchar(128) NOT NULL DEFAULT '',
  `ipCITY` varchar(128) NOT NULL DEFAULT '',
  `ipLATITUDE` double DEFAULT NULL,
  `ipLONGITUDE` double DEFAULT NULL,
  `lookup_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ip`),
  KEY `idx_lookup_datetime` (`lookup_datetime`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__migrations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `file` varchar(255) NOT NULL DEFAULT '',
  `scope` varchar(255) NOT NULL,
  `hash` char(32) NOT NULL DEFAULT '',
  `direction` varchar(10) NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  `action_by` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__newsletter_mailing_recipient_actions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mailingid` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `action_vars` text,
  `email` varchar(255) DEFAULT NULL,
  `ip` varchar(100) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `countrySHORT` char(2) DEFAULT NULL,
  `countryLONG` varchar(64) DEFAULT NULL,
  `ipREGION` varchar(128) DEFAULT NULL,
  `ipCITY` varchar(128) DEFAULT NULL,
  `ipLATITUDE` double DEFAULT NULL,
  `ipLONGITUDE` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__newsletter_mailing_recipients` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(11) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_sent` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__newsletter_mailinglist_emails` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(11) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `confirmed` int(11) DEFAULT '0',
  `date_added` datetime DEFAULT NULL,
  `date_confirmed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__newsletter_mailinglist_unsubscribes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(11) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `reason` text,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__newsletter_mailinglists` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `description` text,
  `private` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__newsletter_mailings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nid` int(11) DEFAULT NULL,
  `lid` int(11) DEFAULT NULL,
  `subject` varchar(250) DEFAULT NULL,
  `html_body` longtext,
  `plain_body` longtext,
  `headers` text,
  `args` text,
  `tracking` int(11) DEFAULT '1',
  `date` datetime DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__newsletter_primary_story` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nid` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `story` text,
  `readmore_title` varchar(100) DEFAULT NULL,
  `readmore_link` varchar(200) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__newsletter_secondary_story` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nid` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `story` text,
  `readmore_title` varchar(100) DEFAULT NULL,
  `readmore_link` varchar(200) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__newsletter_templates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `editable` int(11) DEFAULT '1',
  `name` varchar(100) DEFAULT NULL,
  `template` text,
  `primary_title_color` varchar(100) DEFAULT NULL,
  `primary_text_color` varchar(100) DEFAULT NULL,
  `secondary_title_color` varchar(100) DEFAULT NULL,
  `secondary_text_color` varchar(100) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__newsletters` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(150) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `issue` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT 'html',
  `template` int(11) DEFAULT NULL,
  `published` int(11) DEFAULT '1',
  `sent` int(11) DEFAULT '0',
  `html_content` mediumtext,
  `plain_content` mediumtext,
  `tracking` int(11) DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__oaipmh_dcspecs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `query` text NOT NULL,
  `display` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__oauthp_consumers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` tinyint(4) NOT NULL,
  `token` varchar(250) NOT NULL,
  `secret` varchar(250) NOT NULL,
  `callback_url` varchar(250) NOT NULL,
  `xauth` tinyint(4) NOT NULL,
  `xauth_grant` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__oauthp_nonces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nonce` varchar(250) NOT NULL,
  `stamp` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_nonce_stamp` (`nonce`,`stamp`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__oauthp_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consumer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `token` varchar(250) NOT NULL,
  `token_secret` varchar(250) NOT NULL,
  `callback_url` varchar(250) NOT NULL,
  `verifier` varchar(250) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__order_items` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `oid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `itemid` int(11) NOT NULL DEFAULT '0',
  `price` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `selections` text,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__password_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` char(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__password_character_class` (
  `flag` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(32) NOT NULL,
  `regex` char(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__plugin_params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) DEFAULT '0',
  `folder` varchar(100) DEFAULT NULL,
  `element` varchar(100) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__poll_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pollid` int(4) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_pollid_text` (`pollid`,`text`(1))
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__poll_date` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vote_id` int(11) NOT NULL DEFAULT '0',
  `poll_id` int(11) NOT NULL DEFAULT '0',
  `voter_ip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_poll_id` (`poll_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__poll_menu` (
  `pollid` int(11) NOT NULL DEFAULT '0',
  `menuid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pollid`,`menuid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__polls` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  `mailPreferenceOption` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_database_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `database_name` varchar(64) NOT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  `data_definition` text,
  PRIMARY KEY (`id`,`database_name`,`version`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_databases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project` int(11) NOT NULL,
  `database_name` varchar(64) NOT NULL,
  `title` varchar(127) NOT NULL DEFAULT '',
  `source_file` varchar(127) NOT NULL,
  `source_dir` varchar(127) NOT NULL,
  `source_revision` varchar(56) NOT NULL,
  `description` text,
  `data_definition` text,
  `revision` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `projectid` int(11) unsigned NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `ajax` tinyint(1) DEFAULT '0',
  `owner` int(11) unsigned DEFAULT '0',
  `ip` varchar(15) DEFAULT '0',
  `section` varchar(100) DEFAULT 'general',
  `layout` varchar(100) DEFAULT '',
  `action` varchar(100) DEFAULT '',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `request_uri` tinytext,
  PRIMARY KEY (`id`),
  KEY `idx_projectid` (`projectid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_microblog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blogentry` text,
  `posted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `posted_by` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(2) DEFAULT '0',
  `params` tinytext,
  `projectid` int(11) NOT NULL DEFAULT '0',
  `activityid` int(11) NOT NULL DEFAULT '0',
  `managers_only` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `ftidx_blogentry` (`blogentry`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_owners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectid` int(11) NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `groupid` int(11) DEFAULT '0',
  `invited_name` varchar(100) DEFAULT NULL,
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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_public_stamps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stamp` varchar(30) NOT NULL DEFAULT '0',
  `projectid` int(11) NOT NULL DEFAULT '0',
  `listed` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL DEFAULT 'files',
  `reference` text NOT NULL,
  `expires` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_stamp` (`stamp`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_remote_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectid` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT '0',
  `paired` int(11) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `synced` datetime DEFAULT NULL,
  `local_path` varchar(255) NOT NULL,
  `original_path` varchar(255) NOT NULL,
  `original_format` varchar(200) NOT NULL,
  `local_dirpath` varchar(255) NOT NULL DEFAULT '',
  `local_format` varchar(200) DEFAULT NULL,
  `local_md5` varchar(32) DEFAULT NULL,
  `service` varchar(50) NOT NULL,
  `type` varchar(25) NOT NULL DEFAULT 'file',
  `remote_editing` tinyint(1) NOT NULL DEFAULT '0',
  `remote_id` varchar(100) NOT NULL,
  `original_id` varchar(100) NOT NULL,
  `remote_parent` varchar(100) DEFAULT NULL,
  `remote_title` varchar(140) DEFAULT NULL,
  `remote_md5` varchar(32) DEFAULT NULL,
  `remote_format` varchar(200) DEFAULT NULL,
  `remote_author` varchar(100) DEFAULT NULL,
  `remote_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_stats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `month` int(2) DEFAULT NULL,
  `year` int(2) DEFAULT NULL,
  `week` int(2) DEFAULT NULL,
  `processed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stats` text,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__project_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(150) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  UNIQUE KEY `uidx_alias` (`alias`),
  FULLTEXT KEY `idx_fulltxt_alias_title_about` (`alias`,`title`,`about`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_version_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_version_id` int(11) NOT NULL DEFAULT '0',
  `publication_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT '0',
  `object_id` int(11) DEFAULT '0',
  `object_name` varchar(64) DEFAULT '0',
  `object_instance` int(11) DEFAULT '0',
  `object_revision` int(11) DEFAULT '0',
  `role` tinyint(1) DEFAULT '0',
  `path` varchar(255) NOT NULL,
  `vcs_hash` varchar(255) DEFAULT NULL,
  `vcs_revision` varchar(255) DEFAULT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'file',
  `params` text,
  `attribs` text,
  `ordering` int(11) DEFAULT '0',
  `content_hash` varchar(255) DEFAULT NULL,
  `element_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_audience` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) NOT NULL DEFAULT '0',
  `publication_version_id` int(11) DEFAULT '0',
  `level0` tinyint(2) NOT NULL DEFAULT '0',
  `level1` tinyint(2) NOT NULL DEFAULT '0',
  `level2` tinyint(2) NOT NULL DEFAULT '0',
  `level3` tinyint(2) NOT NULL DEFAULT '0',
  `level4` tinyint(2) NOT NULL DEFAULT '0',
  `level5` tinyint(2) NOT NULL DEFAULT '0',
  `comments` varchar(255) DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_audience_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(11) NOT NULL DEFAULT '0',
  `title` varchar(100) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_authors` (
  `publication_version_id` int(11) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `project_owner_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `firstName` varchar(255) DEFAULT NULL,
  `lastName` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `credit` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block` varchar(100) NOT NULL DEFAULT '',
  `label` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  `minimum` int(11) NOT NULL DEFAULT '0',
  `maximum` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text,
  `manifest` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `block` (`block`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `dc_type` varchar(200) NOT NULL DEFAULT 'Dataset',
  `alias` varchar(200) NOT NULL DEFAULT '',
  `url_alias` varchar(200) NOT NULL DEFAULT '',
  `description` tinytext,
  `contributable` int(2) DEFAULT '1',
  `state` tinyint(1) DEFAULT '1',
  `customFields` text,
  `params` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_name` (`name`),
  UNIQUE KEY `uidx_alias` (`alias`),
  UNIQUE KEY `uidx_url_alias` (`url_alias`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_curation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) NOT NULL DEFAULT '0',
  `publication_version_id` int(11) NOT NULL DEFAULT '0',
  `updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  `update` text,
  `reviewed` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT '0',
  `review` text,
  `review_status` int(11) NOT NULL DEFAULT '0',
  `block` varchar(100) NOT NULL DEFAULT '',
  `step` int(11) DEFAULT '0',
  `element` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_curation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_version_id` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `changelog` text NOT NULL,
  `curator` tinyint(3) NOT NULL DEFAULT '0',
  `oldstatus` int(11) NOT NULL DEFAULT '0',
  `newstatus` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_handlers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `label` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  `about` text,
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `text` text,
  `title` varchar(100) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `info` text,
  `ordering` int(11) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '0',
  `apps_only` int(11) NOT NULL DEFAULT '0',
  `main` int(11) NOT NULL DEFAULT '0',
  `agreement` int(11) DEFAULT '0',
  `customizable` int(11) DEFAULT '0',
  `icon` varchar(250) DEFAULT NULL,
  `opensource` tinyint(1) NOT NULL DEFAULT '0',
  `restriction` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) NOT NULL,
  `publication_version_id` int(11) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(2) NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `page_views` int(11) DEFAULT '0',
  `primary_accesses` int(11) DEFAULT '0',
  `support_accesses` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_master_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(200) NOT NULL DEFAULT '',
  `alias` varchar(200) NOT NULL DEFAULT '',
  `description` tinytext,
  `contributable` int(2) DEFAULT '0',
  `supporting` int(2) DEFAULT '0',
  `ordering` int(2) DEFAULT '0',
  `params` text,
  `curation` text,
  `curatorgroup` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_alias` (`alias`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) NOT NULL DEFAULT '0',
  `publication_version_id` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `comment` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anonymous` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_screenshots` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `publication_version_id` int(11) NOT NULL DEFAULT '0',
  `publication_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(127) DEFAULT '',
  `ordering` int(11) DEFAULT '0',
  `filename` varchar(100) NOT NULL,
  `srcfile` varchar(100) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `created_by` varchar(127) DEFAULT NULL,
  `modified_by` varchar(127) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_stats` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `publication_id` bigint(20) NOT NULL,
  `publication_version` tinyint(4) DEFAULT NULL,
  `users` bigint(20) DEFAULT NULL,
  `downloads` bigint(20) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT '-1',
  `processed_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_publication_id_datetime_period` (`publication_id`,`datetime`,`period`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publication_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) NOT NULL DEFAULT '0',
  `main` int(1) NOT NULL DEFAULT '0',
  `doi` varchar(255) DEFAULT '',
  `ark` varchar(255) DEFAULT '',
  `state` int(1) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `abstract` text NOT NULL,
  `metadata` text,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `published_up` datetime DEFAULT '0000-00-00 00:00:00',
  `published_down` datetime DEFAULT NULL,
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `accepted` datetime DEFAULT '0000-00-00 00:00:00',
  `submitted` datetime DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT '0',
  `version_label` varchar(100) NOT NULL DEFAULT '1.0',
  `secret` varchar(10) NOT NULL DEFAULT '',
  `version_number` int(11) NOT NULL DEFAULT '0',
  `params` text,
  `release_notes` text,
  `license_text` text,
  `license_type` int(11) DEFAULT NULL,
  `access` int(11) NOT NULL DEFAULT '0',
  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `times_rated` int(11) NOT NULL DEFAULT '0',
  `ranking` float NOT NULL DEFAULT '0',
  `curation` text,
  `reviewed` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `idx_fulltxt_title_description_abstract` (`title`,`description`,`abstract`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__publications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL DEFAULT '0',
  `master_type` int(11) NOT NULL DEFAULT '1',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `access` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `times_rated` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(100) NOT NULL DEFAULT '',
  `ranking` float NOT NULL DEFAULT '0',
  `group_owner` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__recent_tools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `tool` varchar(200) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__recommendation` (
  `fromID` int(11) NOT NULL,
  `toID` int(11) NOT NULL,
  `contentScore` float unsigned zerofill DEFAULT NULL,
  `tagScore` float unsigned zerofill DEFAULT NULL,
  `titleScore` float unsigned zerofill DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fromID`,`toID`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__redirection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cpt` int(11) NOT NULL DEFAULT '0',
  `oldurl` varchar(100) NOT NULL DEFAULT '',
  `newurl` varchar(150) NOT NULL DEFAULT '',
  `dateadd` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `idx_newurl` (`newurl`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_assoc` (
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `child_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `grouping` int(11) NOT NULL DEFAULT '0',
  KEY `idx_parent_id_child_id` (`parent_id`,`child_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_import_hooks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(25) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `notes` text,
  `file` varchar(100) DEFAULT NULL,
  `state` int(11) DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_import_runs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `import_id` int(11) DEFAULT NULL,
  `processed` int(11) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `ran_by` int(11) DEFAULT NULL,
  `ran_at` datetime DEFAULT NULL,
  `dry_run` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_imports` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `notes` text,
  `file` varchar(255) DEFAULT '',
  `count` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `state` int(11) DEFAULT '1',
  `mode` varchar(10) DEFAULT 'UPDATE',
  `params` text,
  `hooks` text,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `comment` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anonymous` tinyint(3) NOT NULL DEFAULT '0',
  `state` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  UNIQUE KEY `uidx_resid_restype_datetime_period` (`resid`,`restype`,`datetime`,`period`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  KEY `idx_cluster` (`cluster`),
  KEY `idx_username` (`username`),
  KEY `idx_uidNumber` (`uidNumber`),
  KEY `idx_toolname` (`toolname`),
  KEY `idx_resid` (`resid`),
  KEY `idx_clustersize` (`clustersize`),
  KEY `idx_cluster_start` (`cluster_start`),
  KEY `idx_cluster_end` (`cluster_end`),
  KEY `idx_institution` (`institution`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_stats_tools_tops` (
  `top` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  `valfmt` tinyint(4) NOT NULL DEFAULT '0',
  `size` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`top`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_stats_tools_topvals` (
  `id` bigint(20) NOT NULL,
  `top` tinyint(4) NOT NULL DEFAULT '0',
  `rank` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `value` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_taxonomy_audience_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(11) NOT NULL DEFAULT '0',
  `title` varchar(100) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__resource_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(100) DEFAULT NULL,
  `type` varchar(200) NOT NULL DEFAULT '',
  `category` int(11) NOT NULL DEFAULT '0',
  `description` tinytext,
  `contributable` int(2) DEFAULT '1',
  `customFields` text,
  `params` text,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  FULLTEXT KEY `ftidx_title` (`title`),
  FULLTEXT KEY `ftidx_introtext_fulltxt` (`introtext`,`fulltxt`),
  FULLTEXT KEY `ftidx_title_introtext_fulltxt` (`title`,`introtext`,`fulltxt`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__screenshots` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `versionid` int(11) DEFAULT '0',
  `title` varchar(127) DEFAULT '',
  `ordering` int(11) DEFAULT '0',
  `filename` varchar(100) NOT NULL,
  `resourceid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__session_geo` (
  `session_id` varchar(200) NOT NULL DEFAULT '0',
  `username` varchar(150) DEFAULT '',
  `time` varchar(14) DEFAULT '',
  `guest` tinyint(4) DEFAULT '1',
  `userid` int(11) DEFAULT '0',
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
  KEY `idx_userid` (`userid`),
  KEY `idx_time` (`time`),
  KEY `idx_ip` (`ip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__stats_tops` (
  `id` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  `valfmt` tinyint(4) NOT NULL DEFAULT '0',
  `size` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__stats_topvals` (
  `top` tinyint(4) NOT NULL DEFAULT '0',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT '1',
  `rank` smallint(6) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `value` bigint(20) NOT NULL DEFAULT '0',
  KEY `idx_top` (`top`),
  KEY `idx_top_rank` (`top`,`rank`),
  KEY `idx_top_datetime` (`top`,`datetime`),
  KEY `idx_top_datetime_rank` (`top`,`datetime`,`rank`),
  KEY `idx_top_datetime_period` (`top`,`datetime`,`period`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__storefront_collections` (
  `cId` char(50) NOT NULL,
  `cName` varchar(64) DEFAULT NULL,
  `cParent` int(16) DEFAULT NULL,
  `cActive` tinyint(1) DEFAULT NULL,
  `cType` char(10) DEFAULT NULL,
  PRIMARY KEY (`cId`),
  KEY `idx_cActive` (`cActive`),
  KEY `idx_cParent` (`cParent`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__storefront_coupon_actions` (
  `cnId` int(16) NOT NULL,
  `cnaAction` char(25) DEFAULT NULL,
  `cnaVal` char(255) DEFAULT NULL,
  UNIQUE KEY `uidx_cnId_cnaAction` (`cnId`,`cnaAction`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__storefront_coupon_conditions` (
  `cnId` int(16) NOT NULL,
  `cncRule` char(100) DEFAULT NULL,
  `cncVal` char(255) DEFAULT NULL
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__storefront_coupon_objects` (
  `cnId` int(16) NOT NULL,
  `cnoObjectId` int(16) DEFAULT NULL,
  `cnoObjectsLimit` int(5) DEFAULT '0' COMMENT 'How many objects can be applied to. 0 - unlimited',
  UNIQUE KEY `uidx_cnId_cnoObjectId` (`cnId`,`cnoObjectId`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__storefront_coupons` (
  `cnId` int(16) NOT NULL AUTO_INCREMENT,
  `cnCode` char(25) DEFAULT NULL,
  `cnDescription` char(255) DEFAULT NULL,
  `cnExpires` date DEFAULT NULL,
  `cnUseLimit` int(5) unsigned DEFAULT NULL,
  `cnObject` char(15) NOT NULL,
  `cnActive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`cnId`),
  UNIQUE KEY `uidx_cnCode` (`cnCode`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__storefront_option_groups` (
  `ogId` int(16) NOT NULL AUTO_INCREMENT,
  `ogName` char(16) DEFAULT NULL,
  PRIMARY KEY (`ogId`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__storefront_options` (
  `oId` int(16) NOT NULL AUTO_INCREMENT,
  `ogId` int(16) DEFAULT NULL COMMENT 'Foreign key to option-groups',
  `oName` char(255) DEFAULT NULL,
  PRIMARY KEY (`oId`),
  UNIQUE KEY `uidx_ogId_oName` (`ogId`,`oName`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__storefront_product_collections` (
  `cllId` int(16) NOT NULL AUTO_INCREMENT,
  `pId` int(16) NOT NULL,
  `cId` char(50) NOT NULL,
  PRIMARY KEY (`cllId`,`pId`,`cId`),
  UNIQUE KEY `uidx_pId_cId` (`pId`,`cId`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__storefront_product_option_groups` (
  `pId` int(16) NOT NULL,
  `ogId` int(16) NOT NULL,
  PRIMARY KEY (`pId`,`ogId`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__storefront_product_types` (
  `ptId` int(16) NOT NULL AUTO_INCREMENT,
  `ptName` char(128) DEFAULT NULL,
  `ptModel` char(25) DEFAULT 'normal',
  PRIMARY KEY (`ptId`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__storefront_products` (
  `pId` int(16) NOT NULL AUTO_INCREMENT,
  `ptId` int(16) NOT NULL COMMENT 'Product type ID. Foreign key to product_types table',
  `pName` char(128) DEFAULT NULL,
  `pTagline` tinytext,
  `pDescription` text,
  `pFeatures` text,
  `pActive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`pId`),
  KEY `idx_pActive` (`pActive`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__storefront_sku_meta` (
  `smId` int(16) NOT NULL AUTO_INCREMENT,
  `sId` int(16) NOT NULL,
  `smKey` varchar(100) DEFAULT NULL,
  `smValue` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`smId`),
  UNIQUE KEY `uidx_sId_smKey` (`sId`,`smKey`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__storefront_sku_options` (
  `sId` int(16) NOT NULL,
  `oId` int(16) NOT NULL,
  PRIMARY KEY (`sId`,`oId`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__storefront_skus` (
  `sId` int(16) NOT NULL AUTO_INCREMENT,
  `pId` int(16) DEFAULT NULL COMMENT 'Foreign key to products',
  `sSku` char(16) DEFAULT NULL,
  `sWeight` decimal(10,2) DEFAULT NULL,
  `sPrice` decimal(10,2) DEFAULT NULL,
  `sDescriprtion` text,
  `sFeatures` text,
  `sTrackInventory` tinyint(1) DEFAULT '0',
  `sInventory` int(11) DEFAULT '0',
  `sEnumerable` tinyint(1) DEFAULT '1',
  `sAllowMultiple` tinyint(1) DEFAULT '1',
  `sActive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`sId`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_acl_acos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(100) NOT NULL DEFAULT '',
  `foreign_key` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_acl_aros` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(100) NOT NULL DEFAULT '',
  `foreign_key` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_model_foreign_key` (`model`,`foreign_key`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_acl_aros_acos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aro_id` int(11) unsigned NOT NULL DEFAULT '0',
  `aco_id` int(11) unsigned NOT NULL DEFAULT '0',
  `action_create` tinyint(3) NOT NULL DEFAULT '0',
  `action_read` tinyint(3) NOT NULL DEFAULT '0',
  `action_update` tinyint(3) NOT NULL DEFAULT '0',
  `action_delete` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_aco_id` (`aco_id`),
  KEY `idx_aro_id` (`aro_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_attachments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(255) DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `comment_id` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_ticket` (`ticket`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(250) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket` int(11) unsigned NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `changelog` text NOT NULL,
  `access` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_ticket` (`ticket`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_queries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `conditions` text NOT NULL,
  `query` text NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `sort` varchar(100) NOT NULL DEFAULT '',
  `sort_dir` varchar(100) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `iscore` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_iscore` (`iscore`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_resolutions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `alias` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_statuses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `open` tinyint(2) NOT NULL DEFAULT '0',
  `title` varchar(250) NOT NULL DEFAULT '',
  `alias` varchar(250) NOT NULL DEFAULT '',
  `color` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_open` (`open`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_tickets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `closed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `login` varchar(200) NOT NULL DEFAULT '',
  `severity` varchar(30) NOT NULL DEFAULT '',
  `owner` int(11) NOT NULL DEFAULT '0',
  `category` varchar(50) NOT NULL DEFAULT '',
  `summary` varchar(250) NOT NULL DEFAULT '',
  `report` text NOT NULL,
  `resolved` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(200) NOT NULL DEFAULT '',
  `name` varchar(200) NOT NULL DEFAULT '',
  `os` varchar(50) NOT NULL DEFAULT '',
  `browser` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(200) NOT NULL DEFAULT '',
  `hostname` varchar(200) NOT NULL DEFAULT '',
  `uas` varchar(250) NOT NULL DEFAULT '',
  `referrer` varchar(250) NOT NULL DEFAULT '',
  `cookies` tinyint(3) NOT NULL DEFAULT '0',
  `instances` int(11) NOT NULL DEFAULT '1',
  `section` int(11) NOT NULL DEFAULT '1',
  `type` tinyint(3) NOT NULL DEFAULT '0',
  `group` varchar(250) NOT NULL DEFAULT '',
  `open` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_owner` (`owner`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__support_watching` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_ticket_id` (`ticket_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(100) NOT NULL DEFAULT '',
  `raw_tag` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_tag` (`tag`),
  FULLTEXT KEY `ftidx_description` (`description`),
  FULLTEXT KEY `ftidx_raw_tag_description` (`raw_tag`,`description`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tags_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupid` int(11) unsigned NOT NULL DEFAULT '0',
  `tagid` int(11) unsigned NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_tagid` (`tagid`),
  KEY `idx_groupid` (`groupid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tags_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) unsigned NOT NULL DEFAULT '0',
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `action` varchar(50) NOT NULL DEFAULT '',
  `comments` text NOT NULL,
  `actorid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_tag_id` (`tag_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tags_object` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `objectid` int(11) unsigned NOT NULL DEFAULT '0',
  `tagid` int(11) unsigned NOT NULL DEFAULT '0',
  `strength` tinyint(3) NOT NULL DEFAULT '0',
  `taggerid` int(11) unsigned NOT NULL DEFAULT '0',
  `taggedon` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tbl` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_objectid_tbl` (`objectid`,`tbl`),
  KEY `idx_label_tagid` (`label`,`tagid`),
  KEY `idx_tbl_objectid_label_tagid` (`tbl`,`objectid`,`label`,`tagid`),
  KEY `idx_tagid` (`tagid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tags_substitute` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) unsigned NOT NULL DEFAULT '0',
  `tag` varchar(100) NOT NULL DEFAULT '',
  `raw_tag` varchar(100) NOT NULL DEFAULT '',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_tag_id` (`tag_id`),
  KEY `idx_tag` (`tag`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__time_hub_contacts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(255) DEFAULT '000-000-0000',
  `email` varchar(255) DEFAULT '',
  `role` varchar(255) DEFAULT '',
  `hub_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__time_hubs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `name_normalized` varchar(255) NOT NULL DEFAULT '',
  `liaison` varchar(255) DEFAULT NULL,
  `anniversary_date` date DEFAULT '0000-00-00',
  `support_level` varchar(255) DEFAULT 'Standard Support',
  `active` int(1) NOT NULL DEFAULT '1',
  `notes` blob,
  `asset_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__time_records` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` double NOT NULL,
  `date` date NOT NULL,
  `description` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__time_tasks` (
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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__time_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `liaison` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  UNIQUE KEY `uidx_toolname` (`toolname`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_authors` (
  `toolname` varchar(50) NOT NULL DEFAULT '',
  `revision` int(15) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT '0',
  `version_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`toolname`,`revision`,`uid`,`version_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_groups` (
  `cn` varchar(255) NOT NULL DEFAULT '',
  `toolid` int(11) NOT NULL DEFAULT '0',
  `role` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cn`,`toolid`,`role`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `text` text,
  `title` varchar(100) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_statusviews` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ticketid` varchar(15) NOT NULL DEFAULT '',
  `uid` varchar(31) NOT NULL DEFAULT '',
  `viewed` datetime DEFAULT '0000-00-00 00:00:00',
  `elapsed` int(11) DEFAULT '500000',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  UNIQUE KEY `uidx_toolname_instance` (`toolname`,`instance`),
  KEY `idx_instance` (`instance`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_version_alias` (
  `tool_version_id` int(11) NOT NULL,
  `alias` varchar(255) NOT NULL
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_version_hostreq` (
  `tool_version_id` int(11) NOT NULL,
  `hostreq` varchar(255) NOT NULL,
  UNIQUE KEY `idx_tool_version_id_hostreq` (`tool_version_id`,`hostreq`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_version_middleware` (
  `tool_version_id` int(11) NOT NULL,
  `middleware` varchar(255) NOT NULL,
  UNIQUE KEY `uidx_tool_version_id_middleware` (`tool_version_id`,`middleware`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_version_tracperm` (
  `tool_version_id` int(11) NOT NULL,
  `tracperm` varchar(64) NOT NULL,
  UNIQUE KEY `uidx_tool_version_id_tracperm` (`tool_version_id`,`tracperm`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tool_version_zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tool_version_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `publish_up` datetime DEFAULT NULL,
  `publish_down` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_zoneid_toolversionid` (`zone_id`,`tool_version_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__trac_group_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `trac_project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_group_id_action_trac_project_id` (`group_id`,`action`,`trac_project_id`) USING BTREE
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__trac_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__trac_projects` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__trac_user_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `trac_project_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_user_id_action_trac_project_id` (`user_id`,`action`,`trac_project_id`) USING BTREE
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__user_roles` (
  `user_id` int(11) NOT NULL,
  `role` varchar(20) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_role_user_id_group_id` (`role`,`user_id`,`group_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_merge_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `source` varchar(150) NOT NULL DEFAULT '',
  `destination` varchar(150) NOT NULL DEFAULT '',
  `table` varchar(255) NOT NULL DEFAULT '',
  `column` varchar(255) NOT NULL DEFAULT '',
  `table_pk` varchar(255) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `logged` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_password_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `passhash` char(127) NOT NULL DEFAULT '',
  `action` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `invalidated` datetime DEFAULT NULL,
  `invalidated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `balance` int(11) NOT NULL DEFAULT '0',
  `earnings` int(11) NOT NULL DEFAULT '0',
  `credit` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_points_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `points` int(11) DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `alias` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  UNIQUE KEY `uidx_alias` (`alias`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_quotas` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `hard_files` int(11) NOT NULL,
  `soft_files` int(11) NOT NULL,
  `hard_blocks` int(11) NOT NULL,
  `soft_blocks` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_quotas_classes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `hard_files` int(11) NOT NULL,
  `soft_files` int(11) NOT NULL,
  `hard_blocks` int(11) NOT NULL,
  `soft_blocks` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_quotas_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `object_type` varchar(255) NOT NULL DEFAULT '',
  `object_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(255) NOT NULL DEFAULT '',
  `actor_id` int(11) NOT NULL,
  `soft_blocks` int(11) NOT NULL,
  `hard_blocks` int(11) NOT NULL,
  `soft_files` int(11) NOT NULL,
  `hard_files` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_tracperms` (
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`action`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  KEY `idx_referenceid_category_type` (`referenceid`,`category`,`type`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__vote_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referenceid` int(11) NOT NULL DEFAULT '0',
  `voted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `voter` int(11) DEFAULT NULL,
  `helpful` varchar(11) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_referenceid` (`referenceid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageid` int(11) DEFAULT '0',
  `filename` varchar(255) DEFAULT NULL,
  `description` tinytext,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_pageid` (`pageid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  PRIMARY KEY (`id`),
  KEY `idx_pageid` (`pageid`),
  KEY `idx_version` (`version`),
  KEY `idx_status` (`status`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid` int(11) DEFAULT '0',
  `action` varchar(50) DEFAULT NULL,
  `comments` text,
  `actorid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_math` (
  `inputhash` varchar(32) NOT NULL DEFAULT '',
  `outputhash` varchar(32) NOT NULL DEFAULT '',
  `conservativeness` tinyint(4) NOT NULL,
  `html` text,
  `mathml` text,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_inputhash` (`inputhash`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pagename` varchar(100) DEFAULT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `version_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_group_cn` (`group_cn`),
  KEY `idx_state` (`state`),
  FULLTEXT KEY `ftidx_title` (`title`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_page_author` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0',
  `page_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_page_id` (`page_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_page_links` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL DEFAULT '0',
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `scope` varchar(50) NOT NULL DEFAULT '',
  `scope_id` int(11) NOT NULL DEFAULT '0',
  `link` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_page_id` (`page_id`),
  KEY `idx_scope_scope_id` (`scope`,`scope_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wiki_page_metrics` (
  `pageid` int(11) NOT NULL DEFAULT '0',
  `pagename` varchar(100) DEFAULT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `visitors` int(11) NOT NULL DEFAULT '0',
  `visits` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pageid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  `length` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_pageid` (`pageid`),
  KEY `idx_approved` (`approved`),
  FULLTEXT KEY `ftidx_pagetext` (`pagetext`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wish_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wish` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wish` (`wish`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  PRIMARY KEY (`id`),
  KEY `idx_category_referenceid` (`category`,`referenceid`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_state` (`state`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  KEY `idx_wishid` (`wishid`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_approved` (`approved`),
  FULLTEXT KEY `ftidx_pagetext` (`pagetext`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  KEY `idx_wishlist` (`wishlist`),
  FULLTEXT KEY `ftidx_subject_about` (`subject`,`about`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wishlist_ownergroups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wishlist` int(11) unsigned NOT NULL DEFAULT '0',
  `groupid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_wishlist` (`wishlist`),
  KEY `idx_groupid` (`groupid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wishlist_owners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wishlist` int(11) unsigned NOT NULL DEFAULT '0',
  `userid` int(11) unsigned NOT NULL DEFAULT '0',
  `type` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_wishlist` (`wishlist`),
  KEY `idx_userid` (`userid`),
  KEY `idx_type` (`type`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__wishlist_vote` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wishid` int(11) unsigned NOT NULL DEFAULT '0',
  `userid` int(11) unsigned NOT NULL DEFAULT '0',
  `voted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `importance` int(3) unsigned NOT NULL DEFAULT '0',
  `effort` int(3) NOT NULL DEFAULT '0',
  `due` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_wishid` (`wishid`),
  KEY `idx_userid` (`userid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xdomain_users` (
  `domain_id` int(11) NOT NULL,
  `domain_username` varchar(150) NOT NULL DEFAULT '',
  `uidNumber` int(11) DEFAULT NULL,
  PRIMARY KEY (`domain_id`,`domain_username`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xdomains` (
  `domain_id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`domain_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups` (
  `gidNumber` int(11) NOT NULL AUTO_INCREMENT,
  `cn` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `published` tinyint(3) DEFAULT '0',
  `approved` tinyint(3) DEFAULT '1',
  `type` tinyint(3) DEFAULT '0',
  `public_desc` text,
  `private_desc` text,
  `restrict_msg` text,
  `join_policy` tinyint(3) DEFAULT '0',
  `discoverability` tinyint(3) DEFAULT NULL,
  `discussion_email_autosubscribe` tinyint(3) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `plugins` text,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`gidNumber`),
  UNIQUE KEY `idx_cn` (`cn`),
  FULLTEXT KEY `ftidx_cn_description_public_desc` (`cn`,`description`,`public_desc`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000;

CREATE TABLE `#__xgroups_applicants` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_inviteemails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `gidNumber` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_invitees` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `userid` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `comments` text,
  `actorid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_managers` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_member_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `roleid` int(11) DEFAULT NULL,
  `uidNumber` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_memberoption` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `optionname` varchar(100) DEFAULT NULL,
  `optionvalue` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_members` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_modules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT '',
  `content` text,
  `position` varchar(50) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `state` int(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `approved` int(11) DEFAULT '1',
  `approved_on` datetime DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `checked_errors` int(11) DEFAULT '0',
  `scanned` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_modules_menu` (
  `moduleid` int(11) DEFAULT NULL,
  `pageid` int(11) DEFAULT NULL
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `category` int(11) DEFAULT NULL,
  `template` varchar(100) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT '1',
  `privacy` varchar(10) DEFAULT NULL,
  `home` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_pages_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `color` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_pages_checkout` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageid` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `when` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_pages_hits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `pageid` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_pages_versions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageid` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `content` longtext,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `approved` int(11) DEFAULT '1',
  `approved_on` datetime DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `checked_errors` int(11) DEFAULT '0',
  `scanned` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uidNumber` int(11) NOT NULL,
  `gidNumber` int(11) NOT NULL,
  `reason` text,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `permissions` text,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xgroups_tracperm` (
  `group_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  UNIQUE KEY `id` (`group_id`,`action`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xmessage` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `message` mediumtext,
  `subject` varchar(250) DEFAULT NULL,
  `component` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_component` (`component`),
  KEY `idx_group_id` (`group_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xmessage_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(20) NOT NULL DEFAULT '',
  `element` int(11) unsigned NOT NULL DEFAULT '0',
  `description` mediumtext,
  PRIMARY KEY (`id`),
  KEY `idx_class` (`class`),
  KEY `idx_element` (`element`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xmessage_component` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component` varchar(50) NOT NULL DEFAULT '',
  `action` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_component` (`component`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xmessage_notify` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `method` varchar(250) DEFAULT NULL,
  `type` varchar(250) DEFAULT NULL,
  `priority` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_method` (`method`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xmessage_recipient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `expires` datetime DEFAULT '0000-00-00 00:00:00',
  `actionid` int(11) DEFAULT '0',
  `state` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_mid` (`mid`),
  KEY `idx_uid` (`uid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xmessage_seen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `whenseen` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_mid` (`mid`),
  KEY `idx_uid` (`uid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xorganization_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(150) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xorganizations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  `mailPreferenceOption` int(11) NOT NULL DEFAULT '-1',
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
  `orcid` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`),
  KEY `idx_username` (`username`),
  FULLTEXT KEY `ftidx_givenName_surname` (`givenName`,`surname`),
  FULLTEXT KEY `ftidx_name` (`name`),
  FULLTEXT KEY `ftidx_fullname` (`givenName`,`middleName`,`surname`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_address` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uidNumber` int(11) DEFAULT NULL,
  `addressTo` varchar(200) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `addressCity` varchar(200) DEFAULT NULL,
  `addressRegion` varchar(200) DEFAULT NULL,
  `addressPostal` varchar(200) DEFAULT NULL,
  `addressCountry` varchar(200) DEFAULT NULL,
  `addressLatitude` float DEFAULT NULL,
  `addressLongitude` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_admin` (
  `uidNumber` int(11) NOT NULL,
  `admin` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`admin`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_bio` (
  `uidNumber` int(11) NOT NULL,
  `bio` text,
  PRIMARY KEY (`uidNumber`),
  FULLTEXT KEY `ftidx_bio` (`bio`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_dashboard_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uidNumber` int(11) unsigned NOT NULL,
  `preferences` text,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidNumber` (`uidNumber`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_disability` (
  `uidNumber` int(11) NOT NULL,
  `disability` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`disability`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_edulevel` (
  `uidNumber` int(11) NOT NULL,
  `edulevel` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`edulevel`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_hispanic` (
  `uidNumber` int(11) NOT NULL,
  `hispanic` varchar(255) NOT NULL,
  PRIMARY KEY (`uidNumber`,`hispanic`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_host` (
  `uidNumber` int(11) NOT NULL,
  `host` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`host`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_race` (
  `uidNumber` int(11) NOT NULL,
  `race` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`race`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__xprofiles_role` (
  `uidNumber` int(11) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`role`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  KEY `idx_ip` (`ip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__ysearch_plugin_weights` (
  `plugin` varchar(20) NOT NULL,
  `weight` float NOT NULL,
  PRIMARY KEY (`plugin`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__ysearch_site_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `link` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `ftidx_title_description` (`title`,`description`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  `params` text,
  `zone_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sessnum`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
  `zone_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sessnum`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `sessionpriv` (
  `privid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `privilege` varchar(40) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`privid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `venue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `venue` varchar(40) DEFAULT NULL,
  `state` varchar(15) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `mw_version` varchar(3) DEFAULT NULL,
  `ssh_key_path` varchar(200) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `master` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `view` (
  `viewid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `username` varchar(32) NOT NULL DEFAULT '',
  `remoteip` varchar(40) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `heartbeat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`viewid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `viewlog` (
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `username` varchar(32) NOT NULL DEFAULT '',
  `remoteip` varchar(40) NOT NULL DEFAULT '',
  `remotehost` varchar(40) NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `duration` float unsigned DEFAULT '0'
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zone_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_id` int(11) NOT NULL,
  `ipFROM` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000',
  `ipTO` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000',
  `continent` char(2) NOT NULL,
  `countrySHORT` char(2) NOT NULL,
  `countryLONG` varchar(64) NOT NULL,
  `ipREGION` varchar(128) NOT NULL,
  `ipCITY` varchar(128) NOT NULL,
  `ipLATITUDE` double DEFAULT NULL,
  `ipLONGITUDE` double DEFAULT NULL,
  `notes` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zone` varchar(40) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `state` varchar(15) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `master` varchar(255) DEFAULT NULL,
  `mw_version` varchar(3) DEFAULT NULL,
  `ssh_key_path` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__resource_contributors_view`
AS SELECT
   `m`.`uidNumber` AS `uidNumber`,count(`AA`.`authorid`) AS `count`
FROM ((`#__xprofiles` `m` left join `#__author_assoc` `AA` on(((`AA`.`authorid` = `m`.`uidNumber`) and (`AA`.`subtable` = _utf8'resources')))) join `#__resources` `R` on(((`R`.`id` = `AA`.`subid`) and (`R`.`published` = 1) and (`R`.`standalone` = 1)))) where (`m`.`public` = 1) group by `m`.`uidNumber`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__wiki_contributors_view`
AS SELECT
   `m`.`uidNumber` AS `uidNumber`,count(`w`.`id`) AS `count`
FROM (`#__xprofiles` `m` left join `#__wiki_page` `w` on(((`w`.`access` <> 1) and ((`w`.`created_by` = `m`.`uidNumber`) or ((`m`.`username` <> _utf8'') and (`w`.`authors` like concat(_utf8'%',`m`.`username`,_utf8'%'))))))) where ((`m`.`public` = 1) and (`w`.`id` is not null)) group by `m`.`uidNumber`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__contributor_ids_view`
AS SELECT
   `#__resource_contributors_view`.`uidNumber` AS `uidNumber`
FROM `#__resource_contributors_view` union select `#__wiki_contributors_view`.`uidNumber` AS `uidNumber` from `#__wiki_contributors_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__contributors_view`
AS SELECT
   `c`.`uidNumber` AS `uidNumber`,coalesce(`r`.`count`,0) AS `resource_count`,coalesce(`w`.`count`,0) AS `wiki_count`,(coalesce(`w`.`count`,0) + coalesce(`r`.`count`,0)) AS `total_count`
FROM ((`#__contributor_ids_view` `c` left join `#__resource_contributors_view` `r` on((`r`.`uidNumber` = `c`.`uidNumber`))) left join `#__wiki_contributors_view` `w` on((`w`.`uidNumber` = `c`.`uidNumber`)));

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__courses_form_latest_responses_view`
AS SELECT
   `fre`.`id` AS `id`,
   `fre`.`respondent_id` AS `respondent_id`,
   `fre`.`question_id` AS `question_id`,
   `fre`.`answer_id` AS `answer_id`
FROM `#__courses_form_responses` `fre` where ((select count(0) from `#__courses_form_responses` `frei` where ((`frei`.`respondent_id` = `fre`.`respondent_id`) and (`frei`.`id` > `fre`.`id`))) < (select count(distinct `frei`.`question_id`) from `#__courses_form_responses` `frei` where (`frei`.`respondent_id` = `fre`.`respondent_id`)));

INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
(1000,'com_answers','component','com_answers','',1,1,1,0,'','{\"infolink\":\"\\/kb\\/points\",\"notify_users\":\"\"}','','',0,'0000-00-00 00:00:00',0,0),
(1001,'com_billboards','component','com_billboards','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1002,'com_blog','component','com_blog','',1,1,1,0,'','{\"title\":\"\",\"uploadpath\":\"\\/site\\/blog\",\"show_authors\":\"1\",\"allow_comments\":\"1\",\"feeds_enabled\":\"1\",\"feed_entries\":\"partial\",\"show_date\":\"3\"}','','',0,'0000-00-00 00:00:00',0,0),
(1003,'com_citations','component','com_citations','',1,1,1,0,'','{\"citation_label\":\"number\",\"citation_rollover\":\"no\",\"citation_sponsors\":\"yes\",\"citation_import\":\"2\",\"citation_bulk_import\":\"2\",\"citation_download\":\"1\",\"citation_batch_download\":\"1\",\"citation_download_exclude\":\"\",\"citation_coins\":\"1\",\"citation_openurl\":\"1\",\"citation_url\":\"url\",\"citation_custom_url\":\"\",\"citation_cited\":\"0\",\"citation_cited_single\":\"\",\"citation_cited_multiple\":\"\",\"citation_show_tags\":\"no\",\"citation_allow_tags\":\"no\",\"citation_show_badges\":\"no\",\"citation_allow_badges\":\"no\",\"citation_format\":\"\"}','','',0,'0000-00-00 00:00:00',0,0),
(1004,'com_courses','component','com_courses','',1,1,1,0,'','{\"uploadpath\":\"\\/site\\/courses\",\"tmpl\":\"\",\"default_asset_groups\":\"Lectures, Activities, Exam\",\"auto_approve\":\"1\",\"email_comment_processing\":\"0\",\"email_member_coursesidcussionemail_autosignup\":\"0\",\"intro_mycourses\":\"1\",\"intro_interestingcourses\":\"1\",\"intro_popularcourses\":\"1\"}','','',0,'0000-00-00 00:00:00',0,0),
(1005,'com_cron','component','com_cron','',1,1,1,0,'',' ','','',0,'0000-00-00 00:00:00',0,0),
(1006,'com_events','component','com_events','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1008,'com_feedback','component','com_feedback','',1,1,1,0,'','{\"defaultpic\":\"\\/components\\/com_feedback\\/assets\\/img\\/contributor.gif\",\"uploadpath\":\"\\/site\\/quotes\",\"maxAllowed\":\"40000000\",\"file_ext\":\"jpg,jpeg,jpe,bmp,tif,tiff,png,gif\",\"blacklist\":\"\",\"badwords\":\"viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, texas holdem, fisting\"}','','',0,'0000-00-00 00:00:00',0,0),
(1009,'com_forum','component','com_forum','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1010,'com_groups','component','com_groups','',1,1,1,0,'','{\"ldapGroupMirror\":\"1\",\"ldapGroupLegacy\":\"1\",\"uploadpath\":\"\\/site\\/groups\",\"iconpath\":\"\\/components\\/com_groups\\/assets\\/img\\/icons\",\"join_policy\":\"0\",\"privacy\":\"0\",\"auto_approve\":\"1\",\"display_system_users\":\"no\",\"email_comment_processing\":\"1\",\"email_member_groupsidcussionemail_autosignup\":\"0\"}','','',0,'0000-00-00 00:00:00',0,0),
(1011,'com_help','component','com_help','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1012,'com_jobs','component','com_jobs','',1,1,1,0,'','{\"component_enabled\":\"1\",\"industry\":\"\",\"admingroup\":\"\",\"specialgroup\":\"jobsadmin\",\"autoapprove\":\"1\",\"defaultsort\":\"category\",\"jobslimit\":\"25\",\"maxads\":\"3\",\"allowsubscriptions\":\"1\",\"usonly\":\"0\",\"usegoogle\":\"0\",\"banking\":\"1\",\"promoline\":\"For a limited time: FREE Employer Services Basic subscription\",\"infolink\":\"kb\\/jobs\",\"premium_infolink\":\"\"}','','',0,'0000-00-00 00:00:00',0,0),
(1013,'com_kb','component','com_kb','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1014,'com_members','component','com_members','',1,1,1,0,'','{\"privacy\":\"1\",\"bankAccounts\":\"1\",\"defaultpic\":\"\\/components\\/com_members\\/assets\\/img\\/profile.gif\",\"webpath\":\"\\/site\\/members\",\"homedir\":\"\",\"maxAllowed\":\"40000000\",\"file_ext\":\"jpg,jpeg,jpe,bmp,tif,tiff,png,gif\",\"user_messaging\":\"1\",\"employeraccess\":\"0\",\"gidNumber\":\"3000\",\"gid\":\"public\",\"shadowMax\":\"120\",\"shadowMin\":\"0\",\"shadowWarning\":\"7\",\"LoginReturn\":\"\\/members\\/myaccount\",\"ConfirmationReturn\":\"\\/members\\/myaccount\",\"passwordMeter\":\"0\",\"registrationUsername\":\"RRUU\",\"registrationPassword\":\"RRUU\",\"registrationConfirmPassword\":\"RRUU\",\"registrationFullname\":\"RRUU\",\"registrationEmail\":\"RRUU\",\"registrationConfirmEmail\":\"RRUU\",\"registrationURL\":\"HOHO\",\"registrationPhone\":\"HOHO\",\"registrationEmployment\":\"HOHO\",\"registrationOrganization\":\"HOHO\",\"registrationCitizenship\":\"HHHR\",\"registrationResidency\":\"HHHR\",\"registrationSex\":\"HHHH\",\"registrationDisability\":\"HHHH\",\"registrationHispanic\":\"HHHH\",\"registrationRace\":\"HHHR\",\"registrationInterests\":\"HOHO\",\"registrationReason\":\"HOHO\",\"registrationOptIn\":\"HOHO\",\"registrationCAPTCHA\":\"RHHH\",\"registrationTOU\":\"RHRH\"}','','',0,'0000-00-00 00:00:00',0,0),
(1015,'com_newsletter','component','com_newsletter','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1016,'com_oaipmh','component','com_oaipmh','',1,1,1,1,'{}','{}','','',0,'0000-00-00 00:00:00',0,0),
(1017,'com_poll','component','com_poll','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1018,'com_dataviewer', 'component', 'com_dataviewer', '', '1', '1', '1', '0', '{\"legacy\":true,\"name\":\"Dataviewer\",\"type\":\"component\",\"creationDate\":\"2013-08-07\",\"author\":\"Sudheera R. Fernando\",\"copyright\":\"Copyright 2010-2012,2013 by Purdue University, West Lafayette, IN 47906\",\"authorEmail\":\"srf@xconsole.org\",\"authorUrl\":\"\",\"version\":\"2.0.2\",\"description\":\"Dataviewer for HUB Databases\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1019,'com_projects','component','com_projects','',1,1,1,1,'{}','component_on=1\ngrantinfo=0\nconfirm_step=0\nedit_settings=1\nrestricted_data=0\nrestricted_upfront=0\napprove_restricted=0\nprivacylink=/legal/privacy\nHIPAAlink=/legal/privacy\nFERPAlink=/legal/privacy\ncreatorgroup=\nadmingroup=projectsadmin\nsdata_group=hipaa_reviewers\nginfo_group=sps_reviewers\nmin_name_length=6\nmax_name_length=25\nreserved_names=clone, temp, test\nwebpath=/srv/projects\noffroot=1\ngitpath=/usr/bin/git\ngitclone=/site/projects/clone/.git\nmaxUpload=104857600\ndefaultQuota=1\npremiumQuota=1\napproachingQuota=90\npubQuota=1\npremiumPubQuota=1\nimagepath=/site/projects\ndefaultpic=/components/com_projects/assets/img/project.png\nimg_maxAllowed=5242880\nimg_file_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nlogging=0\nmessaging=1\nprivacy=1\nlimit=25\nsidebox_limit=3\ngroup_prefix=pr-\nuse_alias=1\ndocumentation=/projects/features\ndbcheck=1','','',0,'0000-00-00 00:00:00',0,0),
(1020,'com_publications','component','com_publications','',1,1,1,1,'{}','enabled=1\nautoapprove=1\nautoapproved_users=\nemail=0\ndefault_category=dataset\ndefaultpic=/components/com_publications/assets/img/resource_thumb.gif\ntoolpic=/components/com_publications/assets/img/tool_thumb.gif\nvideo_thumb=/components/com_publications/images/video_thumb.gif\ngallery_thumb=/components/com_publications/images/gallery_thumb.gif\nwebpath=/site/publications\naboutdoi=\ndoi_shoulder=\ndoi_prefix=\ndoi_service=\ndoi_userpw=\ndoi_xmlschema=\ndoi_publisher=\ndoi_resolve=http://dx.doi.org/\ndoi_verify=http://n2t.net/ezid/id/\nsupportedtag=\nsupportedlink=\ngoogle_id=\nshow_authors=1\nshow_ranking=1\nshow_rating=1\nshow_date=3\nshow_citation=1\npanels=content, description, authors, audience, gallery, tags, access, license, notes\nsuggest_licence=0\nshow_tags=1\nshow_metadata=1\nshow_notes=1\nshow_license=1\nshow_access=0\nshow_gallery=1\nshow_audience=0\naudiencelink=\ndocumentation=/kb/publications\ndeposit_terms=/legal/termsofdeposit\ndbcheck=0\nrepository=0\naip_path=/srv/AIP','','',0,'0000-00-00 00:00:00',0,0),
(1022,'com_resources','component','com_resources','',1,1,1,0,'','{\"autoapprove\":\"0\",\"autoapproved_users\":\"\",\"cc_license\":\"1\",\"email_when_approved\":\"0\",\"defaultpic\":\"\\/components\\/com_resources\\/images\\/resource_thumb.gif\",\"tagstool\":\"screenshots,poweredby,bio,credits,citations,sponsoredby,references,publications\",\"tagsothr\":\"bio,credits,citations,sponsoredby,references,publications\",\"accesses\":\"Public,Registered,Special,Protected,Private\",\"webpath\":\"\\/site\\/resources\",\"toolpath\":\"\\/site\\/resources\\/tools\",\"uploadpath\":\"\\/site\\/resources\",\"maxAllowed\":\"40000000\",\"file_ext\":\"jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz\",\"doi\":\"\",\"aboutdoi\":\"\",\"supportedtag\":\"\",\"supportedlink\":\"\",\"browsetags\":\"on\",\"google_id\":\"\",\"show_authors\":\"1\",\"show_assocs\":\"1\",\"show_ranking\":\"0\",\"show_rating\":\"1\",\"show_date\":\"3\",\"show_metadata\":\"1\",\"show_citation\":\"1\",\"show_audience\":\"0\",\"audiencelink\":\"\"}','','',0,'0000-00-00 00:00:00',0,0),
(1023,'com_services','component','com_services','',1,1,1,0,'','{\"autoapprove\":\"1\"}','','',0,'0000-00-00 00:00:00',0,0),
(1024,'com_store','component','com_store','',1,1,1,0,'','{\"store_enabled\":\"1\",\"webpath\":\"\\/site\\/store\",\"hubaddress_ln1\":\"\",\"hubaddress_ln2\":\"\",\"hubaddress_ln3\":\"\",\"hubaddress_ln4\":\"\",\"hubaddress_ln5\":\"\",\"hubemail\":\"\",\"hubphone\":\"\",\"headertext_ln1\":\"\",\"headertext_ln2\":\"\",\"footertext\":\"\",\"receipt_title\":\"Your Order at HUB Store\",\"receipt_note\":\"Thank You for contributing to our HUB!\"}','','',0,'0000-00-00 00:00:00',0,0),
(1025,'com_support','component','com_support','',1,1,1,0,'','{\"feed_summary\":\"0\",\"severities\":\"critical,major,normal,minor,trivial\",\"webpath\":\"\\/site\\/tickets\",\"maxAllowed\":\"40000000\",\"file_ext\":\"jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz\",\"group\":\"\",\"emails\":\"{config.mailfrom}\",\"0\":\"\",\"blacklist\":\"\",\"badwords\":\"viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, texas holdem, fisting\",\"email_processing\":\"1\"}','','',0,'0000-00-00 00:00:00',0,0),
(1026,'com_system','component','com_system','',1,1,1,0,'','{\"geodb_driver\":\"mysql\",\"geodb_host\":\"\",\"geodb_port\":\"\",\"geodb_user\":\"\",\"geodb_password\":\"\",\"geodb_database\":\"\",\"geodb_prefix\":\"\",\"ldap_primary\":\"ldap:\\/\\/127.0.0.1\",\"ldap_secondary\":\"\",\"ldap_basedn\":\"\",\"ldap_searchdn\":\"\",\"ldap_searchpw\":\"\",\"ldap_managerdn\":\"\",\"ldap_managerpw\":\"\",\"ldap_tls\":\"0\"}','','',0,'0000-00-00 00:00:00',0,0),
(1027,'com_tags','component','com_tags','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1028,'com_tools','component','com_tools','',1,1,1,0,'','{\"mw_on\":\"1\",\"mw_redirect\":\"\\/home\",\"stopRedirect\":\"index.php?option=com_members&task=myaccount\",\"mwDBDriver\":\"\",\"mwDBHost\":\"\",\"mwDBPort\":\"\",\"mwDBUsername\":\"\",\"mwDBPassword\":\"\",\"mwDBDatabase\":\"\",\"mwDBPrefix\":\"\",\"shareable\":\"1\",\"warn_multiples\":\"0\",\"storagehost\":\"tcp:\\/\\/localhost:300\",\"show_storage\":\"0\",\"params_whitelist\":\"\",\"contribtool_on\":\"1\",\"contribtool_redirect\":\"\\/home\",\"launch_ipad\":\"0\",\"admingroup\":\"apps\",\"default_mw\":\"narwhal\",\"default_vnc\":\"780x600\",\"developer_site\":\"Forge\",\"project_path\":\"\\/tools\\/\",\"invokescript_dir\":\"\\/apps\",\"dev_suffix\":\"_dev\",\"group_prefix\":\"app-\",\"sourcecodePath\":\"site\\/protected\\/source\",\"learn_url\":\"http:\\/\\/rappture.org\\/wiki\\/FAQ_UpDownloadSrc\",\"rappture_url\":\"http:\\/\\/rappture.org\",\"demo_url\":\"\",\"new_doi\":\"0\",\"doi_newservice\":\"\",\"doi_shoulder\":\"\",\"doi_newprefix\":\"\",\"doi_publisher\":\"\",\"doi_resolve\":\"http:\\/\\/dx.doi.org\\/\",\"doi_verify\":\"http:\\/\\/n2t.net\\/ezid\\/id\\/\",\"exec_pu\":\"1\",\"screenshot_edit\":\"0\",\"downloadable_on\":\"0\"}','','',0,'0000-00-00 00:00:00',0,0),
(1030,'com_usage','component','com_usage','',1,1,1,0,'','{\"statsDBDriver\":\"mysql\",\"statsDBHost\":\"localhost\",\"statsDBPort\":\"\",\"statsDBUsername\":\"\",\"statsDBPassword\":\"\",\"statsDBDatabase\":\"\",\"statsDBPrefix\":\"\",\"mapsApiKey\":\"\",\"stats_path\":\"\\/site\\/stats\",\"maps_path\":\"\\/site\\/stats\\/maps\",\"plots_path\":\"\\/site\\/stats\\/plots\",\"charts_path\":\"\\/site\\/stats\\/plots\"}','','',0,'0000-00-00 00:00:00',0,0),
(1031,'com_whatsnew','component','com_whatsnew','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1032,'com_wiki','component','com_wiki','',1,1,1,0,'','{\"subpage_separator\":\"\\/\",\"homepage\":\"MainPage\",\"max_pagename_length\":\"100\",\"filepath\":\"\\/site\\/wiki\",\"mathpath\":\"\\/site\\/wiki\\/math\",\"tmppath\":\"\\/site\\/wiki\\/tmp\",\"maxAllowed\":\"40000000\",\"img_ext\":\"jpg,jpeg,jpe,bmp,tif,tiff,png,gif\",\"file_ext\":\"jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz\",\"cache\":\"0\",\"cache_time\":\"15\"}','','',0,'0000-00-00 00:00:00',0,0),
(1033,'com_wishlist','component','com_wishlist','',1,1,1,0,'','{\"categories\":\"general, resource, group, user\",\"group\":\"hubdev\",\"banking\":\"1\",\"allow_advisory\":\"0\",\"votesplit\":\"0\",\"webpath\":\"\\/site\\/wishlist\",\"show_percentage_granted\":\"0\"}','','',0,'0000-00-00 00:00:00',0,0),
(1034,'com_search','component','com_search','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1035,'com_cart', 'component', 'com_cart', '', '1', '0', '1', '0', '{\"legacy\":true,\"name\":\"Cart\",\"type\":\"component\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Configure cart\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1036,'com_storefront', 'component', 'com_storefront', '', '1', '0', '1', '0', '', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1037,'com_collections', 'component', 'com_collections', '', 1, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(1038,'com_feedaggregator', 'component', 'com_feedaggregator', '', 1, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(1039,'com_update', 'component', 'com_update', '', 1, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(1040,'com_time', 'component', 'com_time', '', 1, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0);

INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
(1400,'Authentication - Facebook','plugin','facebook','authentication',0,0,1,0,'','app_id=\napp_secret=\n','','',0,'0000-00-00 00:00:00',2,0),
(1401,'Authentication - Google','plugin','google','authentication',0,0,1,0,'','app_id=\napp_secret=\n','','',0,'0000-00-00 00:00:00',3,0),
(1402,'Authentication - HUBzero','plugin','hubzero','authentication',0,1,1,0,'','{\"admin_login\":\"1\"}','','',0,'0000-00-00 00:00:00',1,0),
(1403,'Authentication - Linkedin','plugin','linkedin','authentication',0,0,1,0,'','api_key=\napp_secret=\n','','',0,'0000-00-00 00:00:00',4,0),
(1404,'Authentication - PUCAS','plugin','pucas','authentication',0,0,1,0,'','domain=Purdue Career Account (CAS)\ndisplay_name=Purdue Career\n\n','','',0,'0000-00-00 00:00:00',6,0),
(1405,'Authentication - Twitter','plugin','twitter','authentication',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',5,0),
(1406,'Citation - Bibtex','plugin','bibtex','citation',0,1,1,0,'','title_match_percent=90%\n\n','','',0,'0000-00-00 00:00:00',2,0),
(1407,'Citation - Default','plugin','default','citation',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0),
(1408,'Citation - Endnote','plugin','endnote','citation',0,1,1,0,'','custom_tags=badges-%=\ntags-%<\ntitle_match_percent=85%\n\n','','',0,'0000-00-00 00:00:00',3,0),
(1409,'Content - xHubTags','plugin','xhubtags','content',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',7,0),
(1410,'Courses - Announcements','plugin','announcements','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',7,0),
(1412,'Courses - Course Offerings','plugin','offerings','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',10,0),
(1413,'Courses - Course Overview','plugin','overview','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0),
(1414,'Courses - Course Related','plugin','related','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',12,0),
(1415,'Courses - Course Reviews','plugin','reviews','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',9,0),
(1416,'Courses - Dashboard','plugin','dashboard','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',8,0),
(1417,'Courses - Disucssions','plugin','discussions','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0),
(1418,'Courses - Guide','plugin','guide','courses',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',15,0),
(1419,'Courses - My Progress','plugin','progress','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0),
(1420,'Courses - Notes','plugin','notes','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',13,0),
(1421,'Courses - Outline','plugin','outline','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0),
(1422,'Courses - Pages','plugin','pages','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0),
(1423,'Courses - Store','plugin','store','courses',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',14,0),
(1425,'Cron - Cache','plugin','cache','cron',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0),
(1426,'Cron - Groups','plugin','groups','cron',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0),
(1427,'Cron - Members','plugin','members','cron',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0),
(1428,'Cron - Newsletter','plugin','newsletter','cron',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0),
(1429,'Cron - Support','plugin','support','cron',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0),
(1430,'Groups - Announcements', 'plugin', 'announcements', 'groups', 0, 1, 1, 0, '', 'plugin_access=members\ndisplay_tab=1', '', '', 0, '0000-00-00 00:00:00', 2, 0),
(1431,'Groups - Blog', 'plugin', 'blog', 'groups', 0, 1, 1, 0, '', 'uploadpath=/site/groups/{{gid}}/blog\nposting=0\nfeeds_enabled=0\nfeed_entries=partial', '', '', 0, '0000-00-00 00:00:00', 3, 0),
(1432,'Groups - Calendar', 'plugin', 'calendar', 'groups', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 4, 0),
(1433,'Groups - Forum', 'plugin', 'forum', 'groups', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 7, 0),
(1434,'Groups - Member Options', 'plugin', 'memberoptions', 'groups', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 8, 0),
(1435,'Groups - Members', 'plugin', 'members', 'groups', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(1436,'Groups - Messages', 'plugin', 'messages', 'groups', 0, 1, 1, 0, '', '{\"limit\":50,\"display_tab\":0}', '', '', 0, '0000-00-00 00:00:00', 9, 0),
(1437,'Groups - Projects', 'plugin', 'projects', 'groups', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 10, 0),
(1438,'Groups - Resources', 'plugin', 'resources', 'groups', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 11, 0),
(1439,'Groups - Usage', 'plugin', 'usage', 'groups', 0, 0, 1, 0, '', 'uploadpath=/site/groups/{{gid}}/blog\nposting=0\nfeeds_enabled=0\nfeed_entries=partial', '', '', 0, '0000-00-00 00:00:00', 12, 0),
(1440,'Groups - Wiki', 'plugin', 'wiki', 'groups', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 13, 0),
(1441,'Groups - Wishlist', 'plugin', 'wishlist', 'groups', 0, 1, 1, 0, '', 'limit=50', '', '', 0, '0000-00-00 00:00:00', 14, 0),
(1442,'HUBzero - Autocompleter','plugin','autocompleter','hubzero',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0),
(1443,'HUBzero - Comments','plugin','comments','hubzero',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',7,0),
(1444,'plg_wiki_parserdefault','plugin','parserdefault','wiki',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0),
(1445,'plg_wiki_editortoolbar','plugin','editortoolbar','wiki',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0),
(1446,'plg_wiki_editorwykiwyg','plugin','editorwykiwyg','wiki',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',8,0),
(1447,'HUBzero - Image CAPTCHA','plugin','imagecaptcha','hubzero',0,1,1,0,'','bgColor=#ffffff\ntextColor=#2c8007\nimageFunction=Adv\n','','',0,'0000-00-00 00:00:00',4,0),
(1448,'HUBzero - Math CAPTCHA','plugin','mathcaptcha','hubzero',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',5,0),
(1449,'HUBzero - ReCAPTCHA','plugin','recaptcha','hubzero',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',6,0),
(1450,'Members - Account', 'plugin', 'account', 'members', 0, 1, 1, 0, '', 'ssh_key_upload=0\n\n', '', '', 0, '0000-00-00 00:00:00', 3, 0),
(1451,'Members - Blog', 'plugin', 'blog', 'members', 0, 1, 1, 0, '', 'uploadpath=/site/members/{{uid}}/blog\nfeeds_enabled=0\nfeed_entries=partial', '', '', 0, '0000-00-00 00:00:00', 4, 0),
(1452,'Members - Contributions', 'plugin', 'contributions', 'members', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 6, 0),
(1453,'Members - Contributions - Resources', 'plugin', 'resources', 'members', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 7, 0),
(1454,'Members - Contributions - Topics', 'plugin', 'wiki', 'members', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 8, 0),
(1455,'Members - Courses', 'plugin', 'courses', 'members', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 9, 0),
(1456,'Members - Dashboard', 'plugin', 'dashboard', 'members', 0, 1, 1, 0, '', '{\"allow_customization\":\"1\",\"position\":\"memberDashboard\",\"defaults\":\"[{\\\"module\\\":44,\\\"col\\\":1,\\\"row\\\":1,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":35,\\\"col\\\":1,\\\"row\\\":3,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":38,\\\"col\\\":1,\\\"row\\\":5,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":39,\\\"col\\\":1,\\\"row\\\":7,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":33,\\\"col\\\":2,\\\"row\\\":1,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":42,\\\"col\\\":2,\\\"row\\\":3,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":34,\\\"col\\\":2,\\\"row\\\":5,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":41,\\\"col\\\":3,\\\"row\\\":1,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":36,\\\"col\\\":3,\\\"row\\\":3,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":37,\\\"col\\\":3,\\\"row\\\":5,\\\"size_x\\\":1,\\\"size_y\\\":2}]\"}', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(1458,'Members - Groups', 'plugin', 'groups', 'members', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 10, 0),
(1460,'Members - Messages', 'plugin', 'messages', 'members', 0, 1, 1, 0, '', 'default_method=email\n\n', '', '', 0, '0000-00-00 00:00:00', 12, 0),
(1461,'Members - Points', 'plugin', 'points', 'members', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 13, 0),
(1462,'Members - Profile', 'plugin', 'profile', 'members', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 0),
(1463,'Members - Projects', 'plugin', 'projects', 'members', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 14, 0),
(1464,'Members - Resume', 'plugin', 'resume', 'members', 0, 1, 1, 0, '', 'limit=50', '', '', 0, '0000-00-00 00:00:00', 15, 0),
(1465,'Members - Usage', 'plugin', 'usage', 'members', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 16, 0),
(1466,'Projects - Blog','plugin','blog','projects',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0),
(1467,'Projects - Files','plugin','files','projects',0,1,1,0,'','maxUpload=104857600\nmaxDownload=1048576\nreservedNames=google , dropbox, shared, temp\nconnectedProjects=\nenable_google=0\ngoogle_clientId=\ngoogle_clientSecret=\ngoogle_appKey=\ngoogle_folder=Google\nsync_lock=0\nauto_sync=1\nlatex=1\ntexpath=/usr/bin/\ngspath=/usr/bin/','','',0,'0000-00-00 00:00:00',3,0),
(1468,'Projects - Notes','plugin','notes','projects',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0),
(1469,'Projects - Publications','plugin','publications','projects',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0),
(1470,'Projects - Team','plugin','team','projects',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0),
(1471,'Projects - Todo','plugin','todo','projects',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0),
(1472,'Resources - About','plugin','about','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0),
(1473,'Resources - About (tool)','plugin','abouttool','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',9,0),
(1474,'Resources - Citations','plugin','citations','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',10,0),
(1476,'Resources - Questions','plugin','questions','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',11,0),
(1477,'Resources - Recommendations','plugin','recommendations','resources',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',2,0),
(1478,'Resources - Related','plugin','related','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0),
(1479,'Resources - Reviews','plugin','reviews','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0),
(1480,'Resources - Share','plugin','share','resources',0,1,1,0,'','icons_limit=3\nshare_facebook=1\nshare_twitter=1\nshare_google=1\nshare_digg=1\nshare_technorati=1\nshare_delicious=1\nshare_reddit=0\nshare_email=0\nshare_print=0\n\n','','',0,'0000-00-00 00:00:00',8,0),
(1481,'Resources - Sponsors','plugin','sponsors','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',12,0),
(1482,'Resources - Supporting Documents','plugin','supportingdocs','resources',0,1,1,0,'','display_limit=50','','',0,'0000-00-00 00:00:00',13,0),
(1483,'Resources - Usage','plugin','usage','resources',0,1,1,0,'','{\"period\":\"14\",\"chart_path\":\"\\/site\\/stats\\/chart_resources\\/\",\"map_path\":\"\\/site\\/stats\\/resource_maps\\/\"}','','',0,'0000-00-00 00:00:00',5,0),
(1484,'Resources - Versions','plugin','versions','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0),
(1485,'Resources - Wishlist','plugin','wishlist','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',14,0),
(1486,'Support - Answers','plugin','answers','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0),
(1487,'Support - Blog','plugin','blog','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0),
(1488,'Support - CAPTCHA','plugin','captcha','support',0,1,1,0,'','modCaptcha=text\ncomCaptcha=image\nbgColor=#2c8007\ntextColor=#ffffff\nimageFunction=Adv\n','','',0,'0000-00-00 00:00:00',7,0),
(1489,'Support - Comments','plugin','comments','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0),
(1490,'Support - Knowledgebase Comments','plugin','kb','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',8,0),
(1491,'Support - Resources','plugin','resources','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0),
(1492,'Support - Transfer','plugin','transfer','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0),
(1493,'Support - Wishlist','plugin','wishlist','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0),
(1494,'System - HUBzero','plugin','hubzero','system',0,1,1,0,'','search=search\n\n','','',0,'0000-00-00 00:00:00',9,0),
(1495,'System - xFeed','plugin','xfeed','system',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',10,0),
(1496,'System - Disable Cache','plugin','disablecache','system',0,1,1,0,'','definitions=/about/contact\nreenable_afterdispatch=0\n\n','','',0,'0000-00-00 00:00:00',11,0),
(1497,'System - JQuery','plugin','jquery','system','0','1','1','1','','{\"jquery\":\"1\",\"jqueryVersion\":\"1.7.2\",\"jquerycdnpath\":\"\\/\\/ajax.googleapis.com\\/ajax\\/libs\\/jquery\\/1.7.2\\/jquery.min.js\",\"jqueryui\":\"1\",\"jqueryuiVersion\":\"1.8.6\",\"jqueryuicdnpath\":\"\\/\\/ajax.googleapis.com\\/ajax\\/libs\\/jqueryui\\/1.8.6\\/jquery-ui.min.js\",\"jqueryuicss\":\"0\",\"jqueryuicsspath\":\"\\/plugins\\/system\\/jquery\\/css\\/jquery-ui-1.8.6.custom.css\",\"jquerytools\":\"1\",\"jquerytoolsVersion\":\"1.2.5\",\"jquerytoolscdnpath\":\"http:\\/\\/cdn.jquerytools.org\\/1.2.5\\/all\\/jquery.tools.min.js\",\"jqueryfb\":\"1\",\"jqueryfbVersion\":\"2.0.4\",\"jqueryfbcdnpath\":\"\\/\\/fancyapps.com\\/fancybox\\/\",\"jqueryfbcss\":\"1\",\"jqueryfbcsspath\":\"\\/media\\/system\\/css\\/jquery.fancybox.css\",\"activateSite\":\"1\",\"noconflictSite\":\"0\",\"activateAdmin\":\"0\",\"noconflictAdmin\":\"0\"}', '', '', '1000', '2013-09-01 14:26:58', '12', '0'),
(1498,'Tags - Answers','plugin','answers','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',7,0),
(1499,'Tags - Blogs','plugin','blogs','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',9,0),
(1500,'Tags - Citations','plugin','citations','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',10,0),
(1501,'Tags - Events','plugin','events','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0),
(1502,'Tags - Forum','plugin','forum','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',8,0),
(1503,'Tags - Groups','plugin','groups','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0),
(1504,'Tags - Knowledgebase','plugin','kb','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',11,0),
(1505,'Tags - Members','plugin','members','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0),
(1506,'Tags - Resources','plugin','resources','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0),
(1507,'Tags - Support','plugin','support','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0),
(1508,'Tags - Topics','plugin','wiki','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0),
(1509,'Usage - Domains','plugin','domains','usage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',6,0),
(1510,'Usage - Domain Class','plugin','domainclass','usage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',1,0),
(1511,'Usage - Maps','plugin','maps','usage',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',7,0),
(1512,'Usage - Overview','plugin','overview','usage',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0),
(1513,'Usage - Region','plugin','region','usage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',8,0),
(1514,'Usage - Partners','plugin','partners','usage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',3,0),
(1515,'Usage - Tools','plugin','tools','usage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',4,0),
(1516,'User - xHUB','plugin','xusers','user',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0),
(1517,'User - LDAP','plugin','ldap','user',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',5,0),
(1518,'User - Constant Contact','plugin','constantcontact','user',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0),
(1519,'Whatsnew - Content','plugin','content','whatsnew',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0),
(1520,'Whatsnew - Events','plugin','events','whatsnew',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0),
(1521,'Whatsnew - Knowledge Base','plugin','kb','whatsnew',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0),
(1522,'Whatsnew - Resources','plugin','resources','whatsnew',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0),
(1523,'Whatsnew - Topics','plugin','wiki','whatsnew',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0),
(1524,'XMessage - RSS','plugin','rss','xmessage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',4,0),
(1525,'XMessage - Internal','plugin','internal','xmessage',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0),
(1526,'XMessage - SMS TXT','plugin','smstxt','xmessage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',3,0),
(1527,'XMessage - Instant Message','plugin','im','xmessage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',2,0),
(1528,'XMessage - Handler','plugin','handler','xmessage',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0),
(1529,'XMessage - Email','plugin','email','xmessage',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1530,'plg_search_blogs','plugin','blogs','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1531,'plg_search_citations','plugin','citations','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1532,'plg_search_content','plugin','content','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1533,'plg_search_weighttitle','plugin','weighttitle','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1534,'plg_search_events','plugin','events','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1535,'plg_search_forum','plugin','forum','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1536,'plg_search_groups','plugin','groups','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1537,'plg_search_kb','plugin','kb','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1538,'plg_search_members','plugin','members','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1539,'plg_search_questions and Answers','plugin','questions','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1540,'plg_search_resources','plugin','resources','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1541,'plg_search_sitemap','plugin','sitemap','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1542,'plg_search_sortcourses','plugin','sortcourses','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1543,'plg_search_sortevents','plugin','sortevents','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1544,'plg_search_suffixes','plugin','suffixes','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1545,'plg_search_wiki','plugin','wiki','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1546,'plg_search_weightcontributor','plugin','weightcontributor','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1547,'plg_search_weighttools','plugin','weighttools','search',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1548,'plg_search_wishlists','plugin','wishlists','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0),
(1549,'plg_content_collect', 'plugin', 'collect', 'content', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Content - Collect\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2013 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Display a link allowing a resource to be favorited\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1550,'plg_resources_collect', 'plugin', 'collect', 'resources', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Resource - Collect\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2013 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Display a link allowing a resource to be favorited\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1551,'plg_wiki_collect', 'plugin', 'collect', 'wiki', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Wiki - Collect\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Display a link allowing a wiki page to be favorited\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1554,'plg_support_forum', 'plugin', 'forum', 'support', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Support - Forum Abuse reports\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2013 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Various functions for the Report Abuse Component\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1555,'plg_courses_memberoptions', 'plugin', 'memberoptions', 'courses', '0', '0', '1', '0', '{\"legacy\":true,\"name\":\"Courses - Member options\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2012 by Purdue Research Foundation, West Lafayette, IN 47906\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Display a course\'s member options\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1558,'plg_cron_users', 'plugin', 'users', 'cron', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Cron - Users\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2013 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Cron events for users\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1559,'plg_groups_collections', 'plugin', 'collections', 'groups', 0, 1, 1, 0, '{\"legacy\":false,\"name\":\"Groups - Collections\",\"type\":\"plugin\",\"creationDate\":\"December 2012\",\"author\":\"Shawn Rice\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"zooley@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1.5\",\"description\":\"Display collections\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 5, 0),
(1560,'plg_members_collections', 'plugin', 'collections', 'members', 0, 1, 1, 0, '{\"legacy\":false,\"name\":\"Members - Collections\",\"type\":\"plugin\",\"creationDate\":\"December 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2013 Purdue University. All rights reserved.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.5\",\"description\":\"Display collections\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 5, 0),
(1561,'plg_projects_databases', 'plugin', 'databases', 'projects', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Projects - Databases\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"Sudheera R. Fernando\",\"copyright\":\"Copyright (C) 2013 by Purdue Research Foundation, West Lafayette, IN 47906.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Databases for Projects environment\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1562,'plg_publications_citations', 'plugin', 'citations', 'publications', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Publication - Citations\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"Alissa Nedossekina\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays citations for a publication\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1564,'plg_publications_questions', 'plugin', 'questions', 'publications', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Publication - Questions\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"Alissa Nedossekina\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays questions related to a publication (by tag)\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1565,'plg_publications_recommendations', 'plugin', 'recommendations', 'publications', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Publication - Recommendations\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"Alissa Nedossekina\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays recommendations for a publication\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1566,'plg_publications_related', 'plugin', 'related', 'publications', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Publication - Related\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"Alissa Nedossekina\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays related publication\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1567,'plg_publications_reviews', 'plugin', 'reviews', 'publications', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Publication - Reviews\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"Alissa Nedossekina\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays reviews for a publication\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1568,'plg_publications_share', 'plugin', 'share', 'publications', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Publication - Share\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"Alissa Nedossekina\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Display options to post publication link on Facebbok, Twitter etc.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1569,'plg_publications_supportingdocs', 'plugin', 'supportingdocs', 'publications', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Publication - supportingdocs\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"Alissa Nedossekina\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays supporting docs for a publication\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1570,'plg_publications_usage', 'plugin', 'usage', 'publications', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Publication - Usage\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"Alissa Nedossekina\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays usage info for a publication\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1571,'plg_publications_versions', 'plugin', 'versions', 'publications', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Publication - versions\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"Alissa Nedossekina\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays all versions of a publication\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1572,'plg_publications_wishlist', 'plugin', 'wishlist', 'publications', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Publication - Wishlist\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"Alissa Nedossekina\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays publication wishlist\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1573,'plg_resources_groups', 'plugin', 'groups', 'resources', '0', '1', '1', '0', '{\"legacy\":false,\"name\":\"Resource - Group\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"Shawn Rice\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Display group ownership for a resource\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1574,'plg_system_indent', 'plugin', 'indent', 'system', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"System - Indent\",\"type\":\"plugin\",\"creationDate\":\"March 2012\",\"author\":\"Shawn Rice\",\"copyright\":\"Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.\",\"authorEmail\":\"zooley@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1.5\",\"description\":\"Indent HTML correctly\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1575,'plg_system_mobile', 'plugin', 'mobile', 'system', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"System - Mobile\",\"type\":\"plugin\",\"creationDate\":\"December 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) Purdue University, 2013. All rights reserved\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"1\",\"description\":\"PLG_SYSTEM_MOBILE_DESC\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1582,'plg_courses_syllabus', 'plugin', 'syllabus', 'courses', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 16, 0),
(1584,'plg_courses_faq', 'plugin', 'faq', 'courses', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 18, 0),
(1585,'plg_search_courses', 'plugin', 'courses', 'search', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(1586,'plg_search_collections', 'plugin', 'collections', 'search', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 0),
(1587,'plg_search_projects', 'plugin', 'projects', 'search', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 3, 0),
(1588,'plg_search_publications', 'plugin', 'publications', 'search', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 4, 0),
(1589,'plg_cron_courses', 'plugin', 'courses', 'cron', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 6, 0),
(1590,'plg_editors_ckeditor', 'plugin', 'ckeditor', 'editors', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 4, 0),
(1591,'plg_system_supergroup', 'plugin', 'supergroup', 'system', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 13, 0),
(1592,'plg_geocode_arcgisonline', 'plugin', 'arcgisonline', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(1593,'plg_geocode_baidu', 'plugin', 'baidu', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 0),
(1594,'plg_geocode_bingmaps', 'plugin', 'bingmaps', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 3, 0),
(1595,'plg_geocode_cloudmade', 'plugin', 'cloudmade', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 4, 0),
(1596,'plg_geocode_datasciencetoolkit', 'plugin', 'datasciencetoolkit', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 5, 0),
(1597,'plg_geocode_freegeoip', 'plugin', 'freegeoip', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 6, 0),
(1598,'plg_geocode_geocoderca', 'plugin', 'geocoderca', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 7, 0),
(1599,'plg_geocode_geocoderus', 'plugin', 'geocoderus', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 8, 0),
(1600,'plg_geocode_geoip', 'plugin', 'geoip', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 9, 0),
(1601,'plg_geocode_geoips', 'plugin', 'geoips', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 10, 0),
(1602,'plg_geocode_geonames', 'plugin', 'geonames', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 11, 0),
(1603,'plg_geocode_geoplugin', 'plugin', 'geoplugin', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 12, 0),
(1604,'plg_geocode_googlemaps', 'plugin', 'googlemaps', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 13, 0),
(1605,'plg_geocode_googlemapsbusiness', 'plugin', 'googlemapsbusiness', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 14, 0),
(1606,'plg_geocode_hostip', 'plugin', 'hostip', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 15, 0),
(1607,'plg_geocode_ignopenls', 'plugin', 'ignopenls', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 16, 0),
(1608,'plg_geocode_ipgeobase', 'plugin', 'ipgeobase', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 17, 0),
(1609,'plg_geocode_ipinfodb', 'plugin', 'ipinfodb', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 18, 0),
(1610,'plg_geocode_local', 'plugin', 'local', 'geocode', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 19, 0),
(1611,'plg_geocode_mapquest', 'plugin', 'mapquest', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 20, 0),
(1612,'plg_geocode_maxmind', 'plugin', 'maxmind', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 21, 0),
(1613,'plg_geocode_maxmindbinary', 'plugin', 'maxmindbinary', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 22, 0),
(1614,'plg_geocode_nominatim', 'plugin', 'nominatim', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 23, 0),
(1615,'plg_geocode_oiorest', 'plugin', 'oiorest', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 24, 0),
(1616,'plg_geocode_openstreetmap', 'plugin', 'openstreetmap', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 25, 0),
(1617,'plg_geocode_tomtom', 'plugin', 'tomtom', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 26, 0),
(1618,'plg_geocode_yandex', 'plugin', 'yandex', 'geocode', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 27, 0),
(1619,'plg_resources_findthistext', 'plugin', 'findthistext', 'resources', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 15, 0),
(1620,'plg_cron_projects', 'plugin', 'projects', 'cron', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 7, 0),
(1621,'plg_cron_publications', 'plugin', 'publications', 'cron', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 8, 0),
(1622,'plg_content_formatwiki', 'plugin', 'formatwiki', 'content', 0, 1, 1, 0, '', '{\"applyFormat\":\"1\",\"convertFormat\":\"0\"}', '', '', 0, '0000-00-00 00:00:00', 8, 0),
(1623,'plg_content_formathtml', 'plugin', 'formathtml', 'content', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 9, 0),
(1624,'plg_editors_wikitoolbar', 'plugin', 'wikitoolbar', 'editors', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 5, 0),
(1625,'plg_editors_wikiwyg', 'plugin', 'wikiwyg', 'editors', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 6, 0),
(1626,'plg_projects_links', 'plugin', 'links', 'projects', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 7, 0),
(1627,'plg_groups_courses', 'plugin', 'courses', 'groups', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 6, 0),
(1628,'plg_support_publications', 'plugin', 'publications', 'support', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 9, 0),
(1629,'plg_tags_publications', 'plugin', 'publications', 'tags', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 12, 0),
(1630,'plg_hubzero_systemplate', 'plugin', 'systemplate', 'hubzero', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 8, 0),
(1631,'plg_hubzero_systickets', 'plugin', 'systickets', 'hubzero', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 9, 0),
(1632,'plg_hubzero_sysusers', 'plugin', 'sysusers', 'hubzero', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 10, 0),
(1633,'plg_support_time', 'plugin', 'time', 'support', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 10, 0),
(1634,'plg_content_akismet', 'plugin', 'akismet', 'content', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 10, 0),
(1635,'plg_content_mollom', 'plugin', 'mollom', 'content', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 11, 0),
(1636,'plg_content_spamassassin', 'plugin', 'spamassassin', 'content', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 12, 0),
(1637,'plg_members_impact', 'plugin', 'impact', 'members', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 11, 0),
(1638,'plg_publications_groups', 'plugin', 'groups', 'publications', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(1639,'plg_support_wiki', 'plugin', 'wiki', 'support', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 11, 0),
(1640,'plg_time_summary', 'plugin', 'summary', 'time', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(1641,'plg_tools_java', 'plugin', 'java', 'tools', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(1642,'plg_tools_novnc', 'plugin', 'novnc', 'tools', 0, 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 0),
(1643,'plg_whatsnew_publications', 'plugin', 'publications', 'whatsnew', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 6, 0);

INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
(1700, 'hubbasic', 'template', 'hubbasic', '', 0, 1, 1, 0, '{}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(1701, 'hubbasic2012', 'template', 'hubbasic2012', '', 0, 1, 1, 0, '{}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(1702, 'hubbasic2013', 'template', 'hubbasic2013', '', 0, 1, 1, 0, '{}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(1703, 'welcome', 'template', 'welcome', '', 0, 1, 1, 0, '{}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(1704, 'hubbasicadmin', 'template', 'hubbasicadmin', '', 1, 1, 1, 0, '{}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(1705, 'kameleon (admin)', 'template', 'kameleon', '', 1, 1, 1, 0, '{}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0);

INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
(1200, 'mod_announcements', 'module', 'mod_announcements', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Announcements Display\",\"type\":\"module\",\"creationDate\":\"May 2010\",\"author\":\"HUBzero\",\"copyright\":\"\",\"authorEmail\":\"alisa@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1.0.0\",\"description\":\"This module allows the display of announcements\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1201, 'mod_application_env', 'module', 'mod_application_env', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Application Environment\",\"type\":\"module\",\"creationDate\":\"April 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.\",\"authorEmail\":\"zooley@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"This module displays the current application environment (production, stage, testing, development)\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1202, 'mod_billboards', 'module', 'mod_billboards', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Billboards\",\"type\":\"module\",\"creationDate\":\"November 2011\",\"author\":\"HUBzero\",\"copyright\":\"\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"1.0\",\"description\":\"Rotate through billboards of content\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1203, 'mod_events_cal', 'module', 'mod_events_cal', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Events Calendar\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays a calendar with days that have events linked. Requires events component.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1204, 'mod_events_latest', 'module', 'mod_events_latest', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Latest Events\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays a list of upcoming events.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1205, 'mod_featuredblog', 'module', 'mod_featuredblog', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Featured Blog\",\"type\":\"module\",\"creationDate\":\"November 2010\",\"author\":\"HUBzero\",\"copyright\":\"(C) 2000 - 2004 Miro International Pty Ltd\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"4.5.1\",\"description\":\"This module randomly displays a featured blog entry.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1206, 'mod_featuredmember', 'module', 'mod_featuredmember', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Featured Member\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module randomly displays a featured member or contributor.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1207, 'mod_featuredquestion', 'module', 'mod_featuredquestion', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Featured Question\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module randomly displays a featured question.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1208, 'mod_featuredresource', 'module', 'mod_featuredresource', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Featured Resource\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module randomly displays a featured resource.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1209, 'mod_feed_youtube', 'module', 'mod_feed_youtube', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"YouTube Feed Display\",\"type\":\"module\",\"creationDate\":\"April 2010\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"1.5.0\",\"description\":\"This module allows to display a youtube playlist feed\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1210, 'mod_findresources', 'module', 'mod_findresources', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Find Resources\",\"type\":\"module\",\"creationDate\":\"Sep 2009\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"alisa@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"Module to display resources search, popular tags and categories.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1211, 'mod_googleanalytics', 'module', 'mod_googleanalytics', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Google Analytics\",\"type\":\"module\",\"creationDate\":\"April 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.\",\"authorEmail\":\"zooley@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"This module adds some Javascript to the page for Google Analytics reporting\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1212, 'mod_hubzilla', 'module', 'mod_hubzilla', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Hubzilla\",\"type\":\"module\",\"creationDate\":\"August 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.\",\"authorEmail\":\"zooley@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"Hubzilla attack!\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1213, 'mod_incremental_registration', 'module', 'mod_incremental_registration', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Incremental Registration\",\"type\":\"module\",\"creationDate\":\"April 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.\",\"authorEmail\":\"zooley@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"This module displays a page curl for enticing users to incrementally register demographics.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1214, 'mod_latestblog', 'module', 'mod_latestblog', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Latest Blog posts\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows the latest blog posts in the site blog as well as group blogs.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1215, 'mod_latestdiscussions', 'module', 'mod_latestdiscussions', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Latest Discussions\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows the latest discussions in the site forum as well as the group forum.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1216, 'mod_latestgroups', 'module', 'mod_latestgroups', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Latest Groups\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2013 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows the latest discussions in the site forum as well as the group forum.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1217, 'mod_latestusage', 'module', 'mod_latestusage', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Latest Usage\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module displays the latest usage numbers.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1218, 'mod_logjserrors', 'module', 'mod_logjserrors', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Log JS Errors\",\"type\":\"module\",\"creationDate\":\"July 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"1.5.0\",\"description\":\"Logs js errors\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1219, 'mod_megamenu', 'module', 'mod_megamenu', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Mega Menu\",\"type\":\"module\",\"creationDate\":\"Feb 2012\",\"author\":\"Shawn Rice\",\"copyright\":\"Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.\",\"authorEmail\":\"zooley@purdue.edu\",\"authorUrl\":\"hubzero.org\",\"version\":\"1.5.0\",\"description\":\"Displays a menu with mega menu option.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1220, 'mod_mycontributions', 'module', 'mod_mycontributions', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"My Contributions\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2013 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a list of contributions\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1221, 'mod_mycourses', 'module', 'mod_mycourses', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"My Courses\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a list of courses the user belongs to and their status in it\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1223, 'mod_mygroups', 'module', 'mod_mygroups', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"My Groups\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a list of groups the user belongs to and their status in it\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1224, 'mod_mymessages', 'module', 'mod_mymessages', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"My Messages\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a list of unread messages sent by the site.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1225, 'mod_mypoints', 'module', 'mod_mypoints', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"My Points\",\"type\":\"module\",\"creationDate\":\"October 2009\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"zooley@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1\",\"description\":\"This module will display a point total and list of most recent point transactions.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1226, 'mod_myprojects', 'module', 'mod_myprojects', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"My Projects\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Alissa Nedossekina\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module displays a list of projects the user belongs, their role in the project and the number of updates since last visit.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1227, 'mod_myquestions', 'module', 'mod_myquestions', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"My Questions\",\"type\":\"module\",\"creationDate\":\"Jan 2009\",\"author\":\"snowwitje\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"alisa@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1\",\"description\":\"This module will display a list of questions submitted by the user, as well as those user can answer.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1228, 'mod_myresources', 'module', 'mod_myresources', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"My Resources\",\"type\":\"module\",\"creationDate\":\"January 2011\",\"author\":\"HUBzero\",\"copyright\":\"(C) 2011 HUBzero\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"1\",\"description\":\"This module will display a list of publications (resources, wiki pages, etc.)\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1229, 'mod_mysessions', 'module', 'mod_mysessions', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"My Sessions\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Christopher Smoak\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows a list of the user\'s active tool sessions.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1230, 'mod_mysubmissions', 'module', 'mod_mysubmissions', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"My Submissions\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Shows a list of submissions (resources) in progress.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1231, 'mod_mytickets', 'module', 'mod_mytickets', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"My Tickets\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Shawn Rice\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a list of active support tickets submitted by the user\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1232, 'mod_mytools', 'module', 'mod_mytools', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"My Tools\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows a list of the user\'s favorite tools, recently used tools, and all available tools.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1233, 'mod_mywishes', 'module', 'mod_mywishes', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"My Wishes\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a list of open wishes submitted by\\/ assigned to the user\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1234, 'mod_newsletter', 'module', 'mod_newsletter', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Newsletter\",\"type\":\"module\",\"creationDate\":\"August 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.\",\"authorEmail\":\"csmoak@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1.0.0\",\"description\":\"Newsletter Mailing List Sign up\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1235, 'mod_notices', 'module', 'mod_notices', '', '0', '0', '1', '0', '{\"legacy\":true,\"name\":\"Notices Module\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows a notice (when site will be down, etc.) box for site visitors.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1236, 'mod_poll', 'module', 'mod_poll', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Poll\",\"type\":\"module\",\"creationDate\":\"July 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"1.5.0\",\"description\":\"DESCPOLL\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1237, 'mod_polltitle', 'module', 'mod_polltitle', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"XPoll Title\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows the most popular FAQs.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1238, 'mod_popularfaq', 'module', 'mod_popularfaq', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Popular FAQs\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows the most popular FAQs.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1239, 'mod_popularquestions', 'module', 'mod_popularquestions', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Popular Questions\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows questions with the most popular (helpful) responses added to the Answers component.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1240, 'mod_quicktips', 'module', 'mod_quicktips', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Quick Tips\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Shawn Rice\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows a quick \\\"tip of the day\\\" or \\\"did you know...\\\" feature.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1241, 'mod_quotes', 'module', 'mod_quotes', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Quotes\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module compliments the Feedback component. It is used to display selected quotes on Notable Quotes page.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1242, 'mod_randomquote', 'module', 'mod_randomquote', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Random Quote\",\"type\":\"module\",\"creationDate\":\"Mar 2010\",\"author\":\"HUBzero\",\"copyright\":\"(C) 2010 HUBzero\",\"authorEmail\":\"alisa@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"Module to display random featured quote\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1243, 'mod_rapid_contact', 'module', 'mod_rapid_contact', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Rapid Contact\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows a notice (when site will be down, etc.) box for site visitors.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1244, 'mod_recentquestions', 'module', 'mod_recentquestions', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Latest Questions\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows the latest questions added to the Answers component.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1245, 'mod_reportproblems', 'module', 'mod_reportproblems', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Trouble Report\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a trouble report form\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1246, 'mod_resourcemenu', 'module', 'mod_resourcemenu', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"HUB Resource Menu\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows any extra navigation or content in a pop-up style menu. Supports {xhub:module position=\\\"\\\" style=\\\"\\\"} tags.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1247, 'mod_slideshow', 'module', 'mod_slideshow', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Slideshow\",\"type\":\"module\",\"creationDate\":\"June 2009\",\"author\":\"HUBzero\",\"copyright\":\"(C) 2000 - 2004 Miro International Pty Ltd\",\"authorEmail\":\"alisa@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1.1.0\",\"description\":\"Displays HUB flash image slideshow.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1248, 'mod_sliding_panes', 'module', 'mod_sliding_panes', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Sliding Panes\",\"type\":\"module\",\"creationDate\":\"Jan 2010\",\"author\":\"HUBzero\",\"copyright\":\"(C) 2000 - 2004 Miro International Pty Ltd\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"Rotate through panes of content\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1249, 'mod_spotlight', 'module', 'mod_spotlight', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Spotlight\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module randomly displays featured items.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1250, 'mod_tagcloud', 'module', 'mod_tagcloud', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Tag Cloud\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a tag cloud\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1251, 'mod_toptags', 'module', 'mod_toptags', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Top Tags\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows a a list of the top used tags.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1252, 'mod_twitterfeed', 'module', 'mod_twitterfeed', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Twitter Feed\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"csmoak@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1.0.0\",\"description\":\"Loads the Twitter feed of the specified Twitter ID\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1253, 'mod_whatsnew', 'module', 'mod_whatsnew', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"What\'s New\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Lists the newest resources and events on the site.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1254, 'mod_wishvoters', 'module', 'mod_wishvoters', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Wish Voters\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a list of most active wish voters\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1255, 'mod_xwhosonline', 'module', 'mod_xwhosonline', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"Extended Who is Online\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"The Who\'s Online module displays the number of anonymous (that is, Guest) users and Registered users, (those that are logged in) that are currently accessing the web site.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1256, 'mod_youtube', 'module', 'mod_youtube', '', '0', '1', '1', '0', '{\"legacy\":true,\"name\":\"YouTube\",\"type\":\"module\",\"creationDate\":\"March 2011\",\"author\":\"HUBzero\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"csmoak@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1.0.0\",\"description\":\"This module allows to display a youtube feed\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1257, 'mod_grouppages', 'module', 'mod_grouppages', '', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0);

INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
(1300, 'mod_hubmenu', 'module', 'mod_hubmenu', '', '1', '1', '1', '1', '{}', '{}', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1301, 'mod_answers', 'module', 'mod_answers', '', '1', '1', '1', '0', '{\"legacy\":true,\"name\":\"Answers\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1302, 'mod_application_env', 'module', 'mod_application_env', '', '1', '1', '1', '0', '{\"legacy\":true,\"name\":\"Application Environment\",\"type\":\"module\",\"creationDate\":\"April 2012\",\"author\":\"Shawn Rice\",\"copyright\":\"Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.\",\"authorEmail\":\"zooley@purdue.edu\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"This module displays the current application environment (production, stage, testing, development)\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1303, 'mod_dashboard', 'module', 'mod_dashboard', '', '1', '1', '1', '0', '{\"legacy\":true,\"name\":\"Dashboard\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1304, 'mod_groups', 'module', 'mod_groups', '', '1', '1', '1', '0', '{\"legacy\":true,\"name\":\"Groups\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1305, 'mod_members', 'module', 'mod_members', '', '1', '1', '1', '0', '{\"legacy\":true,\"name\":\"Members\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1306, 'mod_resources', 'module', 'mod_resources', '', '1', '1', '1', '0', '{\"legacy\":true,\"name\":\"Resources\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1307, 'mod_supporttickets', 'module', 'mod_supporttickets', '', '1', '1', '1', '0', '{\"legacy\":true,\"name\":\"Support Tickets\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1308, 'mod_tools', 'module', 'mod_tools', '', '1', '1', '1', '0', '{\"legacy\":true,\"name\":\"Tools\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1309, 'mod_whosonline', 'module', 'mod_whosonline', '', '1', '1', '1', '0', '{\"legacy\":true,\"name\":\"Show Online Users\",\"type\":\"module\",\"creationDate\":\"January 2005\",\"author\":\"Christopher Smoak\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"csmoak@purdue.edu\",\"authorUrl\":\"https:\\/\\/hubzero.org\",\"version\":\"1.0.0\",\"description\":\"This module shows a list of the currently logged in users\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1310, 'mod_wishlist', 'module', 'mod_wishlist', '', '1', '1', '1', '0', '{\"legacy\":true,\"name\":\"Wishlist\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright 2005-2011 Purdue University. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}', '', '', '', '0', '0000-00-00 00:00:00', '0', '0'),
(1311, 'mod_supportactivity', 'module', 'mod_supportactivity', '', 1, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0);

UPDATE `#__template_styles` SET home=0;
INSERT INTO `#__template_styles` VALUES (7,'hubbasic',0,'0','HUBzero Standard Site Template - 2011','{}');
INSERT INTO `#__template_styles` VALUES (8,'hubbasic2012',0,'0','HUBzero Standard Site Template - 2012','{}');
INSERT INTO `#__template_styles` VALUES (9,'hubbasic2013',0,'0','HUBzero Standard Site Template - 2013','{}');
INSERT INTO `#__template_styles` VALUES (10,'welcome', 0, '1', 'Welcome Template', '{\"flavor\":\"\",\"template\":\"hubbasic2013\"}');
INSERT INTO `#__template_styles` VALUES (11,'hubbasicadmin',1,'0','HUBzero Standard Admin Template','{}');
INSERT INTO `#__template_styles` VALUES (12,'kameleon', 1, '1', 'kameleon (admin)', '{\"header\":\"dark\",\"theme\":\"bluesteel\"}');

INSERT INTO `#__stats_tops` VALUES (1,'Top Tools by Ranking',1,5);
INSERT INTO `#__stats_tops` VALUES (2,'Top Tools by Simulation Users',1,5);
INSERT INTO `#__stats_tops` VALUES (3,'Top Tools by Interactive Sessions',1,5);
INSERT INTO `#__stats_tops` VALUES (4,'Top Tools by Simulation Sessions',1,5);
INSERT INTO `#__stats_tops` VALUES (5,'Top Tools by Simulation Runs',1,5);
INSERT INTO `#__stats_tops` VALUES (6,'Top Tools by Simulation Wall Time',2,5);
INSERT INTO `#__stats_tops` VALUES (7,'Top Tools by Simulation CPU Time',2,5);
INSERT INTO `#__stats_tops` VALUES (8,'Top Tools by Simulation Interaction Time',2,5);
INSERT INTO `#__stats_tops` VALUES (9,'Top Tools by Citations',1,5);

INSERT INTO `#__resource_stats_tools_tops` VALUES (1,'Users By Country Of Residence',1,5);
INSERT INTO `#__resource_stats_tools_tops` VALUES (2,'Top Domains By User Count',1,5);
INSERT INTO `#__resource_stats_tools_tops` VALUES (3,'Users By Organization Type',1,5);

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
INSERT INTO `#__xmessage_component` VALUES (24,'com_tools','contribtool_status_changed','Tool development status has changed');
INSERT INTO `#__xmessage_component` VALUES (25,'com_tools','contribtool_new_message','New contribtool message is received');
INSERT INTO `#__xmessage_component` VALUES (26,'com_tools','contribtool_info_changed','Information about a tool I develop has changed');
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
INSERT INTO `#__ysearch_plugin_weights` VALUES ('wiki',1);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('weighttitle',1);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('sortrelevance',1);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('sortnewer',0.2);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('tagmod',1.3);
INSERT INTO `#__ysearch_plugin_weights` VALUES ('weightcontributor',0.2);

INSERT INTO `#__courses_roles` (`offering_id`, `alias`, `title`, `permissions`) VALUES	(0, 'instructor', 'Instructor', ''), (0, 'manager', 'Manager', ''),	(0, 'student', 'Student', '');

INSERT INTO `#__courses_grade_policies` (`id`, `description`, `threshold`, `exam_weight`, `quiz_weight`, `homework_weight`)
						VALUES (1, 'An average exam score of 70% or greater is required to pass the class.  Quizzes and homeworks do not count toward the final score.', 0.70, 1.00, 0.00, 0.00);

INSERT INTO `#__newsletter_templates` (`editable`, `name`, `template`, `primary_title_color`, `primary_text_color`, `secondary_title_color`, `secondary_text_color`, `deleted`) VALUES
(0, 'Default HTML Email Template', '<html>\n <head>\n    <title>{{TITLE}}</title>\n  </head>\n <body>\n    <table width=\"100%\" border=\"0\" cellspacing=\"0\">\n     <tr>\n        <td align=\"center\">\n         \n          <table width=\"700\" border=\"0\" cellpadding=\"20\" cellspacing=\"0\">\n           <tr class=\"display-browser\">\n              <td colspan=\"2\" style=\"font-size:10px;padding:0 0 5px 0;\" align=\"center\">\n               Email not displaying correctly? <a href=\"{{LINK}}\">View in a Web Browser</a>\n              </td>\n           </tr>\n           <tr>\n              <td colspan=\"2\" style=\"background:#000000;\">\n                <h1 style=\"color:#FFFFFF;\">HUB Campaign Template</h1>\n               <h3 style=\"color:#888888;\">{{TITLE}}</h3>\n             </td>\n           <tr>\n              <td width=\"500\" valign=\"top\" style=\"font-size:14px;color:#222222;border-left:1px solid #000000;\">\n               <span style=\"display:block;color:#CCCCCC;margin-bottom:20px;\">Issue {{ISSUE}}</span>\n                {{PRIMARY_STORIES}}\n             </td>\n             <td width=\"200\" valign=\"top\" style=\"font-size:12px;color:#555555;border-left:1px solid #AAAAAA;border-right:1px solid #000000;\">\n                {{SECONDARY_STORIES}}\n             </td>\n           </tr>\n           <tr>\n              <td colspan=\"2\" align=\"center\" style=\"background:#000000;color:#FFFFFF;\">\n               Copyright &copy; {{COPYRIGHT}} HUB. All Rights reserved.\n              </td>\n           </tr>\n         </table>\n        \n        </td>\n     </tr>\n   </table>\n  </body>\n</html>  ', '', '', '', '', 0),
(0, 'Default Plain Text Email Template', 'View In Browser - {{LINK}}\n=====================================\n{{TITLE}} - {{ISSUE}}\n=====================================\n\n{{PRIMARY_STORIES}}\n\n--------------------------------------------------\n\n{{SECONDARY_STORIES}}\n\n--------------------------------------------------\n\nUnsubscribe - {{UNSUBSCRIBE_LINK}}\nCopyright - {{COPYRIGHT}}', NULL, NULL, NULL, NULL, 0);

INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `last_run`, `next_run`, `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`, `params`) VALUES ('Process Newsletter Mailings', 0, 'newsletter', 'processMailings', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '*/5 * * * *', '2013-06-25 08:23:04', 1001, '2013-07-16 17:15:01', 0, 0, 0, '');
INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `last_run`, `next_run`, `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`, `params`) VALUES ('Process Newsletter Opens & Click IP Addresses', 0, 'newsletter', 'processIps', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '*/5 * * * *', '2013-06-25 08:23:04', 1001, '2013-07-16 17:15:01', 0, 0, 0, '');
INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `last_run`, `next_run`, `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`, `params`) VALUES ('Group Announcements', 1, 'groups', 'sendGroupAnnouncements', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '*/5 * * * *', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, 0, 0, '');

INSERT INTO `#__oaipmh_dcspecs` (`id`, `name`, `query`, `display`) VALUES
						(1, 'resource IDs', 'SELECT p.id FROM #__publications p, #__publication_versions pv WHERE p.id = pv.publication_id AND pv.state = 1', 1),
						(2, 'specify sets', '', 1),
						(3, 'title', 'SELECT pv.title FROM #__publication_versions pv, #__publications p WHERE p.id = pv.publication_id AND p.id = \$id LIMIT 1', 1),
						(4, 'creator', 'SELECT pa.name FROM #__publication_authors pa, #__publication_versions pv, #__publications p WHERE pa.publication_version_id = pv.id AND pv.publication_id = p.id AND p.id = \$id LIMIT 1', 1),
						(5, 'subject', 'SELECT t.raw_tag FROM #__tags t, #__tags_object tos WHERE t.id = tos.tagid AND tos.objectid = \$id ORDER BY t.raw_tag', 1),
						(6, 'date', 'SELECT pv.submitted FROM #__publication_versions pv, #__publications p WHERE p.id = pv.publication_id AND p.id = \$id ORDER BY pv.submitted LIMIT 1', 1),
						(7, 'identifier', 'SELECT pv.doi FROM #__publication_versions pv, #__publications p WHERE p.id = pv.publication_id AND pv.state = 1 AND p.id = \$id', 1),
						(8, 'description', 'SELECT pv.description FROM #__publication_versions pv, #__publications p WHERE p.id = pv.publication_id AND p.id = \$id LIMIT 1', 1),
						(9, 'type', 'Dataset', 1),
						(10, 'publisher', 'myhub', 1),
						(11, 'rights', 'SELECT pl.title FROM #__publications p, #__publication_versions pv, #__publication_licenses pl WHERE pl.id = pv.license_type AND pv.publication_id = p.id AND p.id = \$id LIMIT 1', 1),
						(12, 'contributor', 'SELECT pa.name FROM #__publication_authors pa, #__publication_versions pv, #__publications p WHERE pa.publication_version_id = pv.id AND pv.publication_id = p.id AND p.id = \$id AND pv.state = 1', 1),
						(13, 'relation', 'SELECT DISTINCT path FROM #__publication_attachments pa WHERE publication_id = \$id AND role = 1 ORDER BY path', 1),
						(14, 'format', '', 1),
						(15, 'coverage', '', 1),
						(16, 'language', '', 1),
						(17, 'source', '', 1);

INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('1','Datasets','Dataset','dataset','datasets','A collection of research data','1','1','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1\nplg_wishlist=1\nplg_citations=1\nplg_usage = 1');
INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('2','Workshops','Event','workshop','workshops','A collection of lectures, seminars, and materials that were presented at a workshop.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('3','Publications','Dataset','publication','publications','A publication is a paper relevant to the community that has been published in some manner.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('4','Learning Modules','InteractiveResource','learning module','learningmodules','A combination of presentations, tools, assignments, etc. geared toward teaching a specific concept.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('5','Animations','MovingImage','animation','animations','An animation is a Flash-based demo or short movie that illustrates some concept.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('6','Courses','Collection','course','courses','University courses that make videos of lectures and associated teaching materials available.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('7','Tools','Software','tool','tools','A simulation tool is software that allows users to run a specific type of calculation.','0','1','poweredby=Powered by=textarea=0\nbio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('9','Downloads','PhysicalObject','download','downloads','A download is a type of resource that users can download and use on their own computer.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('10','Notes','Text','note','notes','Notes are typically a category for any resource that might not fit any of the other categories.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('11','Series','Collection','series','series','Series are collections of other resources, typically online presentations, that cover a specific topic.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('12','Teaching Materials','Text','teaching material','teachingmaterials','Supplementary materials (study notes, guides, etc.) that don\'t quite fit into any of the other categories.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');

INSERT INTO `#__project_types` (`type`,`description`,`params`) VALUES ('General','Individual or collaborative projects of general nature','apps_dev=0\npublications_public=1\nteam_public=1\nallow_invite=0');
INSERT INTO `#__project_types` (`type`,`description`,`params`) VALUES ('Content publication','Projects created with the purpose to publish data as a resource or a collection of related resources','apps_dev=0\npublications_public=1\nteam_public=1\nallow_invite=0');
INSERT INTO `#__project_types` (`type`,`description`,`params`) VALUES ('Application development','Projects created with the purpose to develop and publish a simulation tool or a code library','apps_dev=1\npublications_public=1\nteam_public=1\nallow_invite=0');

INSERT INTO `#__publication_audience_levels` (`label`,`title`,`description`) VALUES ('level0','K12','Middle/High School');
INSERT INTO `#__publication_audience_levels` (`label`,`title`,`description`) VALUES ('level1','Easy','Freshmen/Sophomores');
INSERT INTO `#__publication_audience_levels` (`label`,`title`,`description`) VALUES ('level2','Intermediate','Juniors/Seniors');
INSERT INTO `#__publication_audience_levels` (`label`,`title`,`description`) VALUES ('level3','Advanced','Graduate Students');
INSERT INTO `#__publication_audience_levels` (`label`,`title`,`description`) VALUES ('level4','Expert','PhD Experts');
INSERT INTO `#__publication_audience_levels` (`label`,`title`,`description`) VALUES ('level5','Professional','Beyond PhD');

INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) VALUES ('File(s)','files','uploaded material','1','1','1','peer_review=1');
INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) VALUES ('Link','links','external content','0','0','3','');
INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) VALUES ('Wiki','notes','from project notes','0','0','5','');
INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) VALUES ('Application','apps','simulation tool','0','0','4','');
INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) VALUES ('Series','series','publication collection','0','0','6','');
INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) VALUES ('Gallery','gallery','image/photo gallery','0','0','7','');
INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) VALUES ('Databases','databases','project database','0','0','2','');

INSERT INTO `#__publication_licenses` (`name`,`text`,`title`,`url`,`info`,`ordering`,`active`,`apps_only`,`main`,`agreement`,`customizable`,`icon`) VALUES ('custom','[ONE LINE DESCRIPTION]\r\nCopyright (C) [YEAR] [OWNER]','Custom','http://creativecommons.org/about/cc0','Custom license','3','1','0','0','0','1','/components/com_publications/assets/img/logos/license.gif');
INSERT INTO `#__publication_licenses` (`name`,`text`,`title`,`url`,`info`,`ordering`,`active`,`apps_only`,`main`,`agreement`,`customizable`,`icon`) VALUES ('cc','','CC0 - Creative Commons','http://creativecommons.org/about/cc0','CC0 enables scientists, educators, artists and other creators and owners of copyright- or database-protected content to waive those interests in their works and thereby place them as completely as possible in the public domain, so that others may freely build upon, enhance and reuse the works for any purposes without restriction under copyright or database law.','2','1','0','1','1','0','/components/com_publications/assets/img/logos/cc.gif');
INSERT INTO `#__publication_licenses` (`name`,`text`,`title`,`url`,`info`,`ordering`,`active`,`apps_only`,`main`,`agreement`,`customizable`,`icon`) VALUES ('standard','All rights reserved.','Standard HUB License','http://nanohub.org','Standard HUB license.','1','0','0','0','0','0','/components/com_publications/images/logos/license.gif');
