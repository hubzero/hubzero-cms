--
--  mysqldump -n --no-data --skip-add-drop-table --skip-add-locks --routines --triggers --skip-set-charset --skip-disable-keys
-- 
--  Further cleaned by hand for readability
--

SET NAMES 'utf8mb3';
SET @@SESSION.sql_mode = '';

--
-- Table structure for table `#__abuse_reports`
--

CREATE TABLE `#__abuse_reports` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(50) DEFAULT NULL,
  `referenceid` int(11) unsigned NOT NULL DEFAULT 0,
  `report` text NOT NULL,
  `created_by` int(11) unsigned NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` tinyint(2) NOT NULL DEFAULT 0,
  `subject` varchar(150) DEFAULT NULL,
  `reviewed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reviewed_by` int(11) unsigned NOT NULL DEFAULT 0,
  `note` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_reviewed_by` (`reviewed_by`),
  KEY `idx_state` (`state`),
  KEY `idx_category_referenceid` (`category`,`referenceid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__announcements`
--

CREATE TABLE `#__announcements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(100) DEFAULT NULL,
  `scope_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `priority` tinyint(2) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sticky` tinyint(2) NOT NULL DEFAULT 0,
  `email` tinyint(4) DEFAULT 0,
  `sent` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_scope_scope_id` (`scope`,`scope_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_state` (`state`),
  KEY `idx_priority` (`priority`),
  KEY `idx_sticky` (`sticky`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__answers_log`
--

CREATE TABLE `#__answers_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `response_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ip` varchar(15) NOT NULL DEFAULT '',
  `helpful` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_rid` (`response_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__answers_questions`
--

CREATE TABLE `#__answers_questions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(250) NOT NULL DEFAULT '',
  `question` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(3) NOT NULL DEFAULT 0,
  `anonymous` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `email` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `helpful` int(11) unsigned NOT NULL DEFAULT 0,
  `reward` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`),
  KEY `idx_created_by` (`created_by`),
  FULLTEXT KEY `ftidx_question` (`question`),
  FULLTEXT KEY `ftidx_subject` (`subject`),
  FULLTEXT KEY `ftidx_question_subject` (`question`,`subject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__answers_questions_log`
--

CREATE TABLE `#__answers_questions_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(11) unsigned NOT NULL DEFAULT 0,
  `expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `voter` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_qid` (`question_id`),
  KEY `idx_voter` (`voter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__answers_responses`
--

CREATE TABLE `#__answers_responses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(11) unsigned NOT NULL DEFAULT 0,
  `answer` text NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `helpful` int(11) unsigned NOT NULL DEFAULT 0,
  `nothelpful` int(11) unsigned NOT NULL DEFAULT 0,
  `state` tinyint(3) NOT NULL DEFAULT 0,
  `anonymous` tinyint(2) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_qid` (`question_id`),
  KEY `idx_state` (`state`),
  KEY `idx_created_by` (`created_by`),
  FULLTEXT KEY `ftidx_answer` (`answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__assets`
--

CREATE TABLE `#__assets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT 'Nested set parent.',
  `lft` int(11) NOT NULL DEFAULT 0 COMMENT 'Nested set lft.',
  `rgt` int(11) NOT NULL DEFAULT 0 COMMENT 'Nested set rgt.',
  `level` int(10) unsigned NOT NULL COMMENT 'The cached level in the nested tree.',
  `name` varchar(50) NOT NULL COMMENT 'The unique name for the asset.\n',
  `title` varchar(100) NOT NULL COMMENT 'The descriptive title for the asset.',
  `rules` varchar(5120) NOT NULL COMMENT 'JSON encoded access control.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_asset_name` (`name`),
  KEY `idx_lft_rgt` (`lft`,`rgt`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__associations`
--

CREATE TABLE `#__associations` (
  `id` varchar(50) NOT NULL COMMENT 'A reference to the associated item.',
  `context` varchar(50) NOT NULL COMMENT 'The context of the associated item.',
  `key` char(32) NOT NULL COMMENT 'The key for the association computed from an md5 on associated ids.',
  PRIMARY KEY (`context`,`id`),
  KEY `idx_key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__auth_domain`
--

CREATE TABLE `#__auth_domain` (
  `authenticator` varchar(255) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `params` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__auth_link`
--

CREATE TABLE `#__auth_link` (
  `auth_domain_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `params` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `linked_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__author_assoc`
--

CREATE TABLE `#__author_assoc` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subtable` varchar(50) NOT NULL DEFAULT '',
  `subid` int(11) NOT NULL DEFAULT 0,
  `authorid` int(11) NOT NULL DEFAULT 0,
  `ordering` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_subtable_subid_authorid` (`subtable`,`subid`,`authorid`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__author_role_types`
--

CREATE TABLE `#__author_role_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT 0,
  `type_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__author_roles`
--

CREATE TABLE `#__author_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `state` tinyint(3) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__author_stats`
--

CREATE TABLE `#__author_stats` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `authorid` int(11) NOT NULL,
  `tool_users` bigint(20) DEFAULT NULL,
  `andmore_users` bigint(20) DEFAULT NULL,
  `total_users` bigint(20) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT -1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__billboard_collection`
--

CREATE TABLE `#__billboard_collection` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__billboards`
--

CREATE TABLE `#__billboards` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `header` varchar(255) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `learn_more_text` varchar(255) DEFAULT NULL,
  `learn_more_target` varchar(255) DEFAULT NULL,
  `learn_more_class` varchar(255) DEFAULT NULL,
  `learn_more_location` varchar(255) DEFAULT NULL,
  `background_img` varchar(255) DEFAULT NULL,
  `padding` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `css` text DEFAULT NULL,
  `published` tinyint(1) DEFAULT 0,
  `ordering` int(11) DEFAULT NULL,
  `checked_out` int(11) DEFAULT 0,
  `checked_out_time` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__blog_comments`
--

CREATE TABLE `#__blog_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) unsigned NOT NULL DEFAULT 0,
  `content` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT 0,
  `anonymous` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `parent` int(11) unsigned NOT NULL DEFAULT 0,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_entry_id` (`entry_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_parent` (`parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__blog_entries`
--

CREATE TABLE `#__blog_entries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT 0,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` tinytext NOT NULL,
  `scope_id` int(11) NOT NULL DEFAULT 0,
  `hits` int(11) unsigned NOT NULL DEFAULT 0,
  `allow_comments` tinyint(2) NOT NULL DEFAULT 0,
  `scope` varchar(100) NOT NULL DEFAULT '',
  `access` tinyint(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_alias` (`alias`),
  KEY `idx_scope_id` (`scope_id`),
  FULLTEXT KEY `ftidx_title` (`title`),
  FULLTEXT KEY `ftidx_content` (`content`),
  FULLTEXT KEY `ftidx_title_content` (`title`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__cart`
--

CREATE TABLE `#__cart` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0,
  `itemid` int(11) NOT NULL DEFAULT 0,
  `type` varchar(20) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `selections` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__cart_cart_items`
--

CREATE TABLE `#__cart_cart_items` (
  `crtId` int(16) NOT NULL,
  `sId` int(16) NOT NULL,
  `crtiQty` int(5) DEFAULT NULL,
  `crtiOldQty` int(5) DEFAULT NULL,
  `crtiPrice` decimal(10,2) DEFAULT NULL,
  `crtiOldPrice` decimal(10,2) DEFAULT NULL,
  `crtiName` varchar(255) DEFAULT NULL,
  `crtiAvailable` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`crtId`,`sId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__cart_carts`
--

CREATE TABLE `#__cart_carts` (
  `crtId` int(16) NOT NULL AUTO_INCREMENT,
  `crtCreated` datetime DEFAULT NULL,
  `crtLastUpdated` datetime DEFAULT NULL,
  `uidNumber` int(16) DEFAULT NULL,
  PRIMARY KEY (`crtId`),
  UNIQUE KEY `uidx_uidNumber` (`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__cart_coupons`
--

CREATE TABLE `#__cart_coupons` (
  `crtId` int(16) NOT NULL,
  `cnId` int(16) NOT NULL,
  `crtCnAdded` datetime DEFAULT NULL,
  `crtCnStatus` char(15) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__cart_memberships`
--

CREATE TABLE `#__cart_memberships` (
  `crtmId` int(16) NOT NULL AUTO_INCREMENT,
  `pId` int(16) DEFAULT NULL,
  `crtId` int(16) DEFAULT NULL,
  `crtmExpires` datetime DEFAULT NULL,
  PRIMARY KEY (`crtmId`),
  UNIQUE KEY `uidx_pId_crtId` (`pId`,`crtId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__cart_saved_addresses`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__cart_transaction_info`
--

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
  `tiItems` text DEFAULT NULL,
  `tiPerks` text DEFAULT NULL,
  `tiMeta` text DEFAULT NULL,
  `tiCustomerStatus` char(15) DEFAULT 'unconfirmed',
  PRIMARY KEY (`tId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__cart_transaction_items`
--

CREATE TABLE `#__cart_transaction_items` (
  `tId` int(16) NOT NULL,
  `sId` int(16) NOT NULL,
  `tiQty` int(5) DEFAULT NULL,
  `tiPrice` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`tId`,`sId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__cart_transaction_steps`
--

CREATE TABLE `#__cart_transaction_steps` (
  `tsId` int(16) NOT NULL AUTO_INCREMENT,
  `tId` int(16) NOT NULL,
  `tsStep` char(16) NOT NULL,
  `tsStatus` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`tsId`),
  UNIQUE KEY `uidx_tId_tsStep` (`tId`,`tsStep`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__cart_transactions`
--

CREATE TABLE `#__cart_transactions` (
  `tId` int(16) NOT NULL AUTO_INCREMENT,
  `crtId` int(16) DEFAULT NULL,
  `tCreated` datetime DEFAULT NULL,
  `tLastUpdated` datetime DEFAULT NULL,
  `tStatus` char(32) DEFAULT NULL,
  PRIMARY KEY (`tId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__categories`
--

CREATE TABLE `#__categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `parent_id` int(10) unsigned NOT NULL DEFAULT 0,
  `lft` int(11) NOT NULL DEFAULT 0,
  `rgt` int(11) NOT NULL DEFAULT 0,
  `level` int(10) unsigned NOT NULL DEFAULT 0,
  `path` varchar(255) NOT NULL DEFAULT '',
  `extension` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `checked_out` int(11) unsigned NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(10) unsigned NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  `metadesc` varchar(1024) NOT NULL COMMENT 'The meta description for the page.',
  `metakey` varchar(1024) NOT NULL COMMENT 'The meta keywords for the page.',
  `metadata` varchar(2048) NOT NULL COMMENT 'JSON encoded metadata properties.',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(10) unsigned NOT NULL DEFAULT 0,
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_extension_published_access` (`extension`,`published`,`access`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_path` (`path`),
  KEY `idx_left_right` (`lft`,`rgt`),
  KEY `idx_alias` (`alias`),
  KEY `idx_language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__citations`
--

CREATE TABLE `#__citations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `published` int(3) NOT NULL DEFAULT 1,
  `affiliated` int(11) NOT NULL DEFAULT 0,
  `fundedby` int(3) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `address` varchar(250) DEFAULT NULL,
  `author` text DEFAULT NULL,
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
  `note` text DEFAULT NULL,
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
  `notes` text DEFAULT NULL,
  `language` varchar(100) DEFAULT NULL,
  `accession_number` varchar(100) DEFAULT NULL,
  `short_title` varchar(250) DEFAULT NULL,
  `author_address` text DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `call_number` varchar(100) DEFAULT NULL,
  `label` varchar(100) DEFAULT NULL,
  `research_notes` text DEFAULT NULL,
  `params` text DEFAULT NULL,
  `formatted` text DEFAULT NULL,
  `format` varchar(11) DEFAULT NULL,
  `scope` varchar(45) DEFAULT NULL,
  `scope_id` varchar(45) DEFAULT NULL,
  `custom1` text DEFAULT NULL,
  `custom2` text DEFAULT NULL,
  `custom3` varchar(45) DEFAULT NULL,
  `custom4` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `ftidx_title_isbn_doi_abstract` (`title`,`isbn`,`doi`,`abstract`),
  FULLTEXT KEY `ftidx_title_isbn_doi_abstract_author_publisher` (`title`,`isbn`,`doi`,`abstract`,`author`,`publisher`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__citations_assoc`
--

CREATE TABLE `#__citations_assoc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT 0,
  `oid` int(11) DEFAULT 0,
  `type` varchar(50) DEFAULT NULL,
  `tbl` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__citations_authors`
--

CREATE TABLE `#__citations_authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT 0,
  `author` varchar(64) DEFAULT NULL,
  `authorid` int(11) DEFAULT 0,
  `uidNumber` int(11) DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
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
  `in_network` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_cid_author_authorid_uidNumber` (`cid`,`author`,`authorid`,`uidNumber`),
  KEY `idx_authorid` (`authorid`),
  KEY `idx_uidNumber` (`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__citations_format`
--

CREATE TABLE `#__citations_format` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `typeid` int(11) DEFAULT NULL,
  `style` varchar(50) DEFAULT NULL,
  `format` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__citations_secondary`
--

CREATE TABLE `#__citations_secondary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `sec_cits_cnt` int(11) DEFAULT NULL,
  `search_string` tinytext DEFAULT NULL,
  `scope` varchar(250) DEFAULT NULL,
  `scope_id` int(11) DEFAULT NULL,
  `link1_url` tinytext DEFAULT NULL,
  `link1_title` varchar(60) DEFAULT NULL,
  `link2_url` tinytext DEFAULT NULL,
  `link2_title` varchar(60) DEFAULT NULL,
  `link3_url` tinytext DEFAULT NULL,
  `link3_title` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__citations_sponsors`
--

CREATE TABLE `#__citations_sponsors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sponsor` varchar(150) DEFAULT NULL,
  `link` varchar(200) DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__citations_sponsors_assoc`
--

CREATE TABLE `#__citations_sponsors_assoc` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT NULL,
  `sid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__citations_types`
--

CREATE TABLE `#__citations_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `type_title` varchar(255) DEFAULT NULL,
  `type_desc` text DEFAULT NULL,
  `type_export` varchar(255) DEFAULT NULL,
  `fields` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__collections`
--

CREATE TABLE `#__collections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL,
  `object_id` int(11) NOT NULL DEFAULT 0,
  `object_type` varchar(150) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(3) NOT NULL DEFAULT 1,
  `access` tinyint(3) NOT NULL DEFAULT 0,
  `is_default` tinyint(2) NOT NULL DEFAULT 0,
  `description` mediumtext NOT NULL,
  `positive` int(11) NOT NULL DEFAULT 0,
  `negative` int(11) NOT NULL DEFAULT 0,
  `sort` varchar(50) NOT NULL DEFAULT 'created',
  `layout` varchar(50) NOT NULL DEFAULT 'grid',
  PRIMARY KEY (`id`),
  KEY `idx_object_type_object_id` (`object_type`,`object_id`),
  KEY `idx_state` (`state`),
  KEY `idx_access` (`access`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__collections_assets`
--

CREATE TABLE `#__collections_assets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  `type` varchar(50) NOT NULL DEFAULT 'file',
  `ordering` tinyint(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__collections_following`
--

CREATE TABLE `#__collections_following` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `follower_type` varchar(150) NOT NULL,
  `follower_id` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `following_type` varchar(150) NOT NULL DEFAULT '',
  `following_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_follower_type_follower_id` (`follower_type`,`follower_id`),
  KEY `idx_following_type_following_id` (`following_type`,`following_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__collections_items`
--

CREATE TABLE `#__collections_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `url` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(3) NOT NULL DEFAULT 1,
  `access` tinyint(2) NOT NULL DEFAULT 0,
  `positive` int(11) NOT NULL DEFAULT 0,
  `negative` int(11) NOT NULL DEFAULT 0,
  `type` varchar(150) NOT NULL DEFAULT '',
  `object_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`),
  KEY `idx_created_by` (`created_by`),
  FULLTEXT KEY `idx_fulltxt_title_description` (`title`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__collections_posts`
--

CREATE TABLE `#__collections_posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `collection_id` int(11) NOT NULL DEFAULT 0,
  `item_id` int(11) NOT NULL DEFAULT 0,
  `description` mediumtext NOT NULL,
  `original` tinyint(2) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_collection_id` (`collection_id`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_original` (`original`),
  FULLTEXT KEY `idx_fulltxt_description` (`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__collections_votes`
--

CREATE TABLE `#__collections_votes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `item_id` int(11) NOT NULL DEFAULT 0,
  `voted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_item_id_user_id` (`item_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__content`
--

CREATE TABLE `#__content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `title_alias` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT 0,
  `sectionid` int(10) unsigned NOT NULL DEFAULT 0,
  `mask` int(10) unsigned NOT NULL DEFAULT 0,
  `catid` int(10) unsigned NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT 0,
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT 0,
  `checked_out` int(10) unsigned NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `images` text NOT NULL,
  `urls` text NOT NULL,
  `attribs` varchar(5120) NOT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT 1,
  `parentid` int(10) unsigned NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `access` int(10) unsigned NOT NULL DEFAULT 0,
  `hits` int(10) unsigned NOT NULL DEFAULT 0,
  `metadata` text NOT NULL,
  `featured` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Set if article is featured.',
  `language` char(7) NOT NULL COMMENT 'The language code for the article.',
  `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_featured_catid` (`featured`,`catid`),
  KEY `idx_language` (`language`),
  KEY `idx_xreference` (`xreference`),
  FULLTEXT KEY `ftidx_title` (`title`),
  FULLTEXT KEY `ftidx_introtext_fulltext` (`introtext`,`fulltext`),
  FULLTEXT KEY `ftidx_title_introtext_fulltext` (`title`,`introtext`,`fulltext`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__content_frontpage`
--

CREATE TABLE `#__content_frontpage` (
  `content_id` int(11) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__content_rating`
--

CREATE TABLE `#__content_rating` (
  `content_id` int(11) NOT NULL DEFAULT 0,
  `rating_sum` int(10) unsigned NOT NULL DEFAULT 0,
  `rating_count` int(10) unsigned NOT NULL DEFAULT 0,
  `lastip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__core_log_searches`
--

CREATE TABLE `#__core_log_searches` (
  `search_term` varchar(128) NOT NULL DEFAULT '',
  `hits` int(10) unsigned NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses`
--

CREATE TABLE `#__courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `group_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `state` tinyint(3) NOT NULL DEFAULT 0,
  `type` tinyint(3) NOT NULL DEFAULT 0,
  `access` tinyint(3) NOT NULL DEFAULT 0,
  `blurb` text NOT NULL,
  `description` text NOT NULL,
  `logo` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  `length` varchar(255) DEFAULT NULL,
  `effort` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `ftidx_alias_title_blurb` (`alias`,`title`,`blurb`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_announcements`
--

CREATE TABLE `#__courses_announcements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) NOT NULL DEFAULT 0,
  `content` text DEFAULT NULL,
  `priority` tinyint(2) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `section_id` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sticky` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_offering_id` (`offering_id`),
  KEY `idx_section_id` (`section_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_state` (`state`),
  KEY `idx_priority` (`priority`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_asset_associations`
--

CREATE TABLE `#__courses_asset_associations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `scope_id` int(11) NOT NULL DEFAULT 0,
  `scope` varchar(255) NOT NULL DEFAULT 'asset_group',
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_asset_id` (`asset_id`),
  KEY `idx_scope_id` (`scope_id`),
  KEY `idx_scope` (`scope`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_asset_group_types`
--

CREATE TABLE `#__courses_asset_group_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(200) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_asset_groups`
--

CREATE TABLE `#__courses_asset_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) NOT NULL DEFAULT 0,
  `alias` varchar(250) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `parent` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_unit_id` (`unit_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_asset_unity`
--

CREATE TABLE `#__courses_asset_unity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `passed` tinyint(1) NOT NULL,
  `details` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_asset_views`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_assets`
--

CREATE TABLE `#__courses_assets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` mediumtext DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `subtype` varchar(255) NOT NULL DEFAULT 'file',
  `url` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(2) NOT NULL DEFAULT 1,
  `course_id` int(11) NOT NULL DEFAULT 0,
  `graded` tinyint(2) DEFAULT NULL,
  `grade_weight` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_course_id` (`course_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_certificates`
--

CREATE TABLE `#__courses_certificates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `properties` text DEFAULT NULL,
  `course_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_form_answers`
--

CREATE TABLE `#__courses_form_answers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `correct` tinyint(4) NOT NULL,
  `left_dist` int(11) NOT NULL,
  `top_dist` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_question_id` (`question_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_form_deployments`
--

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
  `allowed_attempts` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_crumb` (`crumb`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_form_questions`
--

CREATE TABLE `#__courses_form_questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `page` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `left_dist` int(11) NOT NULL,
  `top_dist` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `form_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_form_respondent_progress`
--

CREATE TABLE `#__courses_form_respondent_progress` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `respondent_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `submitted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_respondent_id_question_id` (`respondent_id`,`question_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_form_respondents`
--

CREATE TABLE `#__courses_form_respondents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `deployment_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `started` timestamp NULL DEFAULT NULL,
  `finished` timestamp NULL DEFAULT NULL,
  `attempt` int(11) NOT NULL DEFAULT 1,
  `attempts` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_member_id` (`member_id`),
  KEY `idx_deployment_id` (`deployment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_form_responses`
--

CREATE TABLE `#__courses_form_responses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `respondent_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_respondent_id` (`respondent_id`),
  KEY `idx_question_id` (`question_id`),
  KEY `idx_answer_id` (`answer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_forms`
--

CREATE TABLE `#__courses_forms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` text DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `asset_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_grade_book`
--

CREATE TABLE `#__courses_grade_book` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `scope` varchar(255) NOT NULL DEFAULT 'asset',
  `scope_id` int(11) NOT NULL DEFAULT 0,
  `override` decimal(5,2) DEFAULT NULL,
  `score_recorded` datetime DEFAULT NULL,
  `override_recorded` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_user_id_scope_scope_id` (`member_id`,`scope`,`scope_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_grade_policies`
--

CREATE TABLE `#__courses_grade_policies` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` mediumtext DEFAULT NULL,
  `threshold` decimal(3,2) DEFAULT NULL,
  `exam_weight` decimal(3,2) DEFAULT NULL,
  `quiz_weight` decimal(3,2) DEFAULT NULL,
  `homework_weight` decimal(3,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_log`
--

CREATE TABLE `#__courses_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scope_id` int(11) NOT NULL DEFAULT 0,
  `scope` varchar(100) NOT NULL DEFAULT '',
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `action` varchar(50) NOT NULL DEFAULT '',
  `comments` text NOT NULL,
  `actor_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_member_badges`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_member_notes`
--

CREATE TABLE `#__courses_member_notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(255) NOT NULL DEFAULT '',
  `scope_id` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `content` mediumtext NOT NULL,
  `pos_x` int(11) NOT NULL DEFAULT 0,
  `pos_y` int(11) NOT NULL DEFAULT 0,
  `width` int(11) NOT NULL DEFAULT 0,
  `height` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  `timestamp` time NOT NULL DEFAULT '00:00:00',
  `section_id` int(11) NOT NULL DEFAULT 0,
  `access` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_scoped` (`scope`,`scope_id`),
  KEY `idx_createdby` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_members`
--

CREATE TABLE `#__courses_members` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `course_id` int(11) NOT NULL DEFAULT 0,
  `offering_id` int(11) NOT NULL DEFAULT 0,
  `section_id` int(11) NOT NULL DEFAULT 0,
  `role_id` int(11) NOT NULL DEFAULT 0,
  `permissions` mediumtext NOT NULL,
  `enrolled` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `student` tinyint(2) NOT NULL DEFAULT 0,
  `first_visit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `token` varchar(23) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_offering_id` (`offering_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_role_id` (`role_id`),
  KEY `idx_section_id` (`section_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_offering_section_badge_criteria`
--

CREATE TABLE `#__courses_offering_section_badge_criteria` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `section_badge_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_offering_section_badges`
--

CREATE TABLE `#__courses_offering_section_badges` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `published` int(1) NOT NULL DEFAULT 0,
  `provider_name` varchar(255) NOT NULL DEFAULT 'passport',
  `provider_badge_id` int(11) NOT NULL,
  `img_url` varchar(255) NOT NULL DEFAULT '',
  `criteria_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_offering_section_codes`
--

CREATE TABLE `#__courses_offering_section_codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT 0,
  `code` varchar(10) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `redeemed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `redeemed_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_offering_section_dates`
--

CREATE TABLE `#__courses_offering_section_dates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT 0,
  `scope` varchar(150) NOT NULL DEFAULT '',
  `scope_id` int(11) NOT NULL DEFAULT 0,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_section_id` (`section_id`),
  KEY `idx_scope_id` (`scope_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_offering_sections`
--

CREATE TABLE `#__courses_offering_sections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) NOT NULL DEFAULT 0,
  `is_default` tinyint(2) NOT NULL DEFAULT 0,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `state` tinyint(2) NOT NULL DEFAULT 1,
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `enrollment` tinyint(2) NOT NULL DEFAULT 0,
  `grade_policy_id` int(11) NOT NULL DEFAULT 1,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_offering_id` (`offering_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_offerings`
--

CREATE TABLE `#__courses_offerings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT 0,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `term` varchar(255) NOT NULL DEFAULT '',
  `state` tinyint(2) NOT NULL DEFAULT 1,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_course_id` (`course_id`),
  KEY `idx_state` (`state`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_page_hits`
--

CREATE TABLE `#__courses_page_hits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) NOT NULL DEFAULT 0,
  `page_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_offering_id` (`offering_id`),
  KEY `idx_page_id` (`page_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_pages`
--

CREATE TABLE `#__courses_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT 0,
  `offering_id` varchar(100) NOT NULL DEFAULT '0',
  `section_id` int(11) NOT NULL DEFAULT 0,
  `url` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `active` int(11) NOT NULL DEFAULT 0,
  `privacy` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_offering_id` (`offering_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_prerequisites`
--

CREATE TABLE `#__courses_prerequisites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT 0,
  `item_scope` varchar(255) NOT NULL DEFAULT 'asset',
  `item_id` int(11) NOT NULL DEFAULT 0,
  `requisite_scope` varchar(255) NOT NULL DEFAULT 'asset',
  `requisite_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_progress_factors`
--

CREATE TABLE `#__courses_progress_factors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_reviews`
--

CREATE TABLE `#__courses_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT 0,
  `offering_id` int(11) NOT NULL DEFAULT 0,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `content` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  `anonymous` tinyint(2) NOT NULL DEFAULT 0,
  `parent` int(11) NOT NULL DEFAULT 0,
  `access` tinyint(2) NOT NULL DEFAULT 0,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  `positive` int(11) NOT NULL DEFAULT 0,
  `negative` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_roles`
--

CREATE TABLE `#__courses_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) NOT NULL DEFAULT 0,
  `alias` varchar(150) NOT NULL,
  `title` varchar(150) NOT NULL DEFAULT '',
  `permissions` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_offering_id` (`offering_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__courses_units`
--

CREATE TABLE `#__courses_units` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `offering_id` int(11) NOT NULL DEFAULT 0,
  `alias` varchar(250) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_offering_id` (`offering_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__cron_jobs`
--

CREATE TABLE `#__cron_jobs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `state` tinyint(3) NOT NULL DEFAULT 0,
  `plugin` varchar(255) NOT NULL DEFAULT '',
  `event` varchar(255) NOT NULL DEFAULT '',
  `last_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `next_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `recurrence` varchar(50) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(3) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__document_resource_rel`
--

CREATE TABLE `#__document_resource_rel` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_id` (`id`),
  UNIQUE KEY `uidx_document_id_resource_id` (`document_id`,`resource_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__document_text_data`
--

CREATE TABLE `#__document_text_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `body` text DEFAULT NULL,
  `hash` char(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_hash` (`hash`),
  FULLTEXT KEY `ftidx_body` (`body`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__doi_mapping`
--

CREATE TABLE `#__doi_mapping` (
  `local_revision` int(11) NOT NULL,
  `doi_label` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `alias` varchar(30) DEFAULT NULL,
  `versionid` int(11) DEFAULT 0,
  `doi` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__email_bounces`
--

CREATE TABLE `#__email_bounces` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) DEFAULT NULL,
  `component` varchar(100) DEFAULT NULL,
  `object` varchar(100) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `resolved` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__event_registration`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__events`
--

CREATE TABLE `#__events` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL DEFAULT 1,
  `calendar_id` int(11) DEFAULT NULL,
  `ical_uid` varchar(255) DEFAULT NULL,
  `scope` varchar(100) DEFAULT NULL,
  `scope_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  `adresse_info` varchar(120) NOT NULL DEFAULT '',
  `contact_info` varchar(120) NOT NULL DEFAULT '',
  `extra_info` varchar(240) NOT NULL DEFAULT '',
  `state` tinyint(3) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL DEFAULT 0,
  `checked_out` int(11) unsigned NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `allday` int(11) DEFAULT 0,
  `time_zone` varchar(5) DEFAULT NULL,
  `repeating_rule` varchar(150) DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT 1,
  `registerby` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text DEFAULT NULL,
  `restricted` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `ftidx_title` (`title`),
  FULLTEXT KEY `ftidx_content` (`content`),
  FULLTEXT KEY `ftidx_title_content` (`title`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__events_calendars`
--

CREATE TABLE `#__events_calendars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scope` varchar(100) DEFAULT NULL,
  `scope_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `color` varchar(100) DEFAULT NULL,
  `published` int(11) DEFAULT 1,
  `url` varchar(255) DEFAULT NULL,
  `readonly` tinyint(4) DEFAULT 0,
  `last_fetched` datetime DEFAULT NULL,
  `last_fetched_attempt` datetime DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__events_categories`
--

CREATE TABLE `#__events_categories` (
  `id` int(12) NOT NULL DEFAULT 0,
  `color` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__events_config`
--

CREATE TABLE `#__events_config` (
  `param` varchar(100) DEFAULT NULL,
  `value` tinytext DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__events_pages`
--

CREATE TABLE `#__events_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) DEFAULT 0,
  `alias` varchar(100) NOT NULL,
  `title` varchar(250) NOT NULL,
  `pagetext` text DEFAULT NULL,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) DEFAULT 0,
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT 0,
  `ordering` int(2) DEFAULT 0,
  `params` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__events_respondent_race_rel`
--

CREATE TABLE `#__events_respondent_race_rel` (
  `respondent_id` int(11) DEFAULT NULL,
  `race` varchar(255) DEFAULT NULL,
  `tribal_affiliation` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__events_respondents`
--

CREATE TABLE `#__events_respondents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL DEFAULT 0,
  `registered` timestamp NOT NULL DEFAULT current_timestamp(),
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
  `abstract` text DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `arrival` varchar(50) DEFAULT NULL,
  `departure` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__extensions`
--

CREATE TABLE `#__extensions` (
  `extension_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  `element` varchar(100) NOT NULL,
  `folder` varchar(100) NOT NULL,
  `client_id` tinyint(3) NOT NULL,
  `enabled` tinyint(3) NOT NULL DEFAULT 1,
  `access` int(10) unsigned NOT NULL DEFAULT 1,
  `protected` tinyint(3) NOT NULL DEFAULT 0,
  `manifest_cache` text NOT NULL,
  `params` text NOT NULL,
  `custom_data` text NOT NULL,
  `system_data` text NOT NULL,
  `checked_out` int(10) unsigned NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) DEFAULT 0,
  `state` int(11) DEFAULT 0,
  PRIMARY KEY (`extension_id`),
  KEY `element_clientid` (`element`,`client_id`),
  KEY `element_folder_clientid` (`element`,`folder`,`client_id`),
  KEY `extension` (`type`,`element`,`folder`,`client_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__faq`
--

CREATE TABLE `#__faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `alias` varchar(200) DEFAULT NULL,
  `params` text DEFAULT NULL,
  `fulltxt` text DEFAULT NULL,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) DEFAULT 0,
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT 0,
  `checked_out` int(11) DEFAULT 0,
  `checked_out_time` datetime DEFAULT '0000-00-00 00:00:00',
  `state` int(3) DEFAULT 0,
  `access` tinyint(3) DEFAULT 0,
  `hits` int(11) DEFAULT 0,
  `version` int(11) DEFAULT 0,
  `section` int(11) NOT NULL DEFAULT 0,
  `category` int(11) DEFAULT 0,
  `helpful` int(11) NOT NULL DEFAULT 0,
  `nothelpful` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_section` (`section`),
  KEY `idx_category` (`category`),
  KEY `idx_alias` (`alias`),
  FULLTEXT KEY `ftidx_title` (`title`),
  FULLTEXT KEY `ftidx_title_params_fulltxt` (`title`,`params`,`fulltxt`),
  FULLTEXT KEY `ftidx_params` (`params`),
  FULLTEXT KEY `ftidx_fulltxt` (`fulltxt`),
  FULLTEXT KEY `ftidx_title_fulltxt` (`title`,`fulltxt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__faq_categories`
--

CREATE TABLE `#__faq_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `alias` varchar(200) DEFAULT NULL,
  `description` varchar(255) DEFAULT '',
  `section` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(3) NOT NULL DEFAULT 0,
  `access` tinyint(3) NOT NULL DEFAULT 0,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_alias` (`alias`),
  KEY `idx_section` (`section`),
  KEY `idx_state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__faq_comments`
--

CREATE TABLE `#__faq_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) NOT NULL DEFAULT 0,
  `content` text DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `anonymous` tinyint(2) NOT NULL DEFAULT 0,
  `parent` int(11) NOT NULL DEFAULT 0,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `helpful` int(11) NOT NULL DEFAULT 0,
  `nothelpful` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_entry_id` (`entry_id`),
  KEY `idx_state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__faq_helpful_log`
--

CREATE TABLE `#__faq_helpful_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) DEFAULT 0,
  `ip` varchar(15) DEFAULT NULL,
  `vote` varchar(10) DEFAULT NULL,
  `user_id` int(11) DEFAULT 0,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_type_object_id` (`type`,`object_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__feedback`
--

CREATE TABLE `#__feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT '',
  `org` varchar(100) DEFAULT '',
  `quote` text DEFAULT NULL,
  `picture` varchar(250) DEFAULT '',
  `date` datetime DEFAULT '0000-00-00 00:00:00',
  `publish_ok` tinyint(1) DEFAULT 0,
  `contact_ok` tinyint(1) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `short_quote` text DEFAULT NULL,
  `miniquote` varchar(255) NOT NULL DEFAULT '',
  `admin_rating` tinyint(1) NOT NULL DEFAULT 0,
  `notable_quote` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__focus_area_resource_type_rel`
--

CREATE TABLE `#__focus_area_resource_type_rel` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `focus_area_id` int(11) NOT NULL,
  `resource_type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__focus_areas`
--

CREATE TABLE `#__focus_areas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL,
  `mandatory_depth` int(11) DEFAULT NULL,
  `multiple_depth` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__forum_attachments`
--

CREATE TABLE `#__forum_attachments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL DEFAULT 0,
  `post_id` int(11) NOT NULL DEFAULT 0,
  `filename` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_filename_post_id` (`filename`,`post_id`),
  KEY `idx_parent` (`parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__forum_categories`
--

CREATE TABLE `#__forum_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  `access` tinyint(2) NOT NULL DEFAULT 0,
  `state` tinyint(3) NOT NULL DEFAULT 0,
  `scope` varchar(100) NOT NULL DEFAULT 'site',
  `scope_id` int(11) NOT NULL DEFAULT 0,
  `section_id` int(11) NOT NULL DEFAULT 0,
  `closed` tinyint(2) NOT NULL DEFAULT 0,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `object_id` int(11) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_scope_scope_id` (`scope`,`scope_id`),
  KEY `idx_asset_id` (`asset_id`),
  KEY `idx_object_id` (`object_id`),
  KEY `idx_state` (`state`),
  KEY `idx_access` (`access`),
  KEY `idx_section_id` (`section_id`),
  KEY `idx_closed` (`closed`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__forum_posts`
--

CREATE TABLE `#__forum_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(3) NOT NULL DEFAULT 0,
  `sticky` tinyint(2) NOT NULL DEFAULT 0,
  `parent` int(11) NOT NULL DEFAULT 0,
  `hits` int(11) NOT NULL DEFAULT 0,
  `scope` varchar(100) NOT NULL DEFAULT 'site',
  `scope_id` int(11) NOT NULL DEFAULT 0,
  `scope_sub_id` int(11) NOT NULL DEFAULT 0,
  `access` tinyint(2) NOT NULL DEFAULT 0,
  `anonymous` tinyint(2) NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  `last_activity` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `object_id` int(11) NOT NULL DEFAULT 0,
  `lft` int(11) NOT NULL DEFAULT 0,
  `rgt` int(11) NOT NULL DEFAULT 0,
  `thread` int(11) NOT NULL DEFAULT 0,
  `closed` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_scope_scope_id` (`scope`,`scope_id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_access` (`access`),
  KEY `idx_asset_id` (`asset_id`),
  KEY `idx_object_id` (`object_id`),
  KEY `idx_state` (`state`),
  KEY `idx_sticky` (`sticky`),
  KEY `idx_parent` (`parent`),
  FULLTEXT KEY `ftidx_comment` (`comment`),
  FULLTEXT KEY `ftidx_comment_title` (`comment`,`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__forum_sections`
--

CREATE TABLE `#__forum_sections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `access` tinyint(2) NOT NULL DEFAULT 0,
  `state` tinyint(3) NOT NULL DEFAULT 0,
  `scope` varchar(100) NOT NULL DEFAULT 'site',
  `scope_id` int(11) NOT NULL DEFAULT 0,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `object_id` int(11) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_scoped` (`scope`,`scope_id`),
  KEY `idx_asset_id` (`asset_id`),
  KEY `idx_object_id` (`object_id`),
  KEY `idx_access` (`access`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__import_hooks`
--

CREATE TABLE `#__import_hooks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event` varchar(25) DEFAULT NULL,
  `type` varchar(150) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `file` varchar(100) DEFAULT NULL,
  `state` int(11) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__import_runs`
--

CREATE TABLE `#__import_runs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `import_id` int(11) DEFAULT NULL,
  `processed` int(11) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `ran_by` int(11) DEFAULT NULL,
  `ran_at` datetime DEFAULT NULL,
  `dry_run` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__imports`
--

CREATE TABLE `#__imports` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(150) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `file` varchar(255) DEFAULT '',
  `count` int(11) unsigned NOT NULL DEFAULT 0,
  `created_by` int(11) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `state` int(11) unsigned NOT NULL DEFAULT 1,
  `mode` varchar(10) DEFAULT 'UPDATE',
  `params` text DEFAULT NULL,
  `hooks` text DEFAULT NULL,
  `fields` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__incremental_registration_group_label_rel`
--

CREATE TABLE `#__incremental_registration_group_label_rel` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__incremental_registration_groups`
--

CREATE TABLE `#__incremental_registration_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hours` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__incremental_registration_labels`
--

CREATE TABLE `#__incremental_registration_labels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__incremental_registration_options`
--

CREATE TABLE `#__incremental_registration_options` (
  `added` timestamp NOT NULL DEFAULT current_timestamp(),
  `popover_text` text NOT NULL,
  `award_per` int(11) NOT NULL,
  `test_group` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__incremental_registration_popover_recurrence`
--

CREATE TABLE `#__incremental_registration_popover_recurrence` (
  `idx` int(11) NOT NULL,
  `hours` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__item_comment_files`
--

CREATE TABLE `#__item_comment_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL DEFAULT 0,
  `filename` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_comment_id` (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__item_comments`
--

CREATE TABLE `#__item_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL DEFAULT 0,
  `item_type` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  `anonymous` tinyint(2) NOT NULL DEFAULT 0,
  `parent` int(11) NOT NULL DEFAULT 0,
  `notify` tinyint(2) NOT NULL DEFAULT 0,
  `access` tinyint(2) NOT NULL DEFAULT 0,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  `positive` int(11) NOT NULL DEFAULT 0,
  `negative` int(11) NOT NULL DEFAULT 0,
  `rating` int(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_item_type_item_id` (`item_type`,`item_id`),
  KEY `idx_parent` (`parent`),
  KEY `idx_state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__item_votes`
--

CREATE TABLE `#__item_votes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL DEFAULT 0,
  `item_type` varchar(255) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `vote` tinyint(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_item_type_item_id` (`item_type`,`item_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__jobs_admins`
--

CREATE TABLE `#__jobs_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jid` int(11) NOT NULL DEFAULT 0,
  `uid` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__jobs_applications`
--

CREATE TABLE `#__jobs_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jid` int(11) NOT NULL DEFAULT 0,
  `uid` int(11) NOT NULL DEFAULT 0,
  `applied` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `withdrawn` datetime DEFAULT '0000-00-00 00:00:00',
  `cover` text DEFAULT NULL,
  `resumeid` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 1,
  `reason` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__jobs_categories`
--

CREATE TABLE `#__jobs_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(150) NOT NULL DEFAULT '',
  `ordernum` int(11) NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__jobs_employers`
--

CREATE TABLE `#__jobs_employers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0,
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `subscriptionid` int(11) NOT NULL DEFAULT 0,
  `companyName` varchar(250) DEFAULT '',
  `companyLocation` varchar(250) DEFAULT '',
  `companyWebsite` varchar(250) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__jobs_openings`
--

CREATE TABLE `#__jobs_openings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT 0,
  `employerid` int(11) NOT NULL DEFAULT 0,
  `code` int(11) NOT NULL DEFAULT 0,
  `title` varchar(200) NOT NULL DEFAULT '',
  `companyName` varchar(200) NOT NULL DEFAULT '',
  `companyLocation` varchar(200) DEFAULT '',
  `companyLocationCountry` varchar(100) DEFAULT '',
  `companyWebsite` varchar(200) DEFAULT '',
  `description` text DEFAULT NULL,
  `addedBy` int(11) NOT NULL DEFAULT 0,
  `editedBy` int(11) DEFAULT 0,
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `edited` datetime DEFAULT '0000-00-00 00:00:00',
  `status` int(3) NOT NULL DEFAULT 0,
  `type` int(3) NOT NULL DEFAULT 0,
  `closedate` datetime DEFAULT '0000-00-00 00:00:00',
  `opendate` datetime DEFAULT '0000-00-00 00:00:00',
  `startdate` datetime DEFAULT '0000-00-00 00:00:00',
  `applyExternalUrl` varchar(250) DEFAULT '',
  `applyInternal` int(3) DEFAULT 0,
  `contactName` varchar(100) DEFAULT '',
  `contactEmail` varchar(100) DEFAULT '',
  `contactPhone` varchar(100) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__jobs_prefs`
--

CREATE TABLE `#__jobs_prefs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT 0,
  `category` varchar(20) NOT NULL DEFAULT 'resume',
  `filters` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__jobs_resumes`
--

CREATE TABLE `#__jobs_resumes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` varchar(100) DEFAULT NULL,
  `filename` varchar(100) DEFAULT NULL,
  `main` tinyint(2) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__jobs_seekers`
--

CREATE TABLE `#__jobs_seekers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0,
  `active` int(11) NOT NULL DEFAULT 0,
  `lookingfor` varchar(255) DEFAULT '',
  `tagline` varchar(255) DEFAULT '',
  `linkedin` varchar(255) DEFAULT '',
  `url` varchar(255) DEFAULT '',
  `updated` datetime DEFAULT '0000-00-00 00:00:00',
  `sought_cid` int(11) DEFAULT 0,
  `sought_type` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__jobs_shortlist`
--

CREATE TABLE `#__jobs_shortlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp` int(11) NOT NULL DEFAULT 0,
  `seeker` int(11) NOT NULL DEFAULT 0,
  `category` varchar(11) NOT NULL DEFAULT 'resume',
  `jobid` int(11) DEFAULT 0,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__jobs_stats`
--

CREATE TABLE `#__jobs_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL,
  `category` varchar(11) NOT NULL DEFAULT '',
  `total_viewed` int(11) DEFAULT 0,
  `total_shared` int(11) DEFAULT 0,
  `viewed_today` int(11) DEFAULT 0,
  `lastviewed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__jobs_types`
--

CREATE TABLE `#__jobs_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__languages`
--

CREATE TABLE `#__languages` (
  `lang_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang_code` char(7) NOT NULL,
  `title` varchar(50) NOT NULL,
  `title_native` varchar(50) NOT NULL,
  `sef` varchar(50) NOT NULL,
  `image` varchar(50) NOT NULL,
  `description` varchar(512) NOT NULL,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `sitename` varchar(1024) NOT NULL DEFAULT '',
  `published` int(11) NOT NULL DEFAULT 0,
  `access` int(10) unsigned NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`lang_id`),
  UNIQUE KEY `idx_sef` (`sef`),
  UNIQUE KEY `idx_image` (`image`),
  UNIQUE KEY `idx_langcode` (`lang_code`),
  KEY `idx_access` (`access`),
  KEY `idx_ordering` (`ordering`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__licenses`
--

CREATE TABLE `#__licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__licenses_tools`
--

CREATE TABLE `#__licenses_tools` (
  `license_id` int(11) DEFAULT 0,
  `tool_id` int(11) DEFAULT 0,
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__licenses_users`
--

CREATE TABLE `#__licenses_users` (
  `license_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  PRIMARY KEY (`license_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__market_history`
--

CREATE TABLE `#__market_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL DEFAULT 0,
  `category` varchar(50) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `action` varchar(50) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `market_value` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__media_tracking`
--

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
  `total_viewing_time` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_session_id` (`session_id`),
  KEY `idx_object_id` (`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__media_tracking_detailed`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__menu`
--

CREATE TABLE `#__menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menutype` varchar(24) NOT NULL COMMENT 'The type of menu this item belongs to. FK to #__menu_types.menutype',
  `title` varchar(255) NOT NULL COMMENT 'The display title of the menu item.',
  `alias` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL COMMENT 'The SEF alias of the menu item.',
  `note` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(1024) NOT NULL COMMENT 'The computed path of the menu item based on the alias field.',
  `link` varchar(1024) NOT NULL COMMENT 'The actually link the menu item refers to.',
  `type` varchar(16) NOT NULL COMMENT 'The type of link: Component, URL, Alias, Separator',
  `published` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'The published state of the menu link.',
  `parent_id` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'The parent menu item in the menu tree.',
  `level` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'The relative level in the tree.',
  `component_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to #__extensions.id',
  `ordering` int(11) NOT NULL DEFAULT 0 COMMENT 'The relative ordering of the menu item in the tree.',
  `checked_out` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to #__users.id',
  `checked_out_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The time the menu item was checked out.',
  `browserNav` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'The click behaviour of the link.',
  `access` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'The access level required to view the menu item.',
  `img` varchar(255) NOT NULL COMMENT 'The image of the menu item.',
  `template_style_id` int(10) unsigned NOT NULL DEFAULT 0,
  `params` text NOT NULL COMMENT 'JSON encoded data for the menu item.',
  `lft` int(11) NOT NULL DEFAULT 0 COMMENT 'Nested set lft.',
  `rgt` int(11) NOT NULL DEFAULT 0 COMMENT 'Nested set rgt.',
  `home` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Indicates if this menu item is the home or default page.',
  `language` char(7) NOT NULL DEFAULT '',
  `client_id` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_client_id_parent_id_alias_language` (`client_id`,`parent_id`,`alias`,`language`),
  KEY `idx_componentid` (`component_id`,`menutype`,`published`,`access`),
  KEY `idx_menutype` (`menutype`),
  KEY `idx_left_right` (`lft`,`rgt`),
  KEY `idx_alias` (`alias`),
  KEY `idx_path` (`path`(333)),
  KEY `idx_language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__menu_types`
--

CREATE TABLE `#__menu_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menutype` varchar(24) NOT NULL,
  `title` varchar(48) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_menutype` (`menutype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__messages`
--

CREATE TABLE `#__messages` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id_from` int(10) unsigned NOT NULL DEFAULT 0,
  `user_id_to` int(10) unsigned NOT NULL DEFAULT 0,
  `folder_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `priority` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `subject` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `useridto_state` (`user_id_to`,`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__messages_cfg`
--

CREATE TABLE `#__messages_cfg` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `cfg_name` varchar(100) NOT NULL DEFAULT '',
  `cfg_value` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `idx_user_var_name` (`user_id`,`cfg_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__metrics_author_cluster`
--

CREATE TABLE `#__metrics_author_cluster` (
  `authorid` varchar(60) NOT NULL DEFAULT '0',
  `classes` int(11) DEFAULT 0,
  `users` int(11) DEFAULT 0,
  `schools` int(11) DEFAULT 0,
  PRIMARY KEY (`authorid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__metrics_ipgeo_cache`
--

CREATE TABLE `#__metrics_ipgeo_cache` (
  `ip` int(10) NOT NULL DEFAULT 0,
  `countrySHORT` char(2) NOT NULL DEFAULT '',
  `countryLONG` varchar(64) NOT NULL DEFAULT '',
  `ipREGION` varchar(128) NOT NULL DEFAULT '',
  `ipCITY` varchar(128) NOT NULL DEFAULT '',
  `ipLATITUDE` double DEFAULT NULL,
  `ipLONGITUDE` double DEFAULT NULL,
  `lookup_datetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ip`),
  KEY `idx_lookup_datetime` (`lookup_datetime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__migrations`
--

CREATE TABLE `#__migrations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `file` varchar(255) NOT NULL DEFAULT '',
  `scope` varchar(255) NOT NULL,
  `hash` char(32) NOT NULL DEFAULT '',
  `direction` varchar(10) NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  `action_by` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__modules`
--

CREATE TABLE `#__modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `position` varchar(50) NOT NULL DEFAULT '',
  `checked_out` int(10) unsigned NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `module` varchar(50) DEFAULT NULL,
  `access` int(10) unsigned NOT NULL DEFAULT 0,
  `showtitle` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `params` text NOT NULL,
  `client_id` tinyint(4) NOT NULL DEFAULT 0,
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`,`access`),
  KEY `newsfeeds` (`module`,`published`),
  KEY `idx_language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__modules_menu`
--

CREATE TABLE `#__modules_menu` (
  `moduleid` int(11) NOT NULL DEFAULT 0,
  `menuid` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`moduleid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__newsfeeds`
--

CREATE TABLE `#__newsfeeds` (
  `catid` int(11) NOT NULL DEFAULT 0,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `link` varchar(200) NOT NULL DEFAULT '',
  `filename` varchar(200) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `numarticles` int(10) unsigned NOT NULL DEFAULT 1,
  `cache_time` int(10) unsigned NOT NULL DEFAULT 3600,
  `checked_out` int(10) unsigned NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT 0,
  `rtl` tinyint(4) NOT NULL DEFAULT 0,
  `access` int(10) unsigned NOT NULL DEFAULT 0,
  `language` char(7) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT 0,
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT 0,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `metadata` text NOT NULL,
  `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`published`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_language` (`language`),
  KEY `idx_xreference` (`xreference`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__newsletter_mailing_recipient_actions`
--

CREATE TABLE `#__newsletter_mailing_recipient_actions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mailingid` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `action_vars` text DEFAULT NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__newsletter_mailing_recipients`
--

CREATE TABLE `#__newsletter_mailing_recipients` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(11) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_sent` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__newsletter_mailinglist_emails`
--

CREATE TABLE `#__newsletter_mailinglist_emails` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(11) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `confirmed` int(11) DEFAULT 0,
  `date_added` datetime DEFAULT NULL,
  `date_confirmed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__newsletter_mailinglist_unsubscribes`
--

CREATE TABLE `#__newsletter_mailinglist_unsubscribes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(11) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__newsletter_mailinglists`
--

CREATE TABLE `#__newsletter_mailinglists` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `private` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__newsletter_mailings`
--

CREATE TABLE `#__newsletter_mailings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nid` int(11) DEFAULT NULL,
  `lid` int(11) DEFAULT NULL,
  `subject` varchar(250) DEFAULT NULL,
  `html_body` longtext DEFAULT NULL,
  `plain_body` longtext DEFAULT NULL,
  `headers` text DEFAULT NULL,
  `args` text DEFAULT NULL,
  `tracking` int(11) DEFAULT 1,
  `date` datetime DEFAULT NULL,
  `deleted` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__newsletter_primary_story`
--

CREATE TABLE `#__newsletter_primary_story` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nid` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `story` text DEFAULT NULL,
  `readmore_title` varchar(100) DEFAULT NULL,
  `readmore_link` varchar(200) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__newsletter_secondary_story`
--

CREATE TABLE `#__newsletter_secondary_story` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nid` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `story` text DEFAULT NULL,
  `readmore_title` varchar(100) DEFAULT NULL,
  `readmore_link` varchar(200) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__newsletter_templates`
--

CREATE TABLE `#__newsletter_templates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `editable` int(11) DEFAULT 1,
  `name` varchar(100) DEFAULT NULL,
  `template` text DEFAULT NULL,
  `primary_title_color` varchar(100) DEFAULT NULL,
  `primary_text_color` varchar(100) DEFAULT NULL,
  `secondary_title_color` varchar(100) DEFAULT NULL,
  `secondary_text_color` varchar(100) DEFAULT NULL,
  `deleted` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__newsletters`
--

CREATE TABLE `#__newsletters` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(150) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `issue` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT 'html',
  `template` int(11) DEFAULT NULL,
  `published` int(11) DEFAULT 1,
  `sent` int(11) DEFAULT 0,
  `html_content` mediumtext DEFAULT NULL,
  `plain_content` mediumtext DEFAULT NULL,
  `tracking` int(11) DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT 0,
  `params` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__oauthp_consumers`
--

CREATE TABLE `#__oauthp_consumers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` tinyint(4) NOT NULL,
  `token` varchar(250) NOT NULL,
  `secret` varchar(250) NOT NULL,
  `callback_url` varchar(250) NOT NULL,
  `xauth` tinyint(4) NOT NULL,
  `xauth_grant` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__oauthp_nonces`
--

CREATE TABLE `#__oauthp_nonces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nonce` varchar(250) NOT NULL,
  `stamp` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_nonce_stamp` (`nonce`,`stamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__oauthp_tokens`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__order_items`
--

CREATE TABLE `#__order_items` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `oid` int(11) NOT NULL DEFAULT 0,
  `uid` int(11) NOT NULL DEFAULT 0,
  `itemid` int(11) NOT NULL DEFAULT 0,
  `price` int(11) NOT NULL DEFAULT 0,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `selections` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__orders`
--

CREATE TABLE `#__orders` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0,
  `type` varchar(20) DEFAULT NULL,
  `total` int(11) DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `details` text DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `ordered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status_changed` datetime DEFAULT '0000-00-00 00:00:00',
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__overrider`
--

CREATE TABLE `#__overrider` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `constant` varchar(255) NOT NULL,
  `string` text NOT NULL,
  `file` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__password_blacklist`
--

CREATE TABLE `#__password_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` char(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__password_character_class`
--

CREATE TABLE `#__password_character_class` (
  `flag` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(32) NOT NULL,
  `regex` char(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__password_rule`
--

CREATE TABLE `#__password_rule` (
  `class` char(255) DEFAULT NULL,
  `description` char(255) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  `failuremsg` char(255) DEFAULT NULL,
  `grp` char(32) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `rule` char(255) DEFAULT NULL,
  `value` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__plugin_params`
--

CREATE TABLE `#__plugin_params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) DEFAULT 0,
  `folder` varchar(100) DEFAULT NULL,
  `element` varchar(100) DEFAULT NULL,
  `params` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__poll_data`
--

CREATE TABLE `#__poll_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pollid` int(4) NOT NULL DEFAULT 0,
  `text` text NOT NULL,
  `hits` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_pollid_text` (`pollid`,`text`(1))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__poll_date`
--

CREATE TABLE `#__poll_date` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vote_id` int(11) NOT NULL DEFAULT 0,
  `poll_id` int(11) NOT NULL DEFAULT 0,
  `voter_ip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_poll_id` (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__poll_menu`
--

CREATE TABLE `#__poll_menu` (
  `pollid` int(11) NOT NULL DEFAULT 0,
  `menuid` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`pollid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__polls`
--

CREATE TABLE `#__polls` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `voters` int(9) NOT NULL DEFAULT 0,
  `checked_out` int(11) NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `access` int(11) NOT NULL DEFAULT 0,
  `lag` int(11) NOT NULL DEFAULT 0,
  `open` tinyint(1) NOT NULL DEFAULT 0,
  `opened` date DEFAULT NULL,
  `closed` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__profile_completion_awards`
--

CREATE TABLE `#__profile_completion_awards` (
  `user_id` int(11) NOT NULL,
  `name` tinyint(4) NOT NULL DEFAULT 0,
  `orgtype` tinyint(4) NOT NULL DEFAULT 0,
  `organization` tinyint(4) NOT NULL DEFAULT 0,
  `countryresident` tinyint(4) NOT NULL DEFAULT 0,
  `countryorigin` tinyint(4) NOT NULL DEFAULT 0,
  `gender` tinyint(4) NOT NULL DEFAULT 0,
  `url` tinyint(4) NOT NULL DEFAULT 0,
  `reason` tinyint(4) NOT NULL DEFAULT 0,
  `race` tinyint(4) NOT NULL DEFAULT 0,
  `phone` tinyint(4) NOT NULL DEFAULT 0,
  `picture` tinyint(4) NOT NULL DEFAULT 0,
  `opted_out` tinyint(4) NOT NULL DEFAULT 0,
  `logins` int(11) NOT NULL DEFAULT 1,
  `invocations` int(11) NOT NULL DEFAULT 0,
  `last_bothered` timestamp NOT NULL DEFAULT current_timestamp(),
  `bothered_times` int(11) NOT NULL DEFAULT 0,
  `edited_profile` tinyint(4) NOT NULL DEFAULT 0,
  `mailPreferenceOption` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__project_activity`
--

CREATE TABLE `#__project_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectid` int(11) NOT NULL DEFAULT 0,
  `userid` int(11) NOT NULL DEFAULT 0,
  `referenceid` varchar(255) NOT NULL DEFAULT '0',
  `managers_only` tinyint(2) DEFAULT 0,
  `admin` tinyint(2) DEFAULT 0,
  `commentable` tinyint(2) NOT NULL DEFAULT 0,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  `recorded` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activity` varchar(255) NOT NULL DEFAULT '',
  `highlighted` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(255) DEFAULT NULL,
  `class` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__project_comments`
--

CREATE TABLE `#__project_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `activityid` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  `parent_activity` int(11) DEFAULT 0,
  `anonymous` tinyint(2) DEFAULT 0,
  `admin` tinyint(2) DEFAULT 0,
  `tbl` varchar(50) NOT NULL DEFAULT 'blog',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__project_database_versions`
--

CREATE TABLE `#__project_database_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `database_name` varchar(64) NOT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `data_definition` text DEFAULT NULL,
  PRIMARY KEY (`id`,`database_name`,`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__project_databases`
--

CREATE TABLE `#__project_databases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project` int(11) NOT NULL,
  `database_name` varchar(64) NOT NULL,
  `title` varchar(127) NOT NULL DEFAULT '',
  `source_file` varchar(127) NOT NULL,
  `source_dir` varchar(127) NOT NULL,
  `source_revision` varchar(56) NOT NULL,
  `description` text DEFAULT NULL,
  `data_definition` text DEFAULT NULL,
  `revision` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__project_logs`
--

CREATE TABLE `#__project_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `projectid` int(11) unsigned NOT NULL DEFAULT 0,
  `userid` int(11) NOT NULL DEFAULT 0,
  `ajax` tinyint(1) DEFAULT 0,
  `owner` int(11) unsigned DEFAULT 0,
  `ip` varchar(15) DEFAULT '0',
  `section` varchar(100) DEFAULT 'general',
  `layout` varchar(100) DEFAULT '',
  `action` varchar(100) DEFAULT '',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `request_uri` tinytext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_projectid` (`projectid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__project_microblog`
--

CREATE TABLE `#__project_microblog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blogentry` text DEFAULT NULL,
  `posted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `posted_by` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(2) DEFAULT 0,
  `params` tinytext DEFAULT NULL,
  `projectid` int(11) NOT NULL DEFAULT 0,
  `activityid` int(11) NOT NULL DEFAULT 0,
  `managers_only` tinyint(2) DEFAULT 0,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `ftidx_blogentry` (`blogentry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__project_owners`
--

CREATE TABLE `#__project_owners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectid` int(11) NOT NULL DEFAULT 0,
  `userid` int(11) NOT NULL DEFAULT 0,
  `groupid` int(11) DEFAULT 0,
  `invited_name` varchar(100) DEFAULT NULL,
  `invited_email` varchar(100) DEFAULT NULL,
  `invited_code` varchar(10) DEFAULT NULL,
  `added` datetime NOT NULL,
  `lastvisit` datetime DEFAULT NULL,
  `prev_visit` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `num_visits` int(11) NOT NULL DEFAULT 0,
  `role` int(11) NOT NULL DEFAULT 0,
  `native` int(11) NOT NULL DEFAULT 0,
  `params` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__project_public_stamps`
--

CREATE TABLE `#__project_public_stamps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stamp` varchar(30) NOT NULL DEFAULT '0',
  `projectid` int(11) NOT NULL DEFAULT 0,
  `listed` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(50) NOT NULL DEFAULT 'files',
  `reference` text NOT NULL,
  `expires` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_stamp` (`stamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__project_remote_files`
--

CREATE TABLE `#__project_remote_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectid` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified_by` int(11) DEFAULT 0,
  `paired` int(11) DEFAULT 0,
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
  `remote_editing` tinyint(1) NOT NULL DEFAULT 0,
  `remote_id` varchar(100) NOT NULL,
  `original_id` varchar(100) NOT NULL,
  `remote_parent` varchar(100) DEFAULT NULL,
  `remote_title` varchar(140) DEFAULT NULL,
  `remote_md5` varchar(32) DEFAULT NULL,
  `remote_format` varchar(200) DEFAULT NULL,
  `remote_author` varchar(100) DEFAULT NULL,
  `remote_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__project_stats`
--

CREATE TABLE `#__project_stats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `month` int(2) DEFAULT NULL,
  `year` int(2) DEFAULT NULL,
  `week` int(2) DEFAULT NULL,
  `processed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stats` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__project_todo`
--

CREATE TABLE `#__project_todo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectid` int(11) NOT NULL DEFAULT 0,
  `todolist` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `duedate` datetime DEFAULT NULL,
  `closed` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `assigned_to` int(11) DEFAULT 0,
  `closed_by` int(11) DEFAULT 0,
  `priority` int(11) DEFAULT 0,
  `activityid` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `milestone` tinyint(1) NOT NULL DEFAULT 0,
  `private` tinyint(1) NOT NULL DEFAULT 0,
  `details` text DEFAULT NULL,
  `content` varchar(255) NOT NULL,
  `color` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__project_types`
--

CREATE TABLE `#__project_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(150) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `params` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__projects`
--

CREATE TABLE `#__projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(30) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `picture` varchar(255) DEFAULT '',
  `about` text DEFAULT NULL,
  `state` int(11) NOT NULL DEFAULT 0,
  `type` int(11) NOT NULL DEFAULT 1,
  `provisioned` int(11) NOT NULL DEFAULT 0,
  `private` int(11) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `owned_by_user` int(11) NOT NULL DEFAULT 0,
  `created_by_user` int(11) NOT NULL,
  `owned_by_group` int(11) DEFAULT 0,
  `modified_by` int(11) DEFAULT 0,
  `setup_stage` int(11) NOT NULL DEFAULT 0,
  `params` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_alias` (`alias`),
  FULLTEXT KEY `idx_fulltxt_alias_title_about` (`alias`,`title`,`about`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_access`
--

CREATE TABLE `#__publication_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_version_id` int(11) NOT NULL DEFAULT 0,
  `group_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_attachments`
--

CREATE TABLE `#__publication_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_version_id` int(11) NOT NULL DEFAULT 0,
  `publication_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified_by` int(11) DEFAULT 0,
  `object_id` int(11) DEFAULT 0,
  `object_name` varchar(64) DEFAULT '0',
  `object_instance` int(11) DEFAULT 0,
  `object_revision` int(11) DEFAULT 0,
  `role` tinyint(1) DEFAULT 0,
  `path` varchar(255) NOT NULL,
  `vcs_hash` varchar(255) DEFAULT NULL,
  `vcs_revision` varchar(255) DEFAULT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'file',
  `params` text DEFAULT NULL,
  `attribs` text DEFAULT NULL,
  `ordering` int(11) DEFAULT 0,
  `content_hash` varchar(255) DEFAULT NULL,
  `element_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_audience`
--

CREATE TABLE `#__publication_audience` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) NOT NULL DEFAULT 0,
  `publication_version_id` int(11) DEFAULT 0,
  `level0` tinyint(2) NOT NULL DEFAULT 0,
  `level1` tinyint(2) NOT NULL DEFAULT 0,
  `level2` tinyint(2) NOT NULL DEFAULT 0,
  `level3` tinyint(2) NOT NULL DEFAULT 0,
  `level4` tinyint(2) NOT NULL DEFAULT 0,
  `level5` tinyint(2) NOT NULL DEFAULT 0,
  `comments` varchar(255) DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_audience_levels`
--

CREATE TABLE `#__publication_audience_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(11) NOT NULL DEFAULT '0',
  `title` varchar(100) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_authors`
--

CREATE TABLE `#__publication_authors` (
  `publication_version_id` int(11) NOT NULL DEFAULT 0,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `project_owner_id` int(11) NOT NULL DEFAULT 0,
  `ordering` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `firstName` varchar(255) DEFAULT NULL,
  `lastName` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `credit` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified_by` int(11) DEFAULT 0,
  `status` tinyint(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_blocks`
--

CREATE TABLE `#__publication_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block` varchar(100) NOT NULL DEFAULT '',
  `label` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT 0,
  `minimum` int(11) NOT NULL DEFAULT 0,
  `maximum` int(11) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `params` text DEFAULT NULL,
  `manifest` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `block` (`block`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_categories`
--

CREATE TABLE `#__publication_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `dc_type` varchar(200) NOT NULL DEFAULT 'Dataset',
  `alias` varchar(200) NOT NULL DEFAULT '',
  `url_alias` varchar(200) NOT NULL DEFAULT '',
  `description` tinytext DEFAULT NULL,
  `contributable` int(2) DEFAULT 1,
  `state` tinyint(1) DEFAULT 1,
  `customFields` text DEFAULT NULL,
  `params` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_name` (`name`),
  UNIQUE KEY `uidx_alias` (`alias`),
  UNIQUE KEY `uidx_url_alias` (`url_alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_curation`
--

CREATE TABLE `#__publication_curation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) NOT NULL DEFAULT 0,
  `publication_version_id` int(11) NOT NULL DEFAULT 0,
  `updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT 0,
  `update` text DEFAULT NULL,
  `reviewed` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT 0,
  `review` text DEFAULT NULL,
  `review_status` int(11) NOT NULL DEFAULT 0,
  `block` varchar(100) NOT NULL DEFAULT '',
  `step` int(11) DEFAULT 0,
  `element` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_curation_history`
--

CREATE TABLE `#__publication_curation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_version_id` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `changelog` text NOT NULL,
  `curator` tinyint(3) NOT NULL DEFAULT 0,
  `oldstatus` int(11) NOT NULL DEFAULT 0,
  `newstatus` int(11) NOT NULL DEFAULT 0,
  `comment` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_handlers`
--

CREATE TABLE `#__publication_handlers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `label` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT 0,
  `about` text DEFAULT NULL,
  `params` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_licenses`
--

CREATE TABLE `#__publication_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `text` text DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `info` text DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `apps_only` int(11) NOT NULL DEFAULT 0,
  `main` int(11) NOT NULL DEFAULT 0,
  `agreement` int(11) DEFAULT 0,
  `customizable` int(11) DEFAULT 0,
  `icon` varchar(250) DEFAULT NULL,
  `opensource` tinyint(1) NOT NULL DEFAULT 0,
  `restriction` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_logs`
--

CREATE TABLE `#__publication_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) NOT NULL,
  `publication_version_id` int(11) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(2) NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `page_views` int(11) DEFAULT 0,
  `primary_accesses` int(11) DEFAULT 0,
  `support_accesses` int(11) DEFAULT 0,
  `page_views_unfiltered` int(11) DEFAULT NULL,
  `primary_accesses_unfiltered` int(11) DEFAULT NULL,
  `page_views_unique` int(11) DEFAULT NULL,
  `primary_accesses_unique` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_master_types`
--

CREATE TABLE `#__publication_master_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(200) NOT NULL DEFAULT '',
  `alias` varchar(200) NOT NULL DEFAULT '',
  `description` tinytext DEFAULT NULL,
  `contributable` int(2) DEFAULT 0,
  `supporting` int(2) DEFAULT 0,
  `ordering` int(2) DEFAULT 0,
  `params` text DEFAULT NULL,
  `curation` text DEFAULT NULL,
  `curatorgroup` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_ratings`
--

CREATE TABLE `#__publication_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) NOT NULL DEFAULT 0,
  `publication_version_id` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `comment` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anonymous` tinyint(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_screenshots`
--

CREATE TABLE `#__publication_screenshots` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `publication_version_id` int(11) NOT NULL DEFAULT 0,
  `publication_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(127) DEFAULT '',
  `ordering` int(11) DEFAULT 0,
  `filename` varchar(100) NOT NULL,
  `srcfile` varchar(100) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `created_by` varchar(127) DEFAULT NULL,
  `modified_by` varchar(127) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_stats`
--

CREATE TABLE `#__publication_stats` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `publication_id` bigint(20) NOT NULL,
  `publication_version` tinyint(4) DEFAULT NULL,
  `users` bigint(20) DEFAULT NULL,
  `downloads` bigint(20) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT -1,
  `processed_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_publication_id_datetime_period` (`publication_id`,`datetime`,`period`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publication_versions`
--

CREATE TABLE `#__publication_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) NOT NULL DEFAULT 0,
  `main` int(1) NOT NULL DEFAULT 0,
  `doi` varchar(255) DEFAULT '',
  `ark` varchar(255) DEFAULT '',
  `state` int(1) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `abstract` text NOT NULL,
  `metadata` text DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `published_up` datetime DEFAULT '0000-00-00 00:00:00',
  `published_down` datetime DEFAULT NULL,
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `accepted` datetime DEFAULT '0000-00-00 00:00:00',
  `archived` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `submitted` datetime DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT 0,
  `version_label` varchar(100) NOT NULL DEFAULT '1.0',
  `secret` varchar(10) NOT NULL DEFAULT '',
  `version_number` int(11) NOT NULL DEFAULT 0,
  `params` text DEFAULT NULL,
  `release_notes` text DEFAULT NULL,
  `license_text` text DEFAULT NULL,
  `license_type` int(11) DEFAULT NULL,
  `access` int(11) NOT NULL DEFAULT 0,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `times_rated` int(11) NOT NULL DEFAULT 0,
  `ranking` float NOT NULL DEFAULT 0,
  `curation` text DEFAULT NULL,
  `reviewed` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `curator` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `idx_fulltxt_title_description_abstract` (`title`,`description`,`abstract`),
  FULLTEXT KEY `ftidx_title` (`title`),
  FULLTEXT KEY `ftidx_abstract_description` (`abstract`,`description`),
  FULLTEXT KEY `ftidx_title_abstract_description` (`title`,`abstract`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__publications`
--

CREATE TABLE `#__publications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL DEFAULT 0,
  `master_type` int(11) NOT NULL DEFAULT 1,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `access` int(11) NOT NULL DEFAULT 0,
  `checked_out` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `times_rated` int(11) NOT NULL DEFAULT 0,
  `alias` varchar(100) NOT NULL DEFAULT '',
  `ranking` float NOT NULL DEFAULT 0,
  `group_owner` int(11) NOT NULL DEFAULT 0,
  `master_doi` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__recent_tools`
--

CREATE TABLE `#__recent_tools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0,
  `tool` varchar(200) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__recommendation`
--

CREATE TABLE `#__recommendation` (
  `fromID` int(11) NOT NULL,
  `toID` int(11) NOT NULL,
  `contentScore` float unsigned zerofill DEFAULT NULL,
  `tagScore` float unsigned zerofill DEFAULT NULL,
  `titleScore` float unsigned zerofill DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`fromID`,`toID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__redirect_links`
--

CREATE TABLE `#__redirect_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `old_url` varchar(255) NOT NULL,
  `new_url` varchar(255) NOT NULL,
  `referer` varchar(150) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT 0,
  `published` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_link_old` (`old_url`),
  KEY `idx_link_modifed` (`modified_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__redirection`
--

CREATE TABLE `#__redirection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cpt` int(11) NOT NULL DEFAULT 0,
  `oldurl` varchar(100) NOT NULL DEFAULT '',
  `newurl` varchar(150) NOT NULL DEFAULT '',
  `dateadd` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `idx_newurl` (`newurl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_assoc`
--

CREATE TABLE `#__resource_assoc` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `child_id` int(11) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `grouping` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idx_parent_id_child_id` (`parent_id`,`child_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_import_hooks`
--

CREATE TABLE `#__resource_import_hooks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(25) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `file` varchar(100) DEFAULT NULL,
  `state` int(11) DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_import_runs`
--

CREATE TABLE `#__resource_import_runs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `import_id` int(11) DEFAULT NULL,
  `processed` int(11) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `ran_by` int(11) DEFAULT NULL,
  `ran_at` datetime DEFAULT NULL,
  `dry_run` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_imports`
--

CREATE TABLE `#__resource_imports` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `file` varchar(255) DEFAULT '',
  `count` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `state` int(11) DEFAULT 1,
  `mode` varchar(10) DEFAULT 'UPDATE',
  `params` text DEFAULT NULL,
  `hooks` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_licenses`
--

CREATE TABLE `#__resource_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `apps_only` tinyint(3) NOT NULL DEFAULT 0,
  `main` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `agreement` tinyint(2) NOT NULL DEFAULT 0,
  `info` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_ratings`
--

CREATE TABLE `#__resource_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `comment` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anonymous` tinyint(3) NOT NULL DEFAULT 0,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_sponsors`
--

CREATE TABLE `#__resource_sponsors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `state` tinyint(3) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_stats`
--

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
  `period` tinyint(4) NOT NULL DEFAULT -1,
  `processed_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_resid_restype_datetime_period` (`resid`,`restype`,`datetime`,`period`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_stats_clusters`
--

CREATE TABLE `#__resource_stats_clusters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cluster` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(32) NOT NULL DEFAULT '',
  `uidNumber` int(11) NOT NULL DEFAULT 0,
  `toolname` varchar(80) NOT NULL DEFAULT '',
  `resid` int(11) NOT NULL DEFAULT 0,
  `clustersize` varchar(255) NOT NULL DEFAULT '',
  `cluster_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cluster_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `institution` varchar(255) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_stats_tools`
--

CREATE TABLE `#__resource_stats_tools` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `resid` bigint(20) NOT NULL,
  `restype` int(11) NOT NULL,
  `users` bigint(20) DEFAULT NULL,
  `sessions` bigint(20) DEFAULT NULL,
  `simulations` bigint(20) DEFAULT NULL,
  `jobs` bigint(20) DEFAULT NULL,
  `avg_wall` double unsigned DEFAULT 0,
  `tot_wall` double unsigned DEFAULT 0,
  `avg_cpu` double unsigned DEFAULT 0,
  `tot_cpu` double unsigned DEFAULT 0,
  `avg_view` double unsigned DEFAULT 0,
  `tot_view` double unsigned DEFAULT 0,
  `avg_wait` double unsigned DEFAULT 0,
  `tot_wait` double unsigned DEFAULT 0,
  `avg_cpus` int(20) DEFAULT NULL,
  `tot_cpus` int(20) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT -1,
  `processed_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_stats_tools_tops`
--

CREATE TABLE `#__resource_stats_tools_tops` (
  `top` tinyint(4) NOT NULL DEFAULT 0,
  `name` varchar(128) NOT NULL DEFAULT '',
  `valfmt` tinyint(4) NOT NULL DEFAULT 0,
  `size` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`top`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_stats_tools_topvals`
--

CREATE TABLE `#__resource_stats_tools_topvals` (
  `id` bigint(20) NOT NULL,
  `top` tinyint(4) NOT NULL DEFAULT 0,
  `rank` tinyint(4) NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `value` bigint(20) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_stats_tools_users`
--

CREATE TABLE `#__resource_stats_tools_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `resid` bigint(20) NOT NULL,
  `restype` int(11) NOT NULL,
  `user` varchar(32) NOT NULL DEFAULT '',
  `sessions` bigint(20) DEFAULT NULL,
  `simulations` bigint(20) DEFAULT NULL,
  `jobs` bigint(20) DEFAULT NULL,
  `tot_wall` double unsigned DEFAULT 0,
  `tot_cpu` double unsigned DEFAULT 0,
  `tot_view` double unsigned DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT -1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_taxonomy_audience`
--

CREATE TABLE `#__resource_taxonomy_audience` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL DEFAULT 0,
  `versionid` int(11) DEFAULT 0,
  `level0` tinyint(2) NOT NULL DEFAULT 0,
  `level1` tinyint(2) NOT NULL DEFAULT 0,
  `level2` tinyint(2) NOT NULL DEFAULT 0,
  `level3` tinyint(2) NOT NULL DEFAULT 0,
  `level4` tinyint(2) NOT NULL DEFAULT 0,
  `level5` tinyint(2) NOT NULL DEFAULT 0,
  `comments` varchar(255) DEFAULT '',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `addedBy` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_taxonomy_audience_levels`
--

CREATE TABLE `#__resource_taxonomy_audience_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(11) NOT NULL DEFAULT '0',
  `title` varchar(100) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resource_types`
--

CREATE TABLE `#__resource_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(100) DEFAULT NULL,
  `type` varchar(200) NOT NULL DEFAULT '',
  `category` int(11) NOT NULL DEFAULT 0,
  `description` tinytext DEFAULT NULL,
  `contributable` int(2) DEFAULT 1,
  `customFields` text DEFAULT NULL,
  `params` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__resources`
--

CREATE TABLE `#__resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `type` int(11) NOT NULL DEFAULT 0,
  `logical_type` int(11) NOT NULL DEFAULT 0,
  `introtext` text NOT NULL,
  `fulltxt` text NOT NULL,
  `footertext` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  `published` int(1) NOT NULL DEFAULT 0,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(11) NOT NULL DEFAULT 0,
  `hits` int(11) NOT NULL DEFAULT 0,
  `path` varchar(200) NOT NULL DEFAULT '',
  `checked_out` int(11) NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `standalone` tinyint(1) NOT NULL DEFAULT 0,
  `group_owner` varchar(250) NOT NULL DEFAULT '',
  `group_access` text DEFAULT NULL,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `times_rated` int(11) NOT NULL DEFAULT 0,
  `params` text DEFAULT NULL,
  `attribs` text DEFAULT NULL,
  `alias` varchar(100) NOT NULL DEFAULT '',
  `ranking` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `ftidx_title` (`title`),
  FULLTEXT KEY `ftidx_introtext_fulltxt` (`introtext`,`fulltxt`),
  FULLTEXT KEY `ftidx_title_introtext_fulltxt` (`title`,`introtext`,`fulltxt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__schemas`
--

CREATE TABLE `#__schemas` (
  `extension_id` int(11) NOT NULL,
  `version_id` varchar(20) NOT NULL,
  PRIMARY KEY (`extension_id`,`version_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__screenshots`
--

CREATE TABLE `#__screenshots` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `versionid` int(11) DEFAULT 0,
  `title` varchar(127) DEFAULT '',
  `ordering` int(11) DEFAULT 0,
  `filename` varchar(100) NOT NULL,
  `resourceid` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__session`
--

CREATE TABLE `#__session` (
  `session_id` varchar(200) NOT NULL DEFAULT '',
  `client_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `guest` tinyint(4) unsigned DEFAULT 1,
  `time` varchar(14) DEFAULT '',
  `data` mediumtext DEFAULT NULL,
  `userid` int(11) DEFAULT 0,
  `username` varchar(150) DEFAULT '',
  `usertype` varchar(50) DEFAULT '',
  `ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  KEY `whosonline` (`guest`,`usertype`),
  KEY `userid` (`userid`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__session_geo`
--

CREATE TABLE `#__session_geo` (
  `session_id` varchar(200) NOT NULL DEFAULT '0',
  `username` varchar(150) DEFAULT '',
  `time` varchar(14) DEFAULT '',
  `guest` tinyint(4) DEFAULT 1,
  `userid` int(11) DEFAULT 0,
  `ip` varchar(15) DEFAULT NULL,
  `host` varchar(128) DEFAULT NULL,
  `domain` varchar(128) DEFAULT NULL,
  `signed` tinyint(3) DEFAULT 0,
  `countrySHORT` char(2) DEFAULT NULL,
  `countryLONG` varchar(64) DEFAULT NULL,
  `ipREGION` varchar(128) DEFAULT NULL,
  `ipCITY` varchar(128) DEFAULT NULL,
  `ipLATITUDE` double DEFAULT NULL,
  `ipLONGITUDE` double DEFAULT NULL,
  `bot` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`session_id`),
  KEY `idx_userid` (`userid`),
  KEY `idx_time` (`time`),
  KEY `idx_ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__session_log`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__stats_tops`
--

CREATE TABLE `#__stats_tops` (
  `id` tinyint(4) NOT NULL DEFAULT 0,
  `name` varchar(128) NOT NULL DEFAULT '',
  `valfmt` tinyint(4) NOT NULL DEFAULT 0,
  `size` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__stats_topvals`
--

CREATE TABLE `#__stats_topvals` (
  `top` tinyint(4) NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL DEFAULT 1,
  `rank` smallint(6) NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `value` bigint(20) NOT NULL DEFAULT 0,
  KEY `idx_top` (`top`),
  KEY `idx_top_rank` (`top`,`rank`),
  KEY `idx_top_datetime` (`top`,`datetime`),
  KEY `idx_top_datetime_rank` (`top`,`datetime`,`rank`),
  KEY `idx_top_datetime_period` (`top`,`datetime`,`period`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__store`
--

CREATE TABLE `#__store` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(127) NOT NULL DEFAULT '',
  `price` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `available` int(1) NOT NULL DEFAULT 0,
  `params` text DEFAULT NULL,
  `special` int(11) DEFAULT 0,
  `type` int(11) DEFAULT 1,
  `category` varchar(127) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__storefront_collections`
--

CREATE TABLE `#__storefront_collections` (
  `cId` char(50) NOT NULL,
  `cName` varchar(64) DEFAULT NULL,
  `cParent` int(16) DEFAULT NULL,
  `cActive` tinyint(1) DEFAULT NULL,
  `cType` char(10) DEFAULT NULL,
  PRIMARY KEY (`cId`),
  KEY `idx_cActive` (`cActive`),
  KEY `idx_cParent` (`cParent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__storefront_coupon_actions`
--

CREATE TABLE `#__storefront_coupon_actions` (
  `cnId` int(16) NOT NULL,
  `cnaAction` char(25) DEFAULT NULL,
  `cnaVal` char(255) DEFAULT NULL,
  UNIQUE KEY `uidx_cnId_cnaAction` (`cnId`,`cnaAction`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__storefront_coupon_conditions`
--

CREATE TABLE `#__storefront_coupon_conditions` (
  `cnId` int(16) NOT NULL,
  `cncRule` char(100) DEFAULT NULL,
  `cncVal` char(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__storefront_coupon_objects`
--

CREATE TABLE `#__storefront_coupon_objects` (
  `cnId` int(16) NOT NULL,
  `cnoObjectId` int(16) DEFAULT NULL,
  `cnoObjectsLimit` int(5) DEFAULT 0 COMMENT 'How many objects can be applied to. 0 - unlimited',
  UNIQUE KEY `uidx_cnId_cnoObjectId` (`cnId`,`cnoObjectId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__storefront_coupons`
--

CREATE TABLE `#__storefront_coupons` (
  `cnId` int(16) NOT NULL AUTO_INCREMENT,
  `cnCode` char(25) DEFAULT NULL,
  `cnDescription` char(255) DEFAULT NULL,
  `cnExpires` date DEFAULT NULL,
  `cnUseLimit` int(5) unsigned DEFAULT NULL,
  `cnObject` char(15) NOT NULL,
  `cnActive` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`cnId`),
  UNIQUE KEY `uidx_cnCode` (`cnCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__storefront_option_groups`
--

CREATE TABLE `#__storefront_option_groups` (
  `ogId` int(16) NOT NULL AUTO_INCREMENT,
  `ogName` char(16) DEFAULT NULL,
  PRIMARY KEY (`ogId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__storefront_options`
--

CREATE TABLE `#__storefront_options` (
  `oId` int(16) NOT NULL AUTO_INCREMENT,
  `ogId` int(16) DEFAULT NULL COMMENT 'Foreign key to option-groups',
  `oName` char(255) DEFAULT NULL,
  PRIMARY KEY (`oId`),
  UNIQUE KEY `uidx_ogId_oName` (`ogId`,`oName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__storefront_product_collections`
--

CREATE TABLE `#__storefront_product_collections` (
  `cllId` int(16) NOT NULL AUTO_INCREMENT,
  `pId` int(16) NOT NULL,
  `cId` char(50) NOT NULL,
  PRIMARY KEY (`cllId`,`pId`,`cId`),
  UNIQUE KEY `uidx_pId_cId` (`pId`,`cId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__storefront_product_option_groups`
--

CREATE TABLE `#__storefront_product_option_groups` (
  `pId` int(16) NOT NULL,
  `ogId` int(16) NOT NULL,
  PRIMARY KEY (`pId`,`ogId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__storefront_product_types`
--

CREATE TABLE `#__storefront_product_types` (
  `ptId` int(16) NOT NULL AUTO_INCREMENT,
  `ptName` char(128) DEFAULT NULL,
  `ptModel` char(25) DEFAULT 'normal',
  PRIMARY KEY (`ptId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__storefront_products`
--

CREATE TABLE `#__storefront_products` (
  `pId` int(16) NOT NULL AUTO_INCREMENT,
  `ptId` int(16) NOT NULL COMMENT 'Product type ID. Foreign key to product_types table',
  `pName` char(128) DEFAULT NULL,
  `pTagline` tinytext DEFAULT NULL,
  `pDescription` text DEFAULT NULL,
  `pFeatures` text DEFAULT NULL,
  `pActive` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`pId`),
  KEY `idx_pActive` (`pActive`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__storefront_sku_meta`
--

CREATE TABLE `#__storefront_sku_meta` (
  `smId` int(16) NOT NULL AUTO_INCREMENT,
  `sId` int(16) NOT NULL,
  `smKey` varchar(100) DEFAULT NULL,
  `smValue` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`smId`),
  UNIQUE KEY `uidx_sId_smKey` (`sId`,`smKey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__storefront_sku_options`
--

CREATE TABLE `#__storefront_sku_options` (
  `sId` int(16) NOT NULL,
  `oId` int(16) NOT NULL,
  PRIMARY KEY (`sId`,`oId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__storefront_skus`
--

CREATE TABLE `#__storefront_skus` (
  `sId` int(16) NOT NULL AUTO_INCREMENT,
  `pId` int(16) DEFAULT NULL COMMENT 'Foreign key to products',
  `sSku` char(16) DEFAULT NULL,
  `sWeight` decimal(10,2) DEFAULT NULL,
  `sPrice` decimal(10,2) DEFAULT NULL,
  `sDescriprtion` text DEFAULT NULL,
  `sFeatures` text DEFAULT NULL,
  `sTrackInventory` tinyint(1) DEFAULT 0,
  `sInventory` int(11) DEFAULT 0,
  `sEnumerable` tinyint(1) DEFAULT 1,
  `sAllowMultiple` tinyint(1) DEFAULT 1,
  `sActive` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`sId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__support_acl_acos`
--

CREATE TABLE `#__support_acl_acos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(100) NOT NULL DEFAULT '',
  `foreign_key` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__support_acl_aros`
--

CREATE TABLE `#__support_acl_aros` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(100) NOT NULL DEFAULT '',
  `foreign_key` int(11) NOT NULL DEFAULT 0,
  `alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_model_foreign_key` (`model`,`foreign_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__support_acl_aros_acos`
--

CREATE TABLE `#__support_acl_aros_acos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aro_id` int(11) unsigned NOT NULL DEFAULT 0,
  `aco_id` int(11) unsigned NOT NULL DEFAULT 0,
  `action_create` tinyint(3) NOT NULL DEFAULT 0,
  `action_read` tinyint(3) NOT NULL DEFAULT 0,
  `action_update` tinyint(3) NOT NULL DEFAULT 0,
  `action_delete` tinyint(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_aco_id` (`aco_id`),
  KEY `idx_aro_id` (`aro_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__support_attachments`
--

CREATE TABLE `#__support_attachments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket` int(11) NOT NULL DEFAULT 0,
  `filename` varchar(255) DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `comment_id` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_ticket` (`ticket`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__support_categories`
--

CREATE TABLE `#__support_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT 0,
  `alias` varchar(250) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__support_comments`
--

CREATE TABLE `#__support_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket` int(11) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `changelog` text NOT NULL,
  `access` tinyint(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_ticket` (`ticket`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__support_messages`
--

CREATE TABLE `#__support_messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__support_queries`
--

CREATE TABLE `#__support_queries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `conditions` text NOT NULL,
  `query` text NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `sort` varchar(100) NOT NULL DEFAULT '',
  `sort_dir` varchar(100) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `iscore` int(3) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `folder_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_iscore` (`iscore`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__support_query_folders`
--

CREATE TABLE `#__support_query_folders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `title` varchar(200) NOT NULL DEFAULT '',
  `alias` varchar(200) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `iscore` tinyint(2) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__support_resolutions`
--

CREATE TABLE `#__support_resolutions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `alias` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__support_sections`
--

CREATE TABLE `#__support_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__support_statuses`
--

CREATE TABLE `#__support_statuses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `open` tinyint(2) NOT NULL DEFAULT 0,
  `title` varchar(250) NOT NULL DEFAULT '',
  `alias` varchar(250) NOT NULL DEFAULT '',
  `color` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_open` (`open`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__support_tickets`
--

CREATE TABLE `#__support_tickets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `closed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `login` varchar(200) NOT NULL DEFAULT '',
  `severity` varchar(30) NOT NULL DEFAULT '',
  `owner` int(11) NOT NULL DEFAULT 0,
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
  `cookies` tinyint(3) NOT NULL DEFAULT 0,
  `instances` int(11) NOT NULL DEFAULT 1,
  `section` int(11) NOT NULL DEFAULT 1,
  `type` tinyint(3) NOT NULL DEFAULT 0,
  `group` varchar(250) NOT NULL DEFAULT '',
  `open` tinyint(3) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_owner` (`owner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__support_watching`
--

CREATE TABLE `#__support_watching` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_ticket_id` (`ticket_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tags`
--

CREATE TABLE `#__tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(100) NOT NULL DEFAULT '',
  `raw_tag` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `admin` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_tag` (`tag`),
  FULLTEXT KEY `ftidx_description` (`description`),
  FULLTEXT KEY `ftidx_raw_tag_description` (`raw_tag`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tags_log`
--

CREATE TABLE `#__tags_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) unsigned NOT NULL DEFAULT 0,
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `action` varchar(50) NOT NULL DEFAULT '',
  `comments` text NOT NULL,
  `actorid` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_tag_id` (`tag_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tags_object`
--

CREATE TABLE `#__tags_object` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `objectid` int(11) unsigned NOT NULL DEFAULT 0,
  `tagid` int(11) unsigned NOT NULL DEFAULT 0,
  `strength` tinyint(3) NOT NULL DEFAULT 0,
  `taggerid` int(11) unsigned NOT NULL DEFAULT 0,
  `taggedon` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tbl` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_objectid_tbl` (`objectid`,`tbl`),
  KEY `idx_label_tagid` (`label`,`tagid`),
  KEY `idx_tbl_objectid_label_tagid` (`tbl`,`objectid`,`label`,`tagid`),
  KEY `idx_tagid` (`tagid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tags_substitute`
--

CREATE TABLE `#__tags_substitute` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) unsigned NOT NULL DEFAULT 0,
  `tag` varchar(100) NOT NULL DEFAULT '',
  `raw_tag` varchar(100) NOT NULL DEFAULT '',
  `created_by` int(11) unsigned NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_tag_id` (`tag_id`),
  KEY `idx_tag` (`tag`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__template_styles`
--

CREATE TABLE `#__template_styles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template` varchar(50) NOT NULL DEFAULT '',
  `client_id` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `home` char(7) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_template` (`template`),
  KEY `idx_home` (`home`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tool`
--

CREATE TABLE `#__tool` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `toolname` varchar(64) NOT NULL DEFAULT '',
  `title` varchar(127) NOT NULL DEFAULT '',
  `version` varchar(15) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `fulltxt` text DEFAULT NULL,
  `license` text DEFAULT NULL,
  `toolaccess` varchar(15) DEFAULT NULL,
  `codeaccess` varchar(15) DEFAULT NULL,
  `wikiaccess` varchar(15) DEFAULT NULL,
  `published` tinyint(1) DEFAULT 0,
  `state` int(15) DEFAULT NULL,
  `priority` int(15) DEFAULT 3,
  `team` text DEFAULT NULL,
  `registered` datetime DEFAULT NULL,
  `registered_by` varchar(31) DEFAULT NULL,
  `mw` varchar(31) DEFAULT NULL,
  `vnc_geometry` varchar(31) DEFAULT NULL,
  `ticketid` int(15) DEFAULT NULL,
  `state_changed` datetime DEFAULT '0000-00-00 00:00:00',
  `revision` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_toolname` (`toolname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tool_authors`
--

CREATE TABLE `#__tool_authors` (
  `toolname` varchar(50) NOT NULL DEFAULT '',
  `revision` int(15) NOT NULL DEFAULT 0,
  `uid` int(11) NOT NULL DEFAULT 0,
  `ordering` int(11) DEFAULT 0,
  `version_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`toolname`,`revision`,`uid`,`version_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tool_groups`
--

CREATE TABLE `#__tool_groups` (
  `cn` varchar(255) NOT NULL DEFAULT '',
  `toolid` int(11) NOT NULL DEFAULT 0,
  `role` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`cn`,`toolid`,`role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tool_licenses`
--

CREATE TABLE `#__tool_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tool_statusviews`
--

CREATE TABLE `#__tool_statusviews` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ticketid` varchar(15) NOT NULL DEFAULT '',
  `uid` varchar(31) NOT NULL DEFAULT '',
  `viewed` datetime DEFAULT '0000-00-00 00:00:00',
  `elapsed` int(11) DEFAULT 500000,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tool_version`
--

CREATE TABLE `#__tool_version` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `toolname` varchar(64) NOT NULL DEFAULT '',
  `instance` varchar(31) NOT NULL DEFAULT '',
  `title` varchar(127) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `fulltxt` text DEFAULT NULL,
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
  `license` text DEFAULT NULL,
  `vnc_geometry` varchar(31) DEFAULT NULL,
  `vnc_depth` int(11) DEFAULT NULL,
  `vnc_timeout` int(11) DEFAULT NULL,
  `vnc_command` varchar(100) DEFAULT NULL,
  `mw` varchar(31) DEFAULT NULL,
  `toolid` int(11) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `params` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_toolname_instance` (`toolname`,`instance`),
  KEY `idx_instance` (`instance`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tool_version_alias`
--

CREATE TABLE `#__tool_version_alias` (
  `tool_version_id` int(11) NOT NULL,
  `alias` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tool_version_hostreq`
--

CREATE TABLE `#__tool_version_hostreq` (
  `tool_version_id` int(11) NOT NULL,
  `hostreq` varchar(255) NOT NULL,
  UNIQUE KEY `idx_tool_version_id_hostreq` (`tool_version_id`,`hostreq`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tool_version_middleware`
--

CREATE TABLE `#__tool_version_middleware` (
  `tool_version_id` int(11) NOT NULL,
  `middleware` varchar(255) NOT NULL,
  UNIQUE KEY `uidx_tool_version_id_middleware` (`tool_version_id`,`middleware`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tool_version_tracperm`
--

CREATE TABLE `#__tool_version_tracperm` (
  `tool_version_id` int(11) NOT NULL,
  `tracperm` varchar(64) NOT NULL,
  UNIQUE KEY `uidx_tool_version_id_tracperm` (`tool_version_id`,`tracperm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__tool_version_zone`
--

CREATE TABLE `#__tool_version_zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tool_version_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `publish_up` datetime DEFAULT NULL,
  `publish_down` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_zoneid_toolversionid` (`zone_id`,`tool_version_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__trac_group_permission`
--

CREATE TABLE `#__trac_group_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `trac_project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_group_id_action_trac_project_id` (`group_id`,`action`,`trac_project_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__trac_project`
--

CREATE TABLE `#__trac_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__trac_projects`
--

CREATE TABLE `#__trac_projects` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__trac_user_permission`
--

CREATE TABLE `#__trac_user_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `trac_project_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_user_id_action_trac_project_id` (`user_id`,`action`,`trac_project_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__update_categories`
--

CREATE TABLE `#__update_categories` (
  `categoryid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT '',
  `description` text NOT NULL,
  `parent` int(11) DEFAULT 0,
  `updatesite` int(11) DEFAULT 0,
  PRIMARY KEY (`categoryid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Update Categories';

--
-- Table structure for table `#__update_sites`
--

CREATE TABLE `#__update_sites` (
  `update_site_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT '',
  `type` varchar(20) DEFAULT '',
  `location` text NOT NULL,
  `enabled` int(11) DEFAULT 0,
  `last_check_timestamp` bigint(20) DEFAULT 0,
  PRIMARY KEY (`update_site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Update Sites';

--
-- Table structure for table `#__update_sites_extensions`
--

CREATE TABLE `#__update_sites_extensions` (
  `update_site_id` int(11) NOT NULL DEFAULT 0,
  `extension_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`update_site_id`,`extension_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Links extensions to update sites';

--
-- Table structure for table `#__updates`
--

CREATE TABLE `#__updates` (
  `update_id` int(11) NOT NULL AUTO_INCREMENT,
  `update_site_id` int(11) DEFAULT 0,
  `extension_id` int(11) DEFAULT 0,
  `categoryid` int(11) DEFAULT 0,
  `name` varchar(100) DEFAULT '',
  `description` text NOT NULL,
  `element` varchar(100) DEFAULT '',
  `type` varchar(20) DEFAULT '',
  `folder` varchar(20) DEFAULT '',
  `client_id` tinyint(3) DEFAULT 0,
  `version` varchar(10) DEFAULT '',
  `data` text NOT NULL,
  `detailsurl` text NOT NULL,
  `infourl` text NOT NULL,
  PRIMARY KEY (`update_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Available Updates';

--
-- Table structure for table `#__user_notes`
--

CREATE TABLE `#__user_notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `catid` int(10) unsigned NOT NULL DEFAULT 0,
  `subject` varchar(100) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT 0,
  `checked_out` int(10) unsigned NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(10) unsigned NOT NULL,
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_category_id` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__user_profiles`
--

CREATE TABLE `#__user_profiles` (
  `user_id` int(11) NOT NULL,
  `profile_key` varchar(100) NOT NULL,
  `profile_value` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  UNIQUE KEY `idx_user_id_profile_key` (`user_id`,`profile_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Simple user profile storage table';

--
-- Table structure for table `#__user_roles`
--

CREATE TABLE `#__user_roles` (
  `user_id` int(11) NOT NULL,
  `role` varchar(20) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_role_user_id_group_id` (`role`,`user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__user_usergroup_map`
--

CREATE TABLE `#__user_usergroup_map` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__users.id',
  `group_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__usergroups.id',
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__usergroups`
--

CREATE TABLE `#__usergroups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `parent_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Adjacency List Reference Id',
  `lft` int(11) NOT NULL DEFAULT 0 COMMENT 'Nested set lft.',
  `rgt` int(11) NOT NULL DEFAULT 0 COMMENT 'Nested set rgt.',
  `title` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_usergroup_parent_title_lookup` (`parent_id`,`title`),
  KEY `idx_usergroup_title_lookup` (`title`),
  KEY `idx_usergroup_adjacency_lookup` (`parent_id`),
  KEY `idx_usergroup_nested_set_lookup` (`lft`,`rgt`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__users`
--

CREATE TABLE `#__users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(150) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(127) NOT NULL DEFAULT '',
  `usertype` varchar(25) NOT NULL DEFAULT '',
  `block` tinyint(4) NOT NULL DEFAULT 0,
  `approved` tinyint(4) NOT NULL DEFAULT 2,
  `sendEmail` tinyint(4) DEFAULT 0,
  `registerDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activation` varchar(100) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  `lastResetTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date of last password reset',
  `resetCount` int(11) NOT NULL DEFAULT 0 COMMENT 'Count of password resets since lastResetTime',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_username` (`username`),
  KEY `usertype` (`usertype`),
  KEY `idx_name` (`name`),
  KEY `idx_block` (`block`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__users_merge_log`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__users_password`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__users_password_history`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__users_points`
--

CREATE TABLE `#__users_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0,
  `balance` int(11) NOT NULL DEFAULT 0,
  `earnings` int(11) NOT NULL DEFAULT 0,
  `credit` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__users_points_config`
--

CREATE TABLE `#__users_points_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `points` int(11) DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `alias` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__users_points_services`
--

CREATE TABLE `#__users_points_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `category` varchar(50) NOT NULL DEFAULT '',
  `alias` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `unitprice` float(6,2) DEFAULT 0.00,
  `pointsprice` int(11) DEFAULT 0,
  `currency` varchar(50) DEFAULT 'points',
  `maxunits` int(11) DEFAULT 0,
  `minunits` int(11) DEFAULT 0,
  `unitsize` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `restricted` int(11) DEFAULT 0,
  `ordering` int(11) DEFAULT 0,
  `params` text DEFAULT NULL,
  `unitmeasure` varchar(200) NOT NULL DEFAULT '',
  `changed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__users_points_subscriptions`
--

CREATE TABLE `#__users_points_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0,
  `serviceid` int(11) NOT NULL DEFAULT 0,
  `units` int(11) NOT NULL DEFAULT 1,
  `status` int(11) NOT NULL DEFAULT 0,
  `pendingunits` int(11) DEFAULT 0,
  `pendingpayment` float(6,2) DEFAULT 0.00,
  `totalpaid` float(6,2) DEFAULT 0.00,
  `installment` int(11) DEFAULT 0,
  `contact` varchar(20) DEFAULT '',
  `code` varchar(10) DEFAULT '',
  `usepoints` tinyint(2) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `added` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__users_quotas`
--

CREATE TABLE `#__users_quotas` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `hard_files` int(11) NOT NULL,
  `soft_files` int(11) NOT NULL,
  `hard_blocks` int(11) NOT NULL,
  `soft_blocks` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__users_quotas_classes`
--

CREATE TABLE `#__users_quotas_classes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `hard_files` int(11) NOT NULL,
  `soft_files` int(11) NOT NULL,
  `hard_blocks` int(11) NOT NULL,
  `soft_blocks` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__users_quotas_classes_groups`
--

CREATE TABLE `#__users_quotas_classes_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int(11) unsigned NOT NULL DEFAULT 0,
  `group_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_class_id` (`class_id`),
  KEY `idx_group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__users_quotas_log`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__users_tracperms`
--

CREATE TABLE `#__users_tracperms` (
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__users_transactions`
--

CREATE TABLE `#__users_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0,
  `type` varchar(20) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `category` varchar(50) DEFAULT NULL,
  `referenceid` int(11) DEFAULT 0,
  `amount` int(11) DEFAULT 0,
  `balance` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_referenceid_category_type` (`referenceid`,`category`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__viewlevels`
--

CREATE TABLE `#__viewlevels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `title` varchar(100) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT 0,
  `rules` varchar(5120) NOT NULL COMMENT 'JSON encoded access control.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_assetgroup_title_lookup` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__vote_log`
--

CREATE TABLE `#__vote_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referenceid` int(11) NOT NULL DEFAULT 0,
  `voted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `voter` int(11) DEFAULT NULL,
  `helpful` varchar(11) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_referenceid` (`referenceid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wiki_attachments`
--

CREATE TABLE `#__wiki_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageid` int(11) DEFAULT 0,
  `filename` varchar(255) DEFAULT NULL,
  `description` tinytext DEFAULT NULL,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_pageid` (`pageid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wiki_comments`
--

CREATE TABLE `#__wiki_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageid` int(11) NOT NULL DEFAULT 0,
  `version` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `ctext` text DEFAULT NULL,
  `chtml` text DEFAULT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 0,
  `anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `parent` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_pageid` (`pageid`),
  KEY `idx_version` (`version`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wiki_log`
--

CREATE TABLE `#__wiki_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0,
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid` int(11) DEFAULT 0,
  `action` varchar(50) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `actorid` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wiki_math`
--

CREATE TABLE `#__wiki_math` (
  `inputhash` varchar(32) NOT NULL DEFAULT '',
  `outputhash` varchar(32) NOT NULL DEFAULT '',
  `conservativeness` tinyint(4) NOT NULL,
  `html` text DEFAULT NULL,
  `mathml` text DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_inputhash` (`inputhash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wiki_page`
--

CREATE TABLE `#__wiki_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pagename` varchar(100) DEFAULT NULL,
  `hits` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `times_rated` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `scope` varchar(255) NOT NULL,
  `params` tinytext DEFAULT NULL,
  `ranking` float DEFAULT 0,
  `authors` varchar(255) DEFAULT NULL,
  `access` tinyint(2) DEFAULT 0,
  `group_cn` varchar(255) DEFAULT NULL,
  `state` tinyint(2) DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `version_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_group_cn` (`group_cn`),
  KEY `idx_state` (`state`),
  FULLTEXT KEY `ftidx_title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wiki_page_author`
--

CREATE TABLE `#__wiki_page_author` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT 0,
  `page_id` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_page_id` (`page_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wiki_page_links`
--

CREATE TABLE `#__wiki_page_links` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL DEFAULT 0,
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `scope` varchar(50) NOT NULL DEFAULT '',
  `scope_id` int(11) NOT NULL DEFAULT 0,
  `link` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_page_id` (`page_id`),
  KEY `idx_scope_scope_id` (`scope`,`scope_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wiki_page_metrics`
--

CREATE TABLE `#__wiki_page_metrics` (
  `pageid` int(11) NOT NULL DEFAULT 0,
  `pagename` varchar(100) DEFAULT NULL,
  `hits` int(11) NOT NULL DEFAULT 0,
  `visitors` int(11) NOT NULL DEFAULT 0,
  `visits` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`pageid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wiki_version`
--

CREATE TABLE `#__wiki_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageid` int(11) NOT NULL DEFAULT 0,
  `version` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `minor_edit` int(1) NOT NULL DEFAULT 0,
  `pagetext` text DEFAULT NULL,
  `pagehtml` text DEFAULT NULL,
  `approved` int(1) NOT NULL DEFAULT 0,
  `summary` varchar(255) DEFAULT NULL,
  `length` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_pageid` (`pageid`),
  KEY `idx_approved` (`approved`),
  FULLTEXT KEY `ftidx_pagetext` (`pagetext`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wish_attachments`
--

CREATE TABLE `#__wish_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wish` int(11) NOT NULL DEFAULT 0,
  `filename` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wish` (`wish`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wishlist`
--

CREATE TABLE `#__wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `referenceid` int(11) NOT NULL DEFAULT 0,
  `title` varchar(150) NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` int(3) NOT NULL DEFAULT 0,
  `public` int(3) NOT NULL DEFAULT 1,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_category_referenceid` (`category`,`referenceid`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wishlist_implementation`
--

CREATE TABLE `#__wishlist_implementation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wishid` int(11) NOT NULL DEFAULT 0,
  `version` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `minor_edit` int(1) NOT NULL DEFAULT 0,
  `pagetext` text DEFAULT NULL,
  `pagehtml` text DEFAULT NULL,
  `approved` int(1) NOT NULL DEFAULT 0,
  `summary` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wishid` (`wishid`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_approved` (`approved`),
  FULLTEXT KEY `ftidx_pagetext` (`pagetext`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wishlist_item`
--

CREATE TABLE `#__wishlist_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wishlist` int(11) DEFAULT 0,
  `subject` varchar(200) NOT NULL,
  `about` text DEFAULT NULL,
  `proposed_by` int(11) DEFAULT 0,
  `granted_by` int(11) DEFAULT 0,
  `assigned` int(11) DEFAULT 0,
  `granted_vid` int(11) DEFAULT 0,
  `proposed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `granted` datetime DEFAULT '0000-00-00 00:00:00',
  `status` int(3) NOT NULL DEFAULT 0,
  `due` datetime DEFAULT '0000-00-00 00:00:00',
  `anonymous` int(3) DEFAULT 0,
  `ranking` int(11) DEFAULT 0,
  `points` int(11) DEFAULT 0,
  `private` int(3) DEFAULT 0,
  `accepted` int(3) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_wishlist` (`wishlist`),
  FULLTEXT KEY `ftidx_subject_about` (`subject`,`about`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wishlist_ownergroups`
--

CREATE TABLE `#__wishlist_ownergroups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wishlist` int(11) unsigned NOT NULL DEFAULT 0,
  `groupid` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_wishlist` (`wishlist`),
  KEY `idx_groupid` (`groupid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wishlist_owners`
--

CREATE TABLE `#__wishlist_owners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wishlist` int(11) unsigned NOT NULL DEFAULT 0,
  `userid` int(11) unsigned NOT NULL DEFAULT 0,
  `type` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_wishlist` (`wishlist`),
  KEY `idx_userid` (`userid`),
  KEY `idx_type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__wishlist_vote`
--

CREATE TABLE `#__wishlist_vote` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wishid` int(11) unsigned NOT NULL DEFAULT 0,
  `userid` int(11) unsigned NOT NULL DEFAULT 0,
  `voted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `importance` int(3) unsigned NOT NULL DEFAULT 0,
  `effort` int(3) NOT NULL DEFAULT 0,
  `due` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_wishid` (`wishid`),
  KEY `idx_userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xdomain_users`
--

CREATE TABLE `#__xdomain_users` (
  `domain_id` int(11) NOT NULL,
  `domain_username` varchar(150) NOT NULL DEFAULT '',
  `uidNumber` int(11) DEFAULT NULL,
  PRIMARY KEY (`domain_id`,`domain_username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xdomains`
--

CREATE TABLE `#__xdomains` (
  `domain_id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`domain_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups`
--

CREATE TABLE `#__xgroups` (
  `gidNumber` int(11) NOT NULL AUTO_INCREMENT,
  `cn` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `published` tinyint(3) DEFAULT 0,
  `approved` tinyint(3) DEFAULT 1,
  `type` tinyint(3) DEFAULT 0,
  `public_desc` text DEFAULT NULL,
  `private_desc` text DEFAULT NULL,
  `restrict_msg` text DEFAULT NULL,
  `join_policy` tinyint(3) DEFAULT 0,
  `discoverability` tinyint(3) DEFAULT NULL,
  `discussion_email_autosubscribe` tinyint(3) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `plugins` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `params` text DEFAULT NULL,
  PRIMARY KEY (`gidNumber`),
  UNIQUE KEY `idx_cn` (`cn`),
  FULLTEXT KEY `ftidx_cn_description_public_desc` (`cn`,`description`,`public_desc`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_applicants`
--

CREATE TABLE `#__xgroups_applicants` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_inviteemails`
--

CREATE TABLE `#__xgroups_inviteemails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `gidNumber` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_invitees`
--

CREATE TABLE `#__xgroups_invitees` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_log`
--

CREATE TABLE `#__xgroups_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `userid` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `actorid` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_action_timestamp` (`action`,`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_managers`
--

CREATE TABLE `#__xgroups_managers` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_member_roles`
--

CREATE TABLE `#__xgroups_member_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `roleid` int(11) DEFAULT NULL,
  `uidNumber` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_memberoption`
--

CREATE TABLE `#__xgroups_memberoption` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `optionname` varchar(100) DEFAULT NULL,
  `optionvalue` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_members`
--

CREATE TABLE `#__xgroups_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  `expires` datetime DEFAULT NULL,
  `expires_set_by` int(11) DEFAULT NULL,
  `expires_notified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `idx_gidNumber_uidNumber` (`gidNumber`,`uidNumber`),
  KEY `idx_expires` (`expires`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_member_history`
--

CREATE TABLE `#__xgroups_member_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  `expires` datetime DEFAULT NULL,
  `revoked` datetime DEFAULT NULL,
  `reason` varchar(32) DEFAULT NULL,
  `was_manager` tinyint(1) DEFAULT 0,
  `actor` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_gidNumber_uidNumber` (`gidNumber`,`uidNumber`),
  KEY `idx_revoked` (`revoked`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_modules`
--

CREATE TABLE `#__xgroups_modules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT '',
  `content` text DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `state` int(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `approved` int(11) DEFAULT 1,
  `approved_on` datetime DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `checked_errors` int(11) DEFAULT 0,
  `scanned` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_modules_menu`
--

CREATE TABLE `#__xgroups_modules_menu` (
  `moduleid` int(11) DEFAULT NULL,
  `pageid` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_pages`
--

CREATE TABLE `#__xgroups_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `parent` int(11) DEFAULT 0,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `depth` int(11) DEFAULT 1,
  `category` int(11) DEFAULT NULL,
  `template` varchar(100) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `state` int(11) DEFAULT 1,
  `privacy` varchar(10) DEFAULT NULL,
  `home` int(11) DEFAULT 0,
  `comments` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_pages_categories`
--

CREATE TABLE `#__xgroups_pages_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `color` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_pages_checkout`
--

CREATE TABLE `#__xgroups_pages_checkout` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageid` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `when` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_pages_hits`
--

CREATE TABLE `#__xgroups_pages_hits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `pageid` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_pages_versions`
--

CREATE TABLE `#__xgroups_pages_versions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageid` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `approved` int(11) DEFAULT 1,
  `approved_on` datetime DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `checked_errors` int(11) DEFAULT 0,
  `scanned` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_reasons`
--

CREATE TABLE `#__xgroups_reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uidNumber` int(11) NOT NULL,
  `gidNumber` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_roles`
--

CREATE TABLE `#__xgroups_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gidNumber` int(11) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `permissions` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xgroups_tracperm`
--

CREATE TABLE `#__xgroups_tracperm` (
  `group_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  UNIQUE KEY `id` (`group_id`,`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xmessage`
--

CREATE TABLE `#__xmessage` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT 0,
  `message` mediumtext DEFAULT NULL,
  `subject` varchar(250) DEFAULT NULL,
  `component` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `group_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_component` (`component`),
  KEY `idx_group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xmessage_action`
--

CREATE TABLE `#__xmessage_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(20) NOT NULL DEFAULT '',
  `element` int(11) unsigned NOT NULL DEFAULT 0,
  `description` mediumtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_class` (`class`),
  KEY `idx_element` (`element`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xmessage_component`
--

CREATE TABLE `#__xmessage_component` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component` varchar(50) NOT NULL DEFAULT '',
  `action` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_component` (`component`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xmessage_notify`
--

CREATE TABLE `#__xmessage_notify` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT 0,
  `method` varchar(250) DEFAULT NULL,
  `type` varchar(250) DEFAULT NULL,
  `priority` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_method` (`method`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xmessage_recipient`
--

CREATE TABLE `#__xmessage_recipient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) DEFAULT 0,
  `uid` int(11) DEFAULT 0,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `expires` datetime DEFAULT '0000-00-00 00:00:00',
  `actionid` int(11) DEFAULT 0,
  `state` tinyint(2) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_mid` (`mid`),
  KEY `idx_uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xmessage_seen`
--

CREATE TABLE `#__xmessage_seen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(11) unsigned NOT NULL DEFAULT 0,
  `uid` int(11) unsigned NOT NULL DEFAULT 0,
  `whenseen` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_mid` (`mid`),
  KEY `idx_uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xorganization_types`
--

CREATE TABLE `#__xorganization_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(150) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xorganizations`
--

CREATE TABLE `#__xorganizations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xprofiles`
--

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
  `mailPreferenceOption` int(11) NOT NULL DEFAULT -1,
  `usageAgreement` int(11) NOT NULL DEFAULT 0,
  `jobsAllowed` int(11) NOT NULL DEFAULT 0,
  `modifiedDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `emailConfirmed` int(11) NOT NULL DEFAULT 0,
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
  `vip` int(11) NOT NULL DEFAULT 0,
  `public` tinyint(2) NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  `note` text NOT NULL,
  `shadowExpire` int(11) DEFAULT NULL,
  `locked` tinyint(4) NOT NULL DEFAULT 0,
  `orcid` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`),
  KEY `idx_username` (`username`),
  FULLTEXT KEY `ftidx_givenName_surname` (`givenName`,`surname`),
  FULLTEXT KEY `ftidx_name` (`name`),
  FULLTEXT KEY `ftidx_fullname` (`givenName`,`middleName`,`surname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xprofiles_address`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xprofiles_admin`
--

CREATE TABLE `#__xprofiles_admin` (
  `uidNumber` int(11) NOT NULL,
  `admin` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`admin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xprofiles_bio`
--

CREATE TABLE `#__xprofiles_bio` (
  `uidNumber` int(11) NOT NULL,
  `bio` text DEFAULT NULL,
  PRIMARY KEY (`uidNumber`),
  FULLTEXT KEY `ftidx_bio` (`bio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xprofiles_dashboard_preferences`
--

CREATE TABLE `#__xprofiles_dashboard_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uidNumber` int(11) unsigned NOT NULL,
  `preferences` text DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidNumber` (`uidNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xprofiles_disability`
--

CREATE TABLE `#__xprofiles_disability` (
  `uidNumber` int(11) NOT NULL,
  `disability` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`disability`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xprofiles_edulevel`
--

CREATE TABLE `#__xprofiles_edulevel` (
  `uidNumber` int(11) NOT NULL,
  `edulevel` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`edulevel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xprofiles_hispanic`
--

CREATE TABLE `#__xprofiles_hispanic` (
  `uidNumber` int(11) NOT NULL,
  `hispanic` varchar(255) NOT NULL,
  PRIMARY KEY (`uidNumber`,`hispanic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xprofiles_host`
--

CREATE TABLE `#__xprofiles_host` (
  `uidNumber` int(11) NOT NULL,
  `host` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`host`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xprofiles_race`
--

CREATE TABLE `#__xprofiles_race` (
  `uidNumber` int(11) NOT NULL,
  `race` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`race`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xprofiles_role`
--

CREATE TABLE `#__xprofiles_role` (
  `uidNumber` int(11) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__xsession`
--

CREATE TABLE `#__xsession` (
  `session_id` varchar(200) NOT NULL DEFAULT '0',
  `ip` varchar(15) DEFAULT NULL,
  `host` varchar(128) DEFAULT NULL,
  `domain` varchar(128) DEFAULT NULL,
  `signed` tinyint(3) DEFAULT 0,
  `countrySHORT` char(2) DEFAULT NULL,
  `countryLONG` varchar(64) DEFAULT NULL,
  `ipREGION` varchar(128) DEFAULT NULL,
  `ipCITY` varchar(128) DEFAULT NULL,
  `ipLATITUDE` double DEFAULT NULL,
  `ipLONGITUDE` double DEFAULT NULL,
  `bot` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`session_id`),
  KEY `idx_ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__ysearch_plugin_weights`
--

CREATE TABLE `#__ysearch_plugin_weights` (
  `plugin` varchar(20) NOT NULL,
  `weight` float NOT NULL,
  PRIMARY KEY (`plugin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `#__ysearch_site_map`
--

CREATE TABLE `#__ysearch_site_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `link` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `ftidx_title_description` (`title`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `app`
--

CREATE TABLE `app` (
  `appname` varchar(80) NOT NULL DEFAULT '',
  `geometry` varchar(9) NOT NULL DEFAULT '',
  `depth` smallint(5) unsigned NOT NULL DEFAULT 16,
  `hostreq` bigint(20) unsigned NOT NULL DEFAULT 0,
  `userreq` bigint(20) unsigned NOT NULL DEFAULT 0,
  `timeout` int(10) unsigned NOT NULL DEFAULT 0,
  `command` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `display`
--

CREATE TABLE `display` (
  `hostname` varchar(40) NOT NULL DEFAULT '',
  `dispnum` int(10) unsigned DEFAULT 0,
  `geometry` varchar(9) NOT NULL DEFAULT '',
  `depth` smallint(5) unsigned NOT NULL DEFAULT 16,
  `sessnum` bigint(20) unsigned DEFAULT 0,
  `vncpass` varchar(16) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT '',
  KEY `idx_hostname` (`hostname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `domainclass`
--

CREATE TABLE `domainclass` (
  `class` tinyint(4) NOT NULL DEFAULT 0,
  `country` varchar(4) NOT NULL,
  `domain` varchar(64) NOT NULL,
  `name` tinytext NOT NULL,
  `state` varchar(4) NOT NULL,
  PRIMARY KEY (`domain`),
  KEY `idx_class` (`class`) USING BTREE,
  KEY `idx_domain_class` (`domain`,`class`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `domainclasses`
--

CREATE TABLE `domainclasses` (
  `class` tinyint(4) NOT NULL DEFAULT 0,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `fileperm`
--

CREATE TABLE `fileperm` (
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT 0,
  `fileuser` varchar(32) NOT NULL DEFAULT '',
  `fwhost` varchar(40) NOT NULL DEFAULT '',
  `fwport` smallint(5) unsigned NOT NULL DEFAULT 0,
  `cookie` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`sessnum`,`fileuser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `host`
--

CREATE TABLE `host` (
  `hostname` varchar(40) NOT NULL DEFAULT '',
  `provisions` bigint(20) unsigned NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT '',
  `uses` int(11) NOT NULL DEFAULT 0,
  `portbase` int(11) NOT NULL DEFAULT 0,
  `zone_id` int(11) DEFAULT NULL,
  `max_uses` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`hostname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `hosttype`
--

CREATE TABLE `hosttype` (
  `name` varchar(40) NOT NULL DEFAULT '',
  `value` bigint(20) unsigned NOT NULL DEFAULT 0,
  `description` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `job`
--

CREATE TABLE `job` (
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT 0,
  `jobid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `superjob` bigint(20) unsigned NOT NULL DEFAULT 0,
  `username` varchar(32) NOT NULL DEFAULT '',
  `event` varchar(40) NOT NULL DEFAULT '',
  `ncpus` smallint(5) unsigned NOT NULL DEFAULT 0,
  `venue` varchar(80) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `heartbeat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` smallint(2) NOT NULL DEFAULT 1,
  `jobtoken` varchar(32) DEFAULT NULL,
  UNIQUE KEY `uidx_jobid` (`jobid`),
  KEY `idx_start` (`start`),
  KEY `idx_heartbeat` (`heartbeat`),
  KEY `idx_username_jobtoken` (`username`,`jobtoken`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `joblog`
--

CREATE TABLE `joblog` (
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT 0,
  `job` int(10) unsigned NOT NULL DEFAULT 0,
  `superjob` bigint(20) unsigned NOT NULL DEFAULT 0,
  `event` varchar(40) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `walltime` double unsigned DEFAULT 0,
  `cputime` double unsigned DEFAULT 0,
  `ncpus` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` smallint(5) unsigned DEFAULT 0,
  `venue` varchar(80) NOT NULL DEFAULT '',
  `zone_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`sessnum`,`job`,`event`,`venue`),
  KEY `idx_sessnum` (`sessnum`),
  KEY `idx_event` (`event`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `sessnum` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '',
  `remoteip` varchar(40) NOT NULL DEFAULT '',
  `exechost` varchar(40) NOT NULL DEFAULT '',
  `dispnum` int(10) unsigned DEFAULT 0,
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `accesstime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeout` int(11) DEFAULT 86400,
  `appname` varchar(80) NOT NULL DEFAULT '',
  `sessname` varchar(100) NOT NULL DEFAULT '',
  `sesstoken` varchar(32) NOT NULL DEFAULT '',
  `params` text DEFAULT NULL,
  `zone_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`sessnum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `sessionlog`
--

CREATE TABLE `sessionlog` (
  `sessnum` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '',
  `remoteip` varchar(40) NOT NULL DEFAULT '',
  `remotehost` varchar(40) NOT NULL DEFAULT '',
  `exechost` varchar(40) NOT NULL DEFAULT '',
  `dispnum` int(10) unsigned DEFAULT 0,
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `appname` varchar(80) NOT NULL DEFAULT '',
  `walltime` double unsigned DEFAULT 0,
  `viewtime` double unsigned DEFAULT 0,
  `cputime` double unsigned DEFAULT 0,
  `status` smallint(5) unsigned DEFAULT 0,
  `zone_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`sessnum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `sessionpriv`
--

CREATE TABLE `sessionpriv` (
  `privid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT 0,
  `privilege` varchar(40) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`privid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `venue`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `view`
--

CREATE TABLE `view` (
  `viewid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT 0,
  `username` varchar(32) NOT NULL DEFAULT '',
  `remoteip` varchar(40) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `heartbeat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`viewid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `viewlog`
--

CREATE TABLE `viewlog` (
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT 0,
  `username` varchar(32) NOT NULL DEFAULT '',
  `remoteip` varchar(40) NOT NULL DEFAULT '',
  `remotehost` varchar(40) NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `duration` float unsigned DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `viewperm`
--

CREATE TABLE `viewperm` (
  `sessnum` bigint(20) unsigned NOT NULL DEFAULT 0,
  `viewuser` varchar(32) NOT NULL DEFAULT '',
  `viewtoken` varchar(32) NOT NULL DEFAULT '',
  `geometry` varchar(9) NOT NULL DEFAULT '0',
  `fwhost` varchar(40) NOT NULL DEFAULT '',
  `fwport` smallint(5) unsigned NOT NULL DEFAULT 0,
  `vncpass` varchar(16) NOT NULL DEFAULT '',
  `readonly` varchar(4) NOT NULL DEFAULT 'Yes',
  PRIMARY KEY (`sessnum`,`viewuser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `zone_locations`
--

CREATE TABLE `zone_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_id` int(11) NOT NULL,
  `ipFROM` int(10) unsigned zerofill NOT NULL DEFAULT 0000000000,
  `ipTO` int(10) unsigned zerofill NOT NULL DEFAULT 0000000000,
  `continent` char(2) NOT NULL,
  `countrySHORT` char(2) NOT NULL,
  `countryLONG` varchar(64) NOT NULL,
  `ipREGION` varchar(128) NOT NULL,
  `ipCITY` varchar(128) NOT NULL,
  `ipLATITUDE` double DEFAULT NULL,
  `ipLONGITUDE` double DEFAULT NULL,
  `notes` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Table structure for table `zones`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Temporary table structure for view `#__contributor_ids_view`
--

CREATE VIEW `#__contributor_ids_view` AS SELECT
  1 AS `uidNumber`;

--
-- Temporary table structure for view `#__contributors_view`
--

CREATE VIEW `#__contributors_view` AS SELECT
  1 AS `uidNumber`,
  1 AS `resource_count`,
  1 AS `wiki_count`,
  1 AS `total_count`;

--
-- Temporary table structure for view `#__courses_form_latest_responses_view`
--

CREATE VIEW `#__courses_form_latest_responses_view` AS SELECT
  1 AS `id`,
  1 AS `respondent_id`,
  1 AS `question_id`,
  1 AS `answer_id`;

--
-- Temporary table structure for view `#__resource_contributors_view`
--

CREATE VIEW `#__resource_contributors_view` AS SELECT
  1 AS `uidNumber`,
  1 AS `count`;

--
-- Temporary table structure for view `#__wiki_contributors_view`
--

CREATE VIEW `#__wiki_contributors_view` AS SELECT
  1 AS `uidNumber`,
  1 AS `count`;

--
-- Final view structure for view `#__contributor_ids_view`
--

DROP VIEW IF EXISTS `#__contributor_ids_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__contributor_ids_view`
AS
  SELECT `#__resource_contributors_view`.`uidnumber` AS `uidnumber`
  FROM   `#__resource_contributors_view`
  UNION
  SELECT `#__wiki_contributors_view`.`uidnumber` AS `uidnumber`
  FROM   `#__wiki_contributors_view`;

--
-- Final view structure for view `#__contributors_view`
--

DROP VIEW IF EXISTS `#__contributors_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__contributors_view`
AS
  SELECT    `c`.`uidnumber`                                   AS `uidnumber`,
            coalesce(`r`.`count`,0)                           AS `resource_count`,
            coalesce(`w`.`count`,0)                           AS `wiki_count`,
            coalesce(`w`.`count`,0) + coalesce(`r`.`count`,0) AS `total_count`
  FROM      ((`#__contributor_ids_view` `c`
  LEFT JOIN `#__resource_contributors_view` `r`
  ON       (
                      `r`.`uidnumber` = `c`.`uidnumber`))
  LEFT JOIN `#__wiki_contributors_view` `w`
  ON       (
                      `w`.`uidnumber` = `c`.`uidnumber`));

--
-- Final view structure for view `#__courses_form_latest_responses_view`
--

DROP VIEW IF EXISTS `#__courses_form_latest_responses_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__courses_form_latest_responses_view`
AS
  SELECT `fre`.`id`            AS `id`,
         `fre`.`respondent_id` AS `respondent_id`,
         `fre`.`question_id`   AS `question_id`,
         `fre`.`answer_id`     AS `answer_id`
  FROM   `#__courses_form_responses` `fre`
  WHERE  (
                SELECT count(0)
                FROM   `#__courses_form_responses` `frei`
                WHERE  `frei`.`respondent_id` = `fre`.`respondent_id`
                AND    `frei`.`id` > `fre`.`id`) <
         (
                SELECT count(DISTINCT `frei`.`question_id`)
                FROM   `#__courses_form_responses` `frei`
                WHERE  `frei`.`respondent_id` = `fre`.`respondent_id`);

--
-- Final view structure for view `#__resource_contributors_view`
--

DROP VIEW IF EXISTS `#__resource_contributors_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__resource_contributors_view`
AS
  SELECT    `m`.`uidnumber`        AS `uidnumber`,
            count(`aa`.`authorid`) AS `count`
  FROM      ((`#__xprofiles` `m`
  LEFT JOIN `#__author_assoc` `aa`
  ON       (
                      `aa`.`authorid` = `m`.`uidnumber`
            AND       `aa`.`subtable` = 'resources'))
  JOIN      `#__resources` `r`
  ON       (
                      `r`.`id` = `aa`.`subid`
            AND       `r`.`published` = 1
            AND       `r`.`standalone` = 1))
  WHERE     `m`.`public` = 1
  GROUP BY  `m`.`uidnumber`;

--
-- Final view structure for view `#__wiki_contributors_view`
--

DROP VIEW IF EXISTS `#__wiki_contributors_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `#__wiki_contributors_view`
AS
  SELECT    `m`.`uidnumber` AS `uidnumber`,
            count(`w`.`id`) AS `count`
  FROM      (`#__xprofiles` `m`
  LEFT JOIN `#__wiki_page` `w`
  ON       (
                      `w`.`access` <> 1
            AND       (
                                `w`.`created_by` = `m`.`uidnumber`
                      OR        `m`.`username` <> ''
                      AND       `w`.`authors` LIKE concat('%',`m`.`username`,'%'))))
  WHERE     `m`.`public` = 1
  AND       `w`.`id` IS NOT NULL
  GROUP BY  `m`.`uidnumber`;

