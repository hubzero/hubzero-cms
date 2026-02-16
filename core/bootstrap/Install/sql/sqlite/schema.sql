--
-- SQLite Schema for HubZero CMS
-- Auto-generated from mysql/schema.sql
-- 
-- This schema is compatible with SQLite 3.35.0+
--

PRAGMA foreign_keys = ON;

--
-- Table: `#__abuse_reports`
--

CREATE TABLE `#__abuse_reports` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `category` varchar(50) DEFAULT NULL,
  `referenceid` INTEGER NOT NULL DEFAULT 0,
  `report` text NOT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `state` INTEGER NOT NULL DEFAULT 0,
  `subject` varchar(150) DEFAULT NULL,
  `reviewed` datetime NOT NULL DEFAULT NULL,
  `reviewed_by` INTEGER NOT NULL DEFAULT 0,
  `note` text NOT NULL
);

CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__abuse_reports` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_reviewed_by` ON `#__abuse_reports` (`reviewed_by`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__abuse_reports` (`state`);
CREATE INDEX IF NOT EXISTS `idx_category_referenceid` ON `#__abuse_reports` (`category`,`referenceid`);

--
-- Table: `#__announcements`
--

CREATE TABLE `#__announcements` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `scope` varchar(100) DEFAULT NULL,
  `scope_id` INTEGER DEFAULT NULL,
  `content` text DEFAULT NULL,
  `priority` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `publish_up` datetime NOT NULL DEFAULT NULL,
  `publish_down` datetime NOT NULL DEFAULT NULL,
  `sticky` INTEGER NOT NULL DEFAULT 0,
  `email` INTEGER DEFAULT 0,
  `sent` INTEGER DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_scope_scope_id` ON `#__announcements` (`scope`,`scope_id`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__announcements` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__announcements` (`state`);
CREATE INDEX IF NOT EXISTS `idx_priority` ON `#__announcements` (`priority`);
CREATE INDEX IF NOT EXISTS `idx_sticky` ON `#__announcements` (`sticky`);

--
-- Table: `#__answers_log`
--

CREATE TABLE `#__answers_log` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `response_id` INTEGER NOT NULL DEFAULT 0,
  `ip` varchar(15) NOT NULL DEFAULT '',
  `helpful` varchar(10) NOT NULL DEFAULT ''
);

CREATE INDEX IF NOT EXISTS `idx_rid` ON `#__answers_log` (`response_id`);

--
-- Table: `#__answers_questions`
--

CREATE TABLE `#__answers_questions` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `subject` varchar(250) NOT NULL DEFAULT '',
  `question` text NOT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `anonymous` INTEGER NOT NULL DEFAULT 0,
  `email` INTEGER NOT NULL DEFAULT 0,
  `helpful` INTEGER NOT NULL DEFAULT 0,
  `reward` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_state` ON `#__answers_questions` (`state`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__answers_questions` (`created_by`);

--
-- Table: `#__answers_questions_log`
--

CREATE TABLE `#__answers_questions_log` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `question_id` INTEGER NOT NULL DEFAULT 0,
  `expires` datetime NOT NULL DEFAULT NULL,
  `voter` INTEGER NOT NULL DEFAULT 0,
  `ip` varchar(15) NOT NULL DEFAULT ''
);

CREATE INDEX IF NOT EXISTS `idx_qid` ON `#__answers_questions_log` (`question_id`);
CREATE INDEX IF NOT EXISTS `idx_voter` ON `#__answers_questions_log` (`voter`);

--
-- Table: `#__answers_responses`
--

CREATE TABLE `#__answers_responses` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `question_id` INTEGER NOT NULL DEFAULT 0,
  `answer` text NOT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `helpful` INTEGER NOT NULL DEFAULT 0,
  `nothelpful` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `anonymous` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_qid` ON `#__answers_responses` (`question_id`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__answers_responses` (`state`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__answers_responses` (`created_by`);

--
-- Table: `#__assets`
--

CREATE TABLE `#__assets` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `parent_id` INTEGER NOT NULL DEFAULT 0,
  `lft` INTEGER NOT NULL DEFAULT 0,
  `rgt` INTEGER NOT NULL DEFAULT 0,
  `level` INTEGER NOT NULL,
  `name` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `rules` varchar(5120) NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_asset_name` ON `#__assets` (`name`);
CREATE INDEX IF NOT EXISTS `idx_lft_rgt` ON `#__assets` (`lft`,`rgt`);
CREATE INDEX IF NOT EXISTS `idx_parent_id` ON `#__assets` (`parent_id`);

--
-- Table: `#__associations`
--

CREATE TABLE `#__associations` (
  `id` varchar(50) NOT NULL,
  `context` varchar(50) NOT NULL,
  `key` char(32) NOT NULL,
  PRIMARY KEY (`context`,`id`)
);

CREATE INDEX IF NOT EXISTS `idx_key` ON `#__associations` (`key`);

--
-- Table: `#__auth_domain`
--

CREATE TABLE `#__auth_domain` (
  `authenticator` varchar(255) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `params` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL
);

--
-- Table: `#__auth_link`
--

CREATE TABLE `#__auth_link` (
  `auth_domain_id` INTEGER DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `params` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_id` INTEGER DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `linked_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

--
-- Table: `#__author_assoc`
--

CREATE TABLE `#__author_assoc` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `subtable` varchar(50) NOT NULL DEFAULT '',
  `subid` INTEGER NOT NULL DEFAULT 0,
  `authorid` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_subtable_subid_authorid` ON `#__author_assoc` (`subtable`,`subid`,`authorid`);
CREATE UNIQUE INDEX IF NOT EXISTS `id` ON `#__author_assoc` (`id`);

--
-- Table: `#__author_role_types`
--

CREATE TABLE `#__author_role_types` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `role_id` INTEGER NOT NULL DEFAULT 0,
  `type_id` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__author_roles`
--

CREATE TABLE `#__author_roles` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `state` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__author_stats`
--

CREATE TABLE `#__author_stats` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `authorid` INTEGER NOT NULL,
  `tool_users` INTEGER DEFAULT NULL,
  `andmore_users` INTEGER DEFAULT NULL,
  `total_users` INTEGER DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT NULL,
  `period` INTEGER NOT NULL DEFAULT -1
);

--
-- Table: `#__billboard_collection`
--

CREATE TABLE `#__billboard_collection` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(255) DEFAULT NULL
);

--
-- Table: `#__billboards`
--

CREATE TABLE `#__billboards` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `collection_id` INTEGER DEFAULT NULL,
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
  `published` INTEGER DEFAULT 0,
  `ordering` INTEGER DEFAULT NULL,
  `checked_out` INTEGER DEFAULT 0,
  `checked_out_time` datetime DEFAULT NULL
);

--
-- Table: `#__blog_comments`
--

CREATE TABLE `#__blog_comments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `entry_id` INTEGER NOT NULL DEFAULT 0,
  `content` text NOT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `anonymous` INTEGER NOT NULL DEFAULT 0,
  `parent` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_entry_id` ON `#__blog_comments` (`entry_id`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__blog_comments` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_parent` ON `#__blog_comments` (`parent`);

--
-- Table: `#__blog_entries`
--

CREATE TABLE `#__blog_entries` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `publish_up` datetime NOT NULL DEFAULT NULL,
  `publish_down` datetime NOT NULL DEFAULT NULL,
  `params` tinytext NOT NULL,
  `scope_id` INTEGER NOT NULL DEFAULT 0,
  `hits` INTEGER NOT NULL DEFAULT 0,
  `allow_comments` INTEGER NOT NULL DEFAULT 0,
  `scope` varchar(100) NOT NULL DEFAULT '',
  `access` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__blog_entries` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_alias` ON `#__blog_entries` (`alias`);
CREATE INDEX IF NOT EXISTS `idx_scope_id` ON `#__blog_entries` (`scope_id`);

--
-- Table: `#__cart`
--

CREATE TABLE `#__cart` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `itemid` INTEGER NOT NULL DEFAULT 0,
  `type` varchar(20) DEFAULT NULL,
  `quantity` INTEGER NOT NULL DEFAULT 0,
  `added` datetime NOT NULL DEFAULT NULL,
  `selections` text DEFAULT NULL
);

--
-- Table: `#__cart_cart_items`
--

CREATE TABLE `#__cart_cart_items` (
  `crtId` INTEGER NOT NULL,
  `sId` INTEGER NOT NULL,
  `crtiQty` INTEGER DEFAULT NULL,
  `crtiOldQty` INTEGER DEFAULT NULL,
  `crtiPrice` decimal(10,2) DEFAULT NULL,
  `crtiOldPrice` decimal(10,2) DEFAULT NULL,
  `crtiName` varchar(255) DEFAULT NULL,
  `crtiAvailable` INTEGER DEFAULT 1,
  PRIMARY KEY (`crtId`,`sId`)
);

--
-- Table: `#__cart_carts`
--

CREATE TABLE `#__cart_carts` (
  `crtId` INTEGER PRIMARY KEY AUTOINCREMENT,
  `crtCreated` datetime DEFAULT NULL,
  `crtLastUpdated` datetime DEFAULT NULL,
  `uidNumber` INTEGER DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_uidNumber` ON `#__cart_carts` (`uidNumber`);

--
-- Table: `#__cart_coupons`
--

CREATE TABLE `#__cart_coupons` (
  `crtId` INTEGER NOT NULL,
  `cnId` INTEGER NOT NULL,
  `crtCnAdded` datetime DEFAULT NULL,
  `crtCnStatus` char(15) DEFAULT NULL
);

--
-- Table: `#__cart_memberships`
--

CREATE TABLE `#__cart_memberships` (
  `crtmId` INTEGER PRIMARY KEY AUTOINCREMENT,
  `pId` INTEGER DEFAULT NULL,
  `crtId` INTEGER DEFAULT NULL,
  `crtmExpires` datetime DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_pId_crtId` ON `#__cart_memberships` (`pId`,`crtId`);

--
-- Table: `#__cart_saved_addresses`
--

CREATE TABLE `#__cart_saved_addresses` (
  `saId` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uidNumber` INTEGER NOT NULL,
  `saToFirst` char(100) NOT NULL,
  `saToLast` char(100) NOT NULL,
  `saAddress` char(255) NOT NULL,
  `saCity` char(25) NOT NULL,
  `saState` char(2) NOT NULL,
  `saZip` char(10) NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_uidNumber_saToFirst_saToLast_saAddress_saZip` ON `#__cart_saved_addresses` (`uidNumber`,`saToFirst`,`saToLast`,`saAddress`,`saZip`);

--
-- Table: `#__cart_transaction_info`
--

CREATE TABLE `#__cart_transaction_info` (
  `tId` INTEGER NOT NULL,
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
);

--
-- Table: `#__cart_transaction_items`
--

CREATE TABLE `#__cart_transaction_items` (
  `tId` INTEGER NOT NULL,
  `sId` INTEGER NOT NULL,
  `tiQty` INTEGER DEFAULT NULL,
  `tiPrice` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`tId`,`sId`)
);

--
-- Table: `#__cart_transaction_steps`
--

CREATE TABLE `#__cart_transaction_steps` (
  `tsId` INTEGER PRIMARY KEY AUTOINCREMENT,
  `tId` INTEGER NOT NULL,
  `tsStep` char(16) NOT NULL,
  `tsStatus` INTEGER DEFAULT 0
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_tId_tsStep` ON `#__cart_transaction_steps` (`tId`,`tsStep`);

--
-- Table: `#__cart_transactions`
--

CREATE TABLE `#__cart_transactions` (
  `tId` INTEGER PRIMARY KEY AUTOINCREMENT,
  `crtId` INTEGER DEFAULT NULL,
  `tCreated` datetime DEFAULT NULL,
  `tLastUpdated` datetime DEFAULT NULL,
  `tStatus` char(32) DEFAULT NULL
);

--
-- Table: `#__categories`
--

CREATE TABLE `#__categories` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `asset_id` INTEGER NOT NULL DEFAULT 0,
  `parent_id` INTEGER NOT NULL DEFAULT 0,
  `lft` INTEGER NOT NULL DEFAULT 0,
  `rgt` INTEGER NOT NULL DEFAULT 0,
  `level` INTEGER NOT NULL DEFAULT 0,
  `path` varchar(255) NOT NULL DEFAULT '',
  `extension` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `published` INTEGER NOT NULL DEFAULT 0,
  `checked_out` INTEGER NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT NULL,
  `access` INTEGER NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  `metadesc` varchar(1024) NOT NULL,
  `metakey` varchar(1024) NOT NULL,
  `metadata` varchar(2048) NOT NULL,
  `created_user_id` INTEGER NOT NULL DEFAULT 0,
  `created_time` datetime NOT NULL DEFAULT NULL,
  `modified_user_id` INTEGER NOT NULL DEFAULT 0,
  `modified_time` datetime NOT NULL DEFAULT NULL,
  `hits` INTEGER NOT NULL DEFAULT 0,
  `language` char(7) NOT NULL
);

CREATE INDEX IF NOT EXISTS `idx_extension_published_access` ON `#__categories` (`extension`,`published`,`access`);
CREATE INDEX IF NOT EXISTS `idx_access` ON `#__categories` (`access`);
CREATE INDEX IF NOT EXISTS `idx_checkout` ON `#__categories` (`checked_out`);
CREATE INDEX IF NOT EXISTS `idx_path` ON `#__categories` (`path`);
CREATE INDEX IF NOT EXISTS `idx_left_right` ON `#__categories` (`lft`,`rgt`);
CREATE INDEX IF NOT EXISTS `idx_alias` ON `#__categories` (`alias`);
CREATE INDEX IF NOT EXISTS `idx_language` ON `#__categories` (`language`);

--
-- Table: `#__citations`
--

CREATE TABLE `#__citations` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uid` INTEGER DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `published` INTEGER NOT NULL DEFAULT 1,
  `affiliated` INTEGER NOT NULL DEFAULT 0,
  `fundedby` INTEGER DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
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
  `date_submit` datetime NOT NULL DEFAULT NULL,
  `date_accept` datetime NOT NULL DEFAULT NULL,
  `date_publish` datetime NOT NULL DEFAULT NULL,
  `software_use` INTEGER DEFAULT NULL,
  `res_edu` INTEGER DEFAULT NULL,
  `exp_list_exp_data` INTEGER DEFAULT NULL,
  `exp_data` INTEGER DEFAULT NULL,
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
  `custom4` varchar(45) DEFAULT NULL
);

--
-- Table: `#__citations_assoc`
--

CREATE TABLE `#__citations_assoc` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `cid` INTEGER DEFAULT 0,
  `oid` INTEGER DEFAULT 0,
  `type` varchar(50) DEFAULT NULL,
  `tbl` varchar(50) DEFAULT NULL
);

--
-- Table: `#__citations_authors`
--

CREATE TABLE `#__citations_authors` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `cid` INTEGER DEFAULT 0,
  `author` varchar(64) DEFAULT NULL,
  `authorid` INTEGER DEFAULT 0,
  `uidNumber` INTEGER DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0,
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
  `in_network` INTEGER NOT NULL DEFAULT 0
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_cid_author_authorid_uidNumber` ON `#__citations_authors` (`cid`,`author`,`authorid`,`uidNumber`);
CREATE INDEX IF NOT EXISTS `idx_authorid` ON `#__citations_authors` (`authorid`);
CREATE INDEX IF NOT EXISTS `idx_uidNumber` ON `#__citations_authors` (`uidNumber`);

--
-- Table: `#__citations_format`
--

CREATE TABLE `#__citations_format` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `typeid` INTEGER DEFAULT NULL,
  `style` varchar(50) DEFAULT NULL,
  `format` text DEFAULT NULL
);

--
-- Table: `#__citations_secondary`
--

CREATE TABLE `#__citations_secondary` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `cid` INTEGER NOT NULL,
  `sec_cits_cnt` INTEGER DEFAULT NULL,
  `search_string` tinytext DEFAULT NULL,
  `scope` varchar(250) DEFAULT NULL,
  `scope_id` INTEGER DEFAULT NULL,
  `link1_url` tinytext DEFAULT NULL,
  `link1_title` varchar(60) DEFAULT NULL,
  `link2_url` tinytext DEFAULT NULL,
  `link2_title` varchar(60) DEFAULT NULL,
  `link3_url` tinytext DEFAULT NULL,
  `link3_title` varchar(60) DEFAULT NULL
);

--
-- Table: `#__citations_sponsors`
--

CREATE TABLE `#__citations_sponsors` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `sponsor` varchar(150) DEFAULT NULL,
  `link` varchar(200) DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL
);

--
-- Table: `#__citations_sponsors_assoc`
--

CREATE TABLE `#__citations_sponsors_assoc` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `cid` INTEGER DEFAULT NULL,
  `sid` INTEGER DEFAULT NULL
);

--
-- Table: `#__citations_types`
--

CREATE TABLE `#__citations_types` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `type_title` varchar(255) DEFAULT NULL,
  `type_desc` text DEFAULT NULL,
  `type_export` varchar(255) DEFAULT NULL,
  `fields` text DEFAULT NULL
);

--
-- Table: `#__collections`
--

CREATE TABLE `#__collections` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL,
  `object_id` INTEGER NOT NULL DEFAULT 0,
  `object_type` varchar(150) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 1,
  `access` INTEGER NOT NULL DEFAULT 0,
  `is_default` INTEGER NOT NULL DEFAULT 0,
  `description` mediumtext NOT NULL,
  `positive` INTEGER NOT NULL DEFAULT 0,
  `negative` INTEGER NOT NULL DEFAULT 0,
  `sort` varchar(50) NOT NULL DEFAULT 'created',
  `layout` varchar(50) NOT NULL DEFAULT 'grid'
);

CREATE INDEX IF NOT EXISTS `idx_object_type_object_id` ON `#__collections` (`object_type`,`object_id`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__collections` (`state`);
CREATE INDEX IF NOT EXISTS `idx_access` ON `#__collections` (`access`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__collections` (`created_by`);

--
-- Table: `#__collections_assets`
--

CREATE TABLE `#__collections_assets` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `item_id` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `state` INTEGER NOT NULL DEFAULT 0,
  `type` varchar(50) NOT NULL DEFAULT 'file',
  `ordering` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_item_id` ON `#__collections_assets` (`item_id`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__collections_assets` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__collections_assets` (`state`);

--
-- Table: `#__collections_following`
--

CREATE TABLE `#__collections_following` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `follower_type` varchar(150) NOT NULL,
  `follower_id` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `following_type` varchar(150) NOT NULL DEFAULT '',
  `following_id` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_follower_type_follower_id` ON `#__collections_following` (`follower_type`,`follower_id`);
CREATE INDEX IF NOT EXISTS `idx_following_type_following_id` ON `#__collections_following` (`following_type`,`following_id`);

--
-- Table: `#__collections_items`
--

CREATE TABLE `#__collections_items` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `url` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 1,
  `access` INTEGER NOT NULL DEFAULT 0,
  `positive` INTEGER NOT NULL DEFAULT 0,
  `negative` INTEGER NOT NULL DEFAULT 0,
  `type` varchar(150) NOT NULL DEFAULT '',
  `object_id` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_state` ON `#__collections_items` (`state`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__collections_items` (`created_by`);

--
-- Table: `#__collections_posts`
--

CREATE TABLE `#__collections_posts` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `collection_id` INTEGER NOT NULL DEFAULT 0,
  `item_id` INTEGER NOT NULL DEFAULT 0,
  `description` mediumtext NOT NULL,
  `original` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_collection_id` ON `#__collections_posts` (`collection_id`);
CREATE INDEX IF NOT EXISTS `idx_item_id` ON `#__collections_posts` (`item_id`);
CREATE INDEX IF NOT EXISTS `idx_original` ON `#__collections_posts` (`original`);

--
-- Table: `#__collections_votes`
--

CREATE TABLE `#__collections_votes` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `item_id` INTEGER NOT NULL DEFAULT 0,
  `voted` datetime NOT NULL DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_item_id_user_id` ON `#__collections_votes` (`item_id`,`user_id`);

--
-- Table: `#__content`
--

CREATE TABLE `#__content` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `asset_id` INTEGER NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `title_alias` varchar(255) NOT NULL DEFAULT '',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `state` INTEGER NOT NULL DEFAULT 0,
  `sectionid` INTEGER NOT NULL DEFAULT 0,
  `mask` INTEGER NOT NULL DEFAULT 0,
  `catid` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0,
  `checked_out` INTEGER NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT NULL,
  `publish_up` datetime NOT NULL DEFAULT NULL,
  `publish_down` datetime NOT NULL DEFAULT NULL,
  `images` text NOT NULL,
  `urls` text NOT NULL,
  `attribs` varchar(5120) NOT NULL,
  `version` INTEGER NOT NULL DEFAULT 1,
  `parentid` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `access` INTEGER NOT NULL DEFAULT 0,
  `hits` INTEGER NOT NULL DEFAULT 0,
  `metadata` text NOT NULL,
  `featured` INTEGER NOT NULL DEFAULT 0,
  `language` char(7) NOT NULL,
  `xreference` varchar(50) NOT NULL
);

CREATE INDEX IF NOT EXISTS `idx_access` ON `#__content` (`access`);
CREATE INDEX IF NOT EXISTS `idx_checkout` ON `#__content` (`checked_out`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__content` (`state`);
CREATE INDEX IF NOT EXISTS `idx_catid` ON `#__content` (`catid`);
CREATE INDEX IF NOT EXISTS `idx_createdby` ON `#__content` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_featured_catid` ON `#__content` (`featured`,`catid`);
CREATE INDEX IF NOT EXISTS `idx_language` ON `#__content` (`language`);
CREATE INDEX IF NOT EXISTS `idx_xreference` ON `#__content` (`xreference`);

--
-- Table: `#__content_frontpage`
--

CREATE TABLE `#__content_frontpage` (
  `content_id` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`content_id`)
);

--
-- Table: `#__content_rating`
--

CREATE TABLE `#__content_rating` (
  `content_id` INTEGER NOT NULL DEFAULT 0,
  `rating_sum` INTEGER NOT NULL DEFAULT 0,
  `rating_count` INTEGER NOT NULL DEFAULT 0,
  `lastip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`content_id`)
);

--
-- Table: `#__core_log_searches`
--

CREATE TABLE `#__core_log_searches` (
  `search_term` varchar(128) NOT NULL DEFAULT '',
  `hits` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__courses`
--

CREATE TABLE `#__courses` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `group_id` INTEGER NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `state` INTEGER NOT NULL DEFAULT 0,
  `type` INTEGER NOT NULL DEFAULT 0,
  `access` INTEGER NOT NULL DEFAULT 0,
  `blurb` text NOT NULL,
  `description` text NOT NULL,
  `logo` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  `length` varchar(255) DEFAULT NULL,
  `effort` varchar(255) DEFAULT NULL
);

--
-- Table: `#__courses_announcements`
--

CREATE TABLE `#__courses_announcements` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `offering_id` INTEGER NOT NULL DEFAULT 0,
  `content` text DEFAULT NULL,
  `priority` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `section_id` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `publish_up` datetime NOT NULL DEFAULT NULL,
  `publish_down` datetime NOT NULL DEFAULT NULL,
  `sticky` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_offering_id` ON `#__courses_announcements` (`offering_id`);
CREATE INDEX IF NOT EXISTS `idx_section_id` ON `#__courses_announcements` (`section_id`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__courses_announcements` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__courses_announcements` (`state`);
CREATE INDEX IF NOT EXISTS `idx_priority` ON `#__courses_announcements` (`priority`);

--
-- Table: `#__courses_asset_associations`
--

CREATE TABLE `#__courses_asset_associations` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `asset_id` INTEGER NOT NULL DEFAULT 0,
  `scope_id` INTEGER NOT NULL DEFAULT 0,
  `scope` varchar(255) NOT NULL DEFAULT 'asset_group',
  `ordering` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_asset_id` ON `#__courses_asset_associations` (`asset_id`);
CREATE INDEX IF NOT EXISTS `idx_scope_id` ON `#__courses_asset_associations` (`scope_id`);
CREATE INDEX IF NOT EXISTS `idx_scope` ON `#__courses_asset_associations` (`scope`);

--
-- Table: `#__courses_asset_group_types`
--

CREATE TABLE `#__courses_asset_group_types` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `alias` varchar(200) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT ''
);

--
-- Table: `#__courses_asset_groups`
--

CREATE TABLE `#__courses_asset_groups` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `unit_id` INTEGER NOT NULL DEFAULT 0,
  `alias` varchar(250) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `parent` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `params` text NOT NULL
);

CREATE INDEX IF NOT EXISTS `idx_unit_id` ON `#__courses_asset_groups` (`unit_id`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__courses_asset_groups` (`created_by`);

--
-- Table: `#__courses_asset_unity`
--

CREATE TABLE `#__courses_asset_unity` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `member_id` INTEGER NOT NULL,
  `asset_id` INTEGER NOT NULL,
  `created` datetime NOT NULL,
  `passed` INTEGER NOT NULL,
  `details` text DEFAULT NULL
);

--
-- Table: `#__courses_asset_views`
--

CREATE TABLE `#__courses_asset_views` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `asset_id` INTEGER NOT NULL,
  `course_id` INTEGER DEFAULT NULL,
  `viewed` datetime NOT NULL,
  `viewed_by` INTEGER NOT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `user_agent_string` varchar(255) DEFAULT NULL,
  `session_id` varchar(200) DEFAULT NULL
);

--
-- Table: `#__courses_assets`
--

CREATE TABLE `#__courses_assets` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` mediumtext DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `subtype` varchar(255) NOT NULL DEFAULT 'file',
  `url` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 1,
  `course_id` INTEGER NOT NULL DEFAULT 0,
  `graded` INTEGER DEFAULT NULL,
  `grade_weight` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT ''
);

CREATE INDEX IF NOT EXISTS `idx_course_id` ON `#__courses_assets` (`course_id`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__courses_assets` (`created_by`);

--
-- Table: `#__courses_certificates`
--

CREATE TABLE `#__courses_certificates` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `properties` text DEFAULT NULL,
  `course_id` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__courses_form_answers`
--

CREATE TABLE `#__courses_form_answers` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `correct` INTEGER NOT NULL,
  `left_dist` INTEGER NOT NULL,
  `top_dist` INTEGER NOT NULL,
  `question_id` INTEGER NOT NULL
);

CREATE INDEX IF NOT EXISTS `idx_question_id` ON `#__courses_form_answers` (`question_id`);

--
-- Table: `#__courses_form_deployments`
--

CREATE TABLE `#__courses_form_deployments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `form_id` INTEGER NOT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `results_open` varchar(50) DEFAULT NULL,
  `time_limit` INTEGER DEFAULT NULL,
  `crumb` varchar(20) NOT NULL,
  `results_closed` varchar(50) DEFAULT NULL,
  `user_id` INTEGER NOT NULL,
  `allowed_attempts` INTEGER NOT NULL DEFAULT 1
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_crumb` ON `#__courses_form_deployments` (`crumb`);

--
-- Table: `#__courses_form_questions`
--

CREATE TABLE `#__courses_form_questions` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `page` INTEGER NOT NULL,
  `version` INTEGER NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `left_dist` INTEGER NOT NULL,
  `top_dist` INTEGER NOT NULL,
  `height` INTEGER NOT NULL,
  `width` INTEGER NOT NULL,
  `form_id` INTEGER DEFAULT NULL
);

--
-- Table: `#__courses_form_respondent_progress`
--

CREATE TABLE `#__courses_form_respondent_progress` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `respondent_id` INTEGER NOT NULL,
  `question_id` INTEGER NOT NULL,
  `answer_id` INTEGER NOT NULL,
  `submitted` datetime DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_respondent_id_question_id` ON `#__courses_form_respondent_progress` (`respondent_id`,`question_id`);

--
-- Table: `#__courses_form_respondents`
--

CREATE TABLE `#__courses_form_respondents` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `deployment_id` INTEGER NOT NULL,
  `member_id` INTEGER NOT NULL,
  `started` timestamp NULL DEFAULT NULL,
  `finished` timestamp NULL DEFAULT NULL,
  `attempt` INTEGER NOT NULL DEFAULT 1,
  `attempts` INTEGER NOT NULL DEFAULT 1
);

CREATE INDEX IF NOT EXISTS `idx_member_id` ON `#__courses_form_respondents` (`member_id`);
CREATE INDEX IF NOT EXISTS `idx_deployment_id` ON `#__courses_form_respondents` (`deployment_id`);

--
-- Table: `#__courses_form_responses`
--

CREATE TABLE `#__courses_form_responses` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `respondent_id` INTEGER NOT NULL,
  `question_id` INTEGER NOT NULL,
  `answer_id` INTEGER NOT NULL
);

CREATE INDEX IF NOT EXISTS `idx_respondent_id` ON `#__courses_form_responses` (`respondent_id`);
CREATE INDEX IF NOT EXISTS `idx_question_id` ON `#__courses_form_responses` (`question_id`);
CREATE INDEX IF NOT EXISTS `idx_answer_id` ON `#__courses_form_responses` (`answer_id`);

--
-- Table: `#__courses_forms`
--

CREATE TABLE `#__courses_forms` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` text DEFAULT NULL,
  `active` INTEGER NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `asset_id` INTEGER DEFAULT NULL
);

--
-- Table: `#__courses_grade_book`
--

CREATE TABLE `#__courses_grade_book` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `member_id` INTEGER NOT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `scope` varchar(255) NOT NULL DEFAULT 'asset',
  `scope_id` INTEGER NOT NULL DEFAULT 0,
  `override` decimal(5,2) DEFAULT NULL,
  `score_recorded` datetime DEFAULT NULL,
  `override_recorded` datetime DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_user_id_scope_scope_id` ON `#__courses_grade_book` (`member_id`,`scope`,`scope_id`);

--
-- Table: `#__courses_grade_policies`
--

CREATE TABLE `#__courses_grade_policies` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `description` mediumtext DEFAULT NULL,
  `threshold` decimal(3,2) DEFAULT NULL,
  `exam_weight` decimal(3,2) DEFAULT NULL,
  `quiz_weight` decimal(3,2) DEFAULT NULL,
  `homework_weight` decimal(3,2) DEFAULT NULL
);

--
-- Table: `#__courses_log`
--

CREATE TABLE `#__courses_log` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `scope_id` INTEGER NOT NULL DEFAULT 0,
  `scope` varchar(100) NOT NULL DEFAULT '',
  `timestamp` datetime NOT NULL DEFAULT NULL,
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `action` varchar(50) NOT NULL DEFAULT '',
  `comments` text NOT NULL,
  `actor_id` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__courses_member_badges`
--

CREATE TABLE `#__courses_member_badges` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `member_id` INTEGER NOT NULL,
  `section_badge_id` INTEGER NOT NULL,
  `earned` INTEGER DEFAULT NULL,
  `earned_on` datetime DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `action_on` datetime DEFAULT NULL,
  `validation_token` varchar(20) DEFAULT NULL,
  `criteria_id` INTEGER DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_member_id` ON `#__courses_member_badges` (`member_id`);

--
-- Table: `#__courses_member_notes`
--

CREATE TABLE `#__courses_member_notes` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `scope` varchar(255) NOT NULL DEFAULT '',
  `scope_id` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `content` mediumtext NOT NULL,
  `pos_x` INTEGER NOT NULL DEFAULT 0,
  `pos_y` INTEGER NOT NULL DEFAULT 0,
  `width` INTEGER NOT NULL DEFAULT 0,
  `height` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `timestamp` time NOT NULL DEFAULT '00:00:00',
  `section_id` INTEGER NOT NULL DEFAULT 0,
  `access` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_scoped` ON `#__courses_member_notes` (`scope`,`scope_id`);
CREATE INDEX IF NOT EXISTS `idx_createdby` ON `#__courses_member_notes` (`created_by`);

--
-- Table: `#__courses_members`
--

CREATE TABLE `#__courses_members` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `course_id` INTEGER NOT NULL DEFAULT 0,
  `offering_id` INTEGER NOT NULL DEFAULT 0,
  `section_id` INTEGER NOT NULL DEFAULT 0,
  `role_id` INTEGER NOT NULL DEFAULT 0,
  `permissions` mediumtext NOT NULL,
  `enrolled` datetime NOT NULL DEFAULT NULL,
  `student` INTEGER NOT NULL DEFAULT 0,
  `first_visit` datetime NOT NULL DEFAULT NULL,
  `token` varchar(23) NOT NULL DEFAULT ''
);

CREATE INDEX IF NOT EXISTS `idx_offering_id` ON `#__courses_members` (`offering_id`);
CREATE INDEX IF NOT EXISTS `idx_user_id` ON `#__courses_members` (`user_id`);
CREATE INDEX IF NOT EXISTS `idx_role_id` ON `#__courses_members` (`role_id`);
CREATE INDEX IF NOT EXISTS `idx_section_id` ON `#__courses_members` (`section_id`);

--
-- Table: `#__courses_offering_section_badge_criteria`
--

CREATE TABLE `#__courses_offering_section_badge_criteria` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `text` text NOT NULL,
  `section_badge_id` INTEGER NOT NULL
);

--
-- Table: `#__courses_offering_section_badges`
--

CREATE TABLE `#__courses_offering_section_badges` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `section_id` INTEGER NOT NULL,
  `published` INTEGER NOT NULL DEFAULT 0,
  `provider_name` varchar(255) NOT NULL DEFAULT 'passport',
  `provider_badge_id` INTEGER NOT NULL,
  `img_url` varchar(255) NOT NULL DEFAULT '',
  `criteria_id` INTEGER NOT NULL
);

--
-- Table: `#__courses_offering_section_codes`
--

CREATE TABLE `#__courses_offering_section_codes` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `section_id` INTEGER NOT NULL DEFAULT 0,
  `code` varchar(10) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `expires` datetime NOT NULL DEFAULT NULL,
  `redeemed` datetime NOT NULL DEFAULT NULL,
  `redeemed_by` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__courses_offering_section_dates`
--

CREATE TABLE `#__courses_offering_section_dates` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `section_id` INTEGER NOT NULL DEFAULT 0,
  `scope` varchar(150) NOT NULL DEFAULT '',
  `scope_id` INTEGER NOT NULL DEFAULT 0,
  `publish_up` datetime NOT NULL DEFAULT NULL,
  `publish_down` datetime NOT NULL DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_section_id` ON `#__courses_offering_section_dates` (`section_id`);
CREATE INDEX IF NOT EXISTS `idx_scope_id` ON `#__courses_offering_section_dates` (`scope_id`);

--
-- Table: `#__courses_offering_sections`
--

CREATE TABLE `#__courses_offering_sections` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `offering_id` INTEGER NOT NULL DEFAULT 0,
  `is_default` INTEGER NOT NULL DEFAULT 0,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `state` INTEGER NOT NULL DEFAULT 1,
  `start_date` datetime NOT NULL DEFAULT NULL,
  `end_date` datetime NOT NULL DEFAULT NULL,
  `publish_up` datetime NOT NULL DEFAULT NULL,
  `publish_down` datetime NOT NULL DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `enrollment` INTEGER NOT NULL DEFAULT 0,
  `grade_policy_id` INTEGER NOT NULL DEFAULT 1,
  `params` text NOT NULL
);

CREATE INDEX IF NOT EXISTS `idx_offering_id` ON `#__courses_offering_sections` (`offering_id`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__courses_offering_sections` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__courses_offering_sections` (`state`);

--
-- Table: `#__courses_offerings`
--

CREATE TABLE `#__courses_offerings` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `course_id` INTEGER NOT NULL DEFAULT 0,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `term` varchar(255) NOT NULL DEFAULT '',
  `state` INTEGER NOT NULL DEFAULT 1,
  `publish_up` datetime NOT NULL DEFAULT NULL,
  `publish_down` datetime NOT NULL DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `params` text NOT NULL
);

CREATE INDEX IF NOT EXISTS `idx_course_id` ON `#__courses_offerings` (`course_id`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__courses_offerings` (`state`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__courses_offerings` (`created_by`);

--
-- Table: `#__courses_page_hits`
--

CREATE TABLE `#__courses_page_hits` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `offering_id` INTEGER NOT NULL DEFAULT 0,
  `page_id` INTEGER NOT NULL DEFAULT 0,
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT NULL,
  `ip` varchar(15) NOT NULL DEFAULT ''
);

CREATE INDEX IF NOT EXISTS `idx_offering_id` ON `#__courses_page_hits` (`offering_id`);
CREATE INDEX IF NOT EXISTS `idx_page_id` ON `#__courses_page_hits` (`page_id`);
CREATE INDEX IF NOT EXISTS `idx_user_id` ON `#__courses_page_hits` (`user_id`);

--
-- Table: `#__courses_pages`
--

CREATE TABLE `#__courses_pages` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `course_id` INTEGER NOT NULL DEFAULT 0,
  `offering_id` varchar(100) NOT NULL DEFAULT '0',
  `section_id` INTEGER NOT NULL DEFAULT 0,
  `url` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `active` INTEGER NOT NULL DEFAULT 0,
  `privacy` varchar(10) NOT NULL DEFAULT ''
);

CREATE INDEX IF NOT EXISTS `idx_offering_id` ON `#__courses_pages` (`offering_id`);

--
-- Table: `#__courses_prerequisites`
--

CREATE TABLE `#__courses_prerequisites` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `section_id` INTEGER NOT NULL DEFAULT 0,
  `item_scope` varchar(255) NOT NULL DEFAULT 'asset',
  `item_id` INTEGER NOT NULL DEFAULT 0,
  `requisite_scope` varchar(255) NOT NULL DEFAULT 'asset',
  `requisite_id` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__courses_progress_factors`
--

CREATE TABLE `#__courses_progress_factors` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `section_id` INTEGER NOT NULL,
  `asset_id` INTEGER NOT NULL
);

--
-- Table: `#__courses_reviews`
--

CREATE TABLE `#__courses_reviews` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `course_id` INTEGER NOT NULL DEFAULT 0,
  `offering_id` INTEGER NOT NULL DEFAULT 0,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `content` text NOT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0,
  `anonymous` INTEGER NOT NULL DEFAULT 0,
  `parent` INTEGER NOT NULL DEFAULT 0,
  `access` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `positive` INTEGER NOT NULL DEFAULT 0,
  `negative` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__courses_roles`
--

CREATE TABLE `#__courses_roles` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `offering_id` INTEGER NOT NULL DEFAULT 0,
  `alias` varchar(150) NOT NULL,
  `title` varchar(150) NOT NULL DEFAULT '',
  `permissions` mediumtext NOT NULL
);

CREATE INDEX IF NOT EXISTS `idx_offering_id` ON `#__courses_roles` (`offering_id`);

--
-- Table: `#__courses_units`
--

CREATE TABLE `#__courses_units` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `offering_id` INTEGER NOT NULL DEFAULT 0,
  `alias` varchar(250) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_offering_id` ON `#__courses_units` (`offering_id`);

--
-- Table: `#__cron_jobs`
--

CREATE TABLE `#__cron_jobs` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `state` INTEGER NOT NULL DEFAULT 0,
  `plugin` varchar(255) NOT NULL DEFAULT '',
  `event` varchar(255) NOT NULL DEFAULT '',
  `last_run` datetime NOT NULL DEFAULT NULL,
  `next_run` datetime NOT NULL DEFAULT NULL,
  `recurrence` varchar(50) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0,
  `active` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  `publish_up` datetime NOT NULL DEFAULT NULL,
  `publish_down` datetime NOT NULL DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_state` ON `#__cron_jobs` (`state`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__cron_jobs` (`created_by`);

--
-- Table: `#__document_resource_rel`
--

CREATE TABLE `#__document_resource_rel` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `document_id` INTEGER NOT NULL,
  `resource_id` INTEGER NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_id` ON `#__document_resource_rel` (`id`);
CREATE UNIQUE INDEX IF NOT EXISTS `uidx_document_id_resource_id` ON `#__document_resource_rel` (`document_id`,`resource_id`);

--
-- Table: `#__document_text_data`
--

CREATE TABLE `#__document_text_data` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `body` text DEFAULT NULL,
  `hash` char(40) NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_hash` ON `#__document_text_data` (`hash`);

--
-- Table: `#__doi_mapping`
--

CREATE TABLE `#__doi_mapping` (
  `local_revision` INTEGER NOT NULL,
  `doi_label` INTEGER NOT NULL,
  `rid` INTEGER NOT NULL,
  `alias` varchar(30) DEFAULT NULL,
  `versionid` INTEGER DEFAULT 0,
  `doi` varchar(50) DEFAULT NULL
);

--
-- Table: `#__email_bounces`
--

CREATE TABLE `#__email_bounces` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `email` varchar(150) DEFAULT NULL,
  `component` varchar(100) DEFAULT NULL,
  `object` varchar(100) DEFAULT NULL,
  `object_id` INTEGER DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `resolved` INTEGER DEFAULT 0
);

--
-- Table: `#__event_registration`
--

CREATE TABLE `#__event_registration` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
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
  `active` INTEGER DEFAULT NULL
);

--
-- Table: `#__events`
--

CREATE TABLE `#__events` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `catid` INTEGER NOT NULL DEFAULT 1,
  `calendar_id` INTEGER DEFAULT NULL,
  `ical_uid` varchar(255) DEFAULT NULL,
  `scope` varchar(100) DEFAULT NULL,
  `scope_id` INTEGER DEFAULT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  `adresse_info` varchar(120) NOT NULL DEFAULT '',
  `contact_info` varchar(120) NOT NULL DEFAULT '',
  `extra_info` varchar(240) NOT NULL DEFAULT '',
  `state` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0,
  `checked_out` INTEGER NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT NULL,
  `publish_up` datetime NOT NULL DEFAULT NULL,
  `publish_down` datetime NOT NULL DEFAULT NULL,
  `allday` INTEGER DEFAULT 0,
  `time_zone` varchar(5) DEFAULT NULL,
  `repeating_rule` varchar(150) DEFAULT NULL,
  `approved` INTEGER NOT NULL DEFAULT 1,
  `registerby` datetime NOT NULL DEFAULT NULL,
  `params` text DEFAULT NULL,
  `restricted` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
);

--
-- Table: `#__events_calendars`
--

CREATE TABLE `#__events_calendars` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `scope` varchar(100) DEFAULT NULL,
  `scope_id` INTEGER DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `color` varchar(100) DEFAULT NULL,
  `published` INTEGER DEFAULT 1,
  `url` varchar(255) DEFAULT NULL,
  `readonly` INTEGER DEFAULT 0,
  `last_fetched` datetime DEFAULT NULL,
  `last_fetched_attempt` datetime DEFAULT NULL,
  `failed_attempts` INTEGER DEFAULT 0
);

--
-- Table: `#__events_categories`
--

CREATE TABLE `#__events_categories` (
  `id` INTEGER NOT NULL DEFAULT 0,
  `color` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
);

--
-- Table: `#__events_config`
--

CREATE TABLE `#__events_config` (
  `param` varchar(100) DEFAULT NULL,
  `value` tinytext DEFAULT NULL
);

--
-- Table: `#__events_pages`
--

CREATE TABLE `#__events_pages` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `event_id` INTEGER DEFAULT 0,
  `alias` varchar(100) NOT NULL,
  `title` varchar(250) NOT NULL,
  `pagetext` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` INTEGER DEFAULT 0,
  `ordering` INTEGER DEFAULT 0,
  `params` text DEFAULT NULL
);

--
-- Table: `#__events_respondent_race_rel`
--

CREATE TABLE `#__events_respondent_race_rel` (
  `respondent_id` INTEGER DEFAULT NULL,
  `race` varchar(255) DEFAULT NULL,
  `tribal_affiliation` varchar(255) DEFAULT NULL
);

--
-- Table: `#__events_respondents`
--

CREATE TABLE `#__events_respondents` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `event_id` INTEGER NOT NULL DEFAULT 0,
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
  `disability_needs` INTEGER DEFAULT NULL,
  `dietary_needs` varchar(500) DEFAULT NULL,
  `attending_dinner` INTEGER DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `arrival` varchar(50) DEFAULT NULL,
  `departure` varchar(50) DEFAULT NULL
);

--
-- Table: `#__extensions`
--

CREATE TABLE `#__extensions` (
  `extension_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  `element` varchar(100) NOT NULL,
  `folder` varchar(100) NOT NULL,
  `client_id` INTEGER NOT NULL,
  `enabled` INTEGER NOT NULL DEFAULT 1,
  `access` INTEGER NOT NULL DEFAULT 1,
  `protected` INTEGER NOT NULL DEFAULT 0,
  `manifest_cache` text NOT NULL,
  `params` text NOT NULL,
  `custom_data` text NOT NULL,
  `system_data` text NOT NULL,
  `checked_out` INTEGER NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT NULL,
  `ordering` INTEGER DEFAULT 0,
  `state` INTEGER DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `element_clientid` ON `#__extensions` (`element`,`client_id`);
CREATE INDEX IF NOT EXISTS `element_folder_clientid` ON `#__extensions` (`element`,`folder`,`client_id`);
CREATE INDEX IF NOT EXISTS `extension` ON `#__extensions` (`type`,`element`,`folder`,`client_id`);

--
-- Table: `#__faq`
--

CREATE TABLE `#__faq` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `alias` varchar(200) DEFAULT NULL,
  `params` text DEFAULT NULL,
  `fulltxt` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` INTEGER DEFAULT 0,
  `checked_out` INTEGER DEFAULT 0,
  `checked_out_time` datetime DEFAULT NULL,
  `state` INTEGER DEFAULT 0,
  `access` INTEGER DEFAULT 0,
  `hits` INTEGER DEFAULT 0,
  `version` INTEGER DEFAULT 0,
  `section` INTEGER NOT NULL DEFAULT 0,
  `category` INTEGER DEFAULT 0,
  `helpful` INTEGER NOT NULL DEFAULT 0,
  `nothelpful` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_section` ON `#__faq` (`section`);
CREATE INDEX IF NOT EXISTS `idx_category` ON `#__faq` (`category`);
CREATE INDEX IF NOT EXISTS `idx_alias` ON `#__faq` (`alias`);

--
-- Table: `#__faq_categories`
--

CREATE TABLE `#__faq_categories` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `alias` varchar(200) DEFAULT NULL,
  `description` varchar(255) DEFAULT '',
  `section` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `access` INTEGER NOT NULL DEFAULT 0,
  `asset_id` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_alias` ON `#__faq_categories` (`alias`);
CREATE INDEX IF NOT EXISTS `idx_section` ON `#__faq_categories` (`section`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__faq_categories` (`state`);

--
-- Table: `#__faq_comments`
--

CREATE TABLE `#__faq_comments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `entry_id` INTEGER NOT NULL DEFAULT 0,
  `content` text DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `anonymous` INTEGER NOT NULL DEFAULT 0,
  `parent` INTEGER NOT NULL DEFAULT 0,
  `asset_id` INTEGER NOT NULL DEFAULT 0,
  `helpful` INTEGER NOT NULL DEFAULT 0,
  `nothelpful` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_entry_id` ON `#__faq_comments` (`entry_id`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__faq_comments` (`state`);

--
-- Table: `#__faq_helpful_log`
--

CREATE TABLE `#__faq_helpful_log` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `object_id` INTEGER DEFAULT 0,
  `ip` varchar(15) DEFAULT NULL,
  `vote` varchar(10) DEFAULT NULL,
  `user_id` INTEGER DEFAULT 0,
  `type` varchar(255) DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_type_object_id` ON `#__faq_helpful_log` (`type`,`object_id`);
CREATE INDEX IF NOT EXISTS `idx_user_id` ON `#__faq_helpful_log` (`user_id`);

--
-- Table: `#__feedback`
--

CREATE TABLE `#__feedback` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER DEFAULT NULL,
  `fullname` varchar(100) DEFAULT '',
  `org` varchar(100) DEFAULT '',
  `quote` text DEFAULT NULL,
  `picture` varchar(250) DEFAULT '',
  `date` datetime DEFAULT NULL,
  `publish_ok` INTEGER DEFAULT 0,
  `contact_ok` INTEGER DEFAULT 0,
  `notes` text DEFAULT NULL,
  `short_quote` text DEFAULT NULL,
  `miniquote` varchar(255) NOT NULL DEFAULT '',
  `admin_rating` INTEGER NOT NULL DEFAULT 0,
  `notable_quote` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__focus_area_resource_type_rel`
--

CREATE TABLE `#__focus_area_resource_type_rel` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `focus_area_id` INTEGER NOT NULL,
  `resource_type_id` INTEGER NOT NULL
);

--
-- Table: `#__focus_areas`
--

CREATE TABLE `#__focus_areas` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `tag_id` INTEGER NOT NULL,
  `mandatory_depth` INTEGER DEFAULT NULL,
  `multiple_depth` INTEGER DEFAULT NULL
);

--
-- Table: `#__forum_attachments`
--

CREATE TABLE `#__forum_attachments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `parent` INTEGER NOT NULL DEFAULT 0,
  `post_id` INTEGER NOT NULL DEFAULT 0,
  `filename` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` INTEGER DEFAULT 1
);

CREATE INDEX IF NOT EXISTS `idx_filename_post_id` ON `#__forum_attachments` (`filename`,`post_id`);
CREATE INDEX IF NOT EXISTS `idx_parent` ON `#__forum_attachments` (`parent`);

--
-- Table: `#__forum_categories`
--

CREATE TABLE `#__forum_categories` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0,
  `access` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `scope` varchar(100) NOT NULL DEFAULT 'site',
  `scope_id` INTEGER NOT NULL DEFAULT 0,
  `section_id` INTEGER NOT NULL DEFAULT 0,
  `closed` INTEGER NOT NULL DEFAULT 0,
  `asset_id` INTEGER NOT NULL DEFAULT 0,
  `object_id` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_scope_scope_id` ON `#__forum_categories` (`scope`,`scope_id`);
CREATE INDEX IF NOT EXISTS `idx_asset_id` ON `#__forum_categories` (`asset_id`);
CREATE INDEX IF NOT EXISTS `idx_object_id` ON `#__forum_categories` (`object_id`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__forum_categories` (`state`);
CREATE INDEX IF NOT EXISTS `idx_access` ON `#__forum_categories` (`access`);
CREATE INDEX IF NOT EXISTS `idx_section_id` ON `#__forum_categories` (`section_id`);
CREATE INDEX IF NOT EXISTS `idx_closed` ON `#__forum_categories` (`closed`);

--
-- Table: `#__forum_posts`
--

CREATE TABLE `#__forum_posts` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `category_id` INTEGER NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `sticky` INTEGER NOT NULL DEFAULT 0,
  `parent` INTEGER NOT NULL DEFAULT 0,
  `hits` INTEGER NOT NULL DEFAULT 0,
  `scope` varchar(100) NOT NULL DEFAULT 'site',
  `scope_id` INTEGER NOT NULL DEFAULT 0,
  `scope_sub_id` INTEGER NOT NULL DEFAULT 0,
  `access` INTEGER NOT NULL DEFAULT 0,
  `anonymous` INTEGER NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0,
  `last_activity` datetime NOT NULL DEFAULT NULL,
  `asset_id` INTEGER NOT NULL DEFAULT 0,
  `object_id` INTEGER NOT NULL DEFAULT 0,
  `lft` INTEGER NOT NULL DEFAULT 0,
  `rgt` INTEGER NOT NULL DEFAULT 0,
  `thread` INTEGER NOT NULL DEFAULT 0,
  `closed` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_scope_scope_id` ON `#__forum_posts` (`scope`,`scope_id`);
CREATE INDEX IF NOT EXISTS `idx_category_id` ON `#__forum_posts` (`category_id`);
CREATE INDEX IF NOT EXISTS `idx_access` ON `#__forum_posts` (`access`);
CREATE INDEX IF NOT EXISTS `idx_asset_id` ON `#__forum_posts` (`asset_id`);
CREATE INDEX IF NOT EXISTS `idx_object_id` ON `#__forum_posts` (`object_id`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__forum_posts` (`state`);
CREATE INDEX IF NOT EXISTS `idx_sticky` ON `#__forum_posts` (`sticky`);
CREATE INDEX IF NOT EXISTS `idx_parent` ON `#__forum_posts` (`parent`);

--
-- Table: `#__forum_sections`
--

CREATE TABLE `#__forum_sections` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `access` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `scope` varchar(100) NOT NULL DEFAULT 'site',
  `scope_id` INTEGER NOT NULL DEFAULT 0,
  `asset_id` INTEGER NOT NULL DEFAULT 0,
  `object_id` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_scoped` ON `#__forum_sections` (`scope`,`scope_id`);
CREATE INDEX IF NOT EXISTS `idx_asset_id` ON `#__forum_sections` (`asset_id`);
CREATE INDEX IF NOT EXISTS `idx_object_id` ON `#__forum_sections` (`object_id`);
CREATE INDEX IF NOT EXISTS `idx_access` ON `#__forum_sections` (`access`);

--
-- Table: `#__import_hooks`
--

CREATE TABLE `#__import_hooks` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `event` varchar(25) DEFAULT NULL,
  `type` varchar(150) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `file` varchar(100) DEFAULT NULL,
  `state` INTEGER NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__import_runs`
--

CREATE TABLE `#__import_runs` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `import_id` INTEGER DEFAULT NULL,
  `processed` INTEGER DEFAULT NULL,
  `count` INTEGER DEFAULT NULL,
  `ran_by` INTEGER DEFAULT NULL,
  `ran_at` datetime DEFAULT NULL,
  `dry_run` INTEGER DEFAULT 0
);

--
-- Table: `#__imports`
--

CREATE TABLE `#__imports` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `type` varchar(150) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `file` varchar(255) DEFAULT '',
  `count` INTEGER NOT NULL DEFAULT 0,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `state` INTEGER NOT NULL DEFAULT 1,
  `mode` varchar(10) DEFAULT 'UPDATE',
  `params` text DEFAULT NULL,
  `hooks` text DEFAULT NULL,
  `fields` text NOT NULL
);

--
-- Table: `#__incremental_registration_group_label_rel`
--

CREATE TABLE `#__incremental_registration_group_label_rel` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `group_id` INTEGER NOT NULL,
  `label_id` INTEGER NOT NULL
);

--
-- Table: `#__incremental_registration_groups`
--

CREATE TABLE `#__incremental_registration_groups` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `hours` INTEGER NOT NULL
);

--
-- Table: `#__incremental_registration_labels`
--

CREATE TABLE `#__incremental_registration_labels` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `field` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL
);

--
-- Table: `#__incremental_registration_options`
--

CREATE TABLE `#__incremental_registration_options` (
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `popover_text` text NOT NULL,
  `award_per` INTEGER NOT NULL,
  `test_group` INTEGER NOT NULL
);

--
-- Table: `#__incremental_registration_popover_recurrence`
--

CREATE TABLE `#__incremental_registration_popover_recurrence` (
  `idx` INTEGER NOT NULL,
  `hours` INTEGER NOT NULL
);

--
-- Table: `#__item_comment_files`
--

CREATE TABLE `#__item_comment_files` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `comment_id` INTEGER NOT NULL DEFAULT 0,
  `filename` varchar(100) DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_comment_id` ON `#__item_comment_files` (`comment_id`);

--
-- Table: `#__item_comments`
--

CREATE TABLE `#__item_comments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `item_id` INTEGER NOT NULL DEFAULT 0,
  `item_type` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0,
  `anonymous` INTEGER NOT NULL DEFAULT 0,
  `parent` INTEGER NOT NULL DEFAULT 0,
  `notify` INTEGER NOT NULL DEFAULT 0,
  `access` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `positive` INTEGER NOT NULL DEFAULT 0,
  `negative` INTEGER NOT NULL DEFAULT 0,
  `rating` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_item_type_item_id` ON `#__item_comments` (`item_type`,`item_id`);
CREATE INDEX IF NOT EXISTS `idx_parent` ON `#__item_comments` (`parent`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__item_comments` (`state`);

--
-- Table: `#__item_votes`
--

CREATE TABLE `#__item_votes` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `item_id` INTEGER NOT NULL DEFAULT 0,
  `item_type` varchar(255) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `vote` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_item_type_item_id` ON `#__item_votes` (`item_type`,`item_id`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__item_votes` (`created_by`);

--
-- Table: `#__jobs_admins`
--

CREATE TABLE `#__jobs_admins` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `jid` INTEGER NOT NULL DEFAULT 0,
  `uid` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__jobs_applications`
--

CREATE TABLE `#__jobs_applications` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `jid` INTEGER NOT NULL DEFAULT 0,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `applied` datetime NOT NULL DEFAULT NULL,
  `withdrawn` datetime DEFAULT NULL,
  `cover` text DEFAULT NULL,
  `resumeid` INTEGER DEFAULT 0,
  `status` INTEGER DEFAULT 1,
  `reason` varchar(255) DEFAULT ''
);

--
-- Table: `#__jobs_categories`
--

CREATE TABLE `#__jobs_categories` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `category` varchar(150) NOT NULL DEFAULT '',
  `ordernum` INTEGER NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL
);

--
-- Table: `#__jobs_employers`
--

CREATE TABLE `#__jobs_employers` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `added` datetime NOT NULL DEFAULT NULL,
  `subscriptionid` INTEGER NOT NULL DEFAULT 0,
  `companyName` varchar(250) DEFAULT '',
  `companyLocation` varchar(250) DEFAULT '',
  `companyWebsite` varchar(250) DEFAULT ''
);

--
-- Table: `#__jobs_openings`
--

CREATE TABLE `#__jobs_openings` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `cid` INTEGER DEFAULT 0,
  `employerid` INTEGER NOT NULL DEFAULT 0,
  `code` INTEGER NOT NULL DEFAULT 0,
  `title` varchar(200) NOT NULL DEFAULT '',
  `companyName` varchar(200) NOT NULL DEFAULT '',
  `companyLocation` varchar(200) DEFAULT '',
  `companyLocationCountry` varchar(100) DEFAULT '',
  `companyWebsite` varchar(200) DEFAULT '',
  `description` text DEFAULT NULL,
  `addedBy` INTEGER NOT NULL DEFAULT 0,
  `editedBy` INTEGER DEFAULT 0,
  `added` datetime NOT NULL DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `status` INTEGER NOT NULL DEFAULT 0,
  `type` INTEGER NOT NULL DEFAULT 0,
  `closedate` datetime DEFAULT NULL,
  `opendate` datetime DEFAULT NULL,
  `startdate` datetime DEFAULT NULL,
  `applyExternalUrl` varchar(250) DEFAULT '',
  `applyInternal` INTEGER DEFAULT 0,
  `contactName` varchar(100) DEFAULT '',
  `contactEmail` varchar(100) DEFAULT '',
  `contactPhone` varchar(100) DEFAULT ''
);

--
-- Table: `#__jobs_prefs`
--

CREATE TABLE `#__jobs_prefs` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `category` varchar(20) NOT NULL DEFAULT 'resume',
  `filters` text DEFAULT NULL
);

--
-- Table: `#__jobs_resumes`
--

CREATE TABLE `#__jobs_resumes` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `filename` varchar(100) DEFAULT NULL,
  `main` INTEGER DEFAULT 1
);

--
-- Table: `#__jobs_seekers`
--

CREATE TABLE `#__jobs_seekers` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `active` INTEGER NOT NULL DEFAULT 0,
  `lookingfor` varchar(255) DEFAULT '',
  `tagline` varchar(255) DEFAULT '',
  `linkedin` varchar(255) DEFAULT '',
  `url` varchar(255) DEFAULT '',
  `updated` datetime DEFAULT NULL,
  `sought_cid` INTEGER DEFAULT 0,
  `sought_type` INTEGER DEFAULT 0
);

--
-- Table: `#__jobs_shortlist`
--

CREATE TABLE `#__jobs_shortlist` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `emp` INTEGER NOT NULL DEFAULT 0,
  `seeker` INTEGER NOT NULL DEFAULT 0,
  `category` varchar(11) NOT NULL DEFAULT 'resume',
  `jobid` INTEGER DEFAULT 0,
  `added` datetime DEFAULT NULL
);

--
-- Table: `#__jobs_stats`
--

CREATE TABLE `#__jobs_stats` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `itemid` INTEGER NOT NULL,
  `category` varchar(11) NOT NULL DEFAULT '',
  `total_viewed` INTEGER DEFAULT 0,
  `total_shared` INTEGER DEFAULT 0,
  `viewed_today` INTEGER DEFAULT 0,
  `lastviewed` datetime DEFAULT NULL
);

--
-- Table: `#__jobs_types`
--

CREATE TABLE `#__jobs_types` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `category` varchar(150) NOT NULL DEFAULT ''
);

--
-- Table: `#__languages`
--

CREATE TABLE `#__languages` (
  `lang_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `lang_code` char(7) NOT NULL,
  `title` varchar(50) NOT NULL,
  `title_native` varchar(50) NOT NULL,
  `sef` varchar(50) NOT NULL,
  `image` varchar(50) NOT NULL,
  `description` varchar(512) NOT NULL,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `sitename` varchar(1024) NOT NULL DEFAULT '',
  `published` INTEGER NOT NULL DEFAULT 0,
  `access` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_sef` ON `#__languages` (`sef`);
CREATE UNIQUE INDEX IF NOT EXISTS `idx_image` ON `#__languages` (`image`);
CREATE UNIQUE INDEX IF NOT EXISTS `idx_langcode` ON `#__languages` (`lang_code`);
CREATE INDEX IF NOT EXISTS `idx_access` ON `#__languages` (`access`);
CREATE INDEX IF NOT EXISTS `idx_ordering` ON `#__languages` (`ordering`);

--
-- Table: `#__licenses`
--

CREATE TABLE `#__licenses` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `alias` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
);

--
-- Table: `#__licenses_tools`
--

CREATE TABLE `#__licenses_tools` (
  `license_id` INTEGER DEFAULT 0,
  `tool_id` INTEGER DEFAULT 0,
  `created` datetime NOT NULL
);

--
-- Table: `#__licenses_users`
--

CREATE TABLE `#__licenses_users` (
  `license_id` INTEGER NOT NULL DEFAULT 0,
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  PRIMARY KEY (`license_id`,`user_id`)
);

--
-- Table: `#__market_history`
--

CREATE TABLE `#__market_history` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `itemid` INTEGER NOT NULL DEFAULT 0,
  `category` varchar(50) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `market_value` INTEGER DEFAULT 0
);

--
-- Table: `#__media_tracking`
--

CREATE TABLE `#__media_tracking` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER DEFAULT NULL,
  `session_id` varchar(200) DEFAULT NULL,
  `ip_address` varchar(100) DEFAULT NULL,
  `object_id` INTEGER DEFAULT NULL,
  `object_type` varchar(100) DEFAULT NULL,
  `object_duration` INTEGER DEFAULT NULL,
  `current_position` INTEGER DEFAULT NULL,
  `farthest_position` INTEGER DEFAULT NULL,
  `current_position_timestamp` datetime DEFAULT NULL,
  `farthest_position_timestamp` datetime DEFAULT NULL,
  `completed` INTEGER DEFAULT NULL,
  `total_views` INTEGER DEFAULT NULL,
  `total_viewing_time` INTEGER DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_user_id` ON `#__media_tracking` (`user_id`);
CREATE INDEX IF NOT EXISTS `idx_session_id` ON `#__media_tracking` (`session_id`);
CREATE INDEX IF NOT EXISTS `idx_object_id` ON `#__media_tracking` (`object_id`);

--
-- Table: `#__media_tracking_detailed`
--

CREATE TABLE `#__media_tracking_detailed` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER DEFAULT NULL,
  `session_id` varchar(200) DEFAULT NULL,
  `ip_address` varchar(100) DEFAULT NULL,
  `object_id` INTEGER DEFAULT NULL,
  `object_type` varchar(100) DEFAULT NULL,
  `object_duration` INTEGER DEFAULT NULL,
  `current_position` INTEGER DEFAULT NULL,
  `farthest_position` INTEGER DEFAULT NULL,
  `current_position_timestamp` datetime DEFAULT NULL,
  `farthest_position_timestamp` datetime DEFAULT NULL,
  `completed` INTEGER DEFAULT NULL
);

--
-- Table: `#__menu`
--

CREATE TABLE `#__menu` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `menutype` varchar(24) NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(1024) NOT NULL,
  `link` varchar(1024) NOT NULL,
  `type` varchar(16) NOT NULL,
  `published` INTEGER NOT NULL DEFAULT 0,
  `parent_id` INTEGER NOT NULL DEFAULT 1,
  `level` INTEGER NOT NULL DEFAULT 0,
  `component_id` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `checked_out` INTEGER NOT NULL DEFAULT 0,
  `checked_out_time` timestamp NOT NULL DEFAULT NULL,
  `browserNav` INTEGER NOT NULL DEFAULT 0,
  `access` INTEGER NOT NULL DEFAULT 0,
  `img` varchar(255) NOT NULL,
  `template_style_id` INTEGER NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  `lft` INTEGER NOT NULL DEFAULT 0,
  `rgt` INTEGER NOT NULL DEFAULT 0,
  `home` INTEGER NOT NULL DEFAULT 0,
  `language` char(7) NOT NULL DEFAULT '',
  `client_id` INTEGER NOT NULL DEFAULT 0
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_client_id_parent_id_alias_language` ON `#__menu` (`client_id`,`parent_id`,`alias`,`language`);
CREATE INDEX IF NOT EXISTS `idx_componentid` ON `#__menu` (`component_id`,`menutype`,`published`,`access`);
CREATE INDEX IF NOT EXISTS `idx_menutype` ON `#__menu` (`menutype`);
CREATE INDEX IF NOT EXISTS `idx_left_right` ON `#__menu` (`lft`,`rgt`);
CREATE INDEX IF NOT EXISTS `idx_alias` ON `#__menu` (`alias`);
CREATE INDEX IF NOT EXISTS `idx_path` ON `#__menu` (`path`);
CREATE INDEX IF NOT EXISTS `idx_language` ON `#__menu` (`language`);

--
-- Table: `#__menu_types`
--

CREATE TABLE `#__menu_types` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `menutype` varchar(24) NOT NULL,
  `title` varchar(48) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT ''
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_menutype` ON `#__menu_types` (`menutype`);

--
-- Table: `#__messages`
--

CREATE TABLE `#__messages` (
  `message_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id_from` INTEGER NOT NULL DEFAULT 0,
  `user_id_to` INTEGER NOT NULL DEFAULT 0,
  `folder_id` INTEGER NOT NULL DEFAULT 0,
  `date_time` datetime NOT NULL DEFAULT NULL,
  `state` INTEGER NOT NULL DEFAULT 0,
  `priority` INTEGER NOT NULL DEFAULT 0,
  `subject` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL
);

CREATE INDEX IF NOT EXISTS `useridto_state` ON `#__messages` (`user_id_to`,`state`);

--
-- Table: `#__messages_cfg`
--

CREATE TABLE `#__messages_cfg` (
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `cfg_name` varchar(100) NOT NULL DEFAULT '',
  `cfg_value` varchar(255) NOT NULL DEFAULT ''
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_user_var_name` ON `#__messages_cfg` (`user_id`,`cfg_name`);

--
-- Table: `#__metrics_author_cluster`
--

CREATE TABLE `#__metrics_author_cluster` (
  `authorid` varchar(60) NOT NULL DEFAULT '0',
  `classes` INTEGER DEFAULT 0,
  `users` INTEGER DEFAULT 0,
  `schools` INTEGER DEFAULT 0,
  PRIMARY KEY (`authorid`)
);

--
-- Table: `#__metrics_ipgeo_cache`
--

CREATE TABLE `#__metrics_ipgeo_cache` (
  `ip` INTEGER NOT NULL DEFAULT 0,
  `countrySHORT` char(2) NOT NULL DEFAULT '',
  `countryLONG` varchar(64) NOT NULL DEFAULT '',
  `ipREGION` varchar(128) NOT NULL DEFAULT '',
  `ipCITY` varchar(128) NOT NULL DEFAULT '',
  `ipLATITUDE` double DEFAULT NULL,
  `ipLONGITUDE` double DEFAULT NULL,
  `lookup_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ip`)
);

CREATE INDEX IF NOT EXISTS `idx_lookup_datetime` ON `#__metrics_ipgeo_cache` (`lookup_datetime`);

--
-- Table: `#__migrations`
--

CREATE TABLE `#__migrations` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `file` varchar(255) NOT NULL DEFAULT '',
  `scope` varchar(255) NOT NULL,
  `hash` char(32) NOT NULL DEFAULT '',
  `direction` varchar(10) NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  `action_by` varchar(255) NOT NULL DEFAULT ''
);

--
-- Table: `#__modules`
--

CREATE TABLE `#__modules` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `position` varchar(50) NOT NULL DEFAULT '',
  `checked_out` INTEGER NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT NULL,
  `publish_up` datetime NOT NULL DEFAULT NULL,
  `publish_down` datetime NOT NULL DEFAULT NULL,
  `published` INTEGER NOT NULL DEFAULT 0,
  `module` varchar(50) DEFAULT NULL,
  `access` INTEGER NOT NULL DEFAULT 0,
  `showtitle` INTEGER NOT NULL DEFAULT 1,
  `params` text NOT NULL,
  `client_id` INTEGER NOT NULL DEFAULT 0,
  `language` char(7) NOT NULL
);

CREATE INDEX IF NOT EXISTS `published` ON `#__modules` (`published`,`access`);
CREATE INDEX IF NOT EXISTS `newsfeeds` ON `#__modules` (`module`,`published`);
CREATE INDEX IF NOT EXISTS `idx_language` ON `#__modules` (`language`);

--
-- Table: `#__modules_menu`
--

CREATE TABLE `#__modules_menu` (
  `moduleid` INTEGER NOT NULL DEFAULT 0,
  `menuid` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`moduleid`,`menuid`)
);

--
-- Table: `#__newsfeeds`
--

CREATE TABLE `#__newsfeeds` (
  `catid` INTEGER NOT NULL DEFAULT 0,
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `link` varchar(200) NOT NULL DEFAULT '',
  `filename` varchar(200) DEFAULT NULL,
  `published` INTEGER NOT NULL DEFAULT 0,
  `numarticles` INTEGER NOT NULL DEFAULT 1,
  `cache_time` INTEGER NOT NULL DEFAULT 3600,
  `checked_out` INTEGER NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT NULL,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `rtl` INTEGER NOT NULL DEFAULT 0,
  `access` INTEGER NOT NULL DEFAULT 0,
  `language` char(7) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `metadata` text NOT NULL,
  `xreference` varchar(50) NOT NULL,
  `publish_up` datetime NOT NULL DEFAULT NULL,
  `publish_down` datetime NOT NULL DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_access` ON `#__newsfeeds` (`access`);
CREATE INDEX IF NOT EXISTS `idx_checkout` ON `#__newsfeeds` (`checked_out`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__newsfeeds` (`published`);
CREATE INDEX IF NOT EXISTS `idx_catid` ON `#__newsfeeds` (`catid`);
CREATE INDEX IF NOT EXISTS `idx_createdby` ON `#__newsfeeds` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_language` ON `#__newsfeeds` (`language`);
CREATE INDEX IF NOT EXISTS `idx_xreference` ON `#__newsfeeds` (`xreference`);

--
-- Table: `#__newsletter_mailing_recipient_actions`
--

CREATE TABLE `#__newsletter_mailing_recipient_actions` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `mailingid` INTEGER DEFAULT NULL,
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
  `ipLONGITUDE` double DEFAULT NULL
);

--
-- Table: `#__newsletter_mailing_recipients`
--

CREATE TABLE `#__newsletter_mailing_recipients` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `mid` INTEGER DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_sent` datetime DEFAULT NULL
);

--
-- Table: `#__newsletter_mailinglist_emails`
--

CREATE TABLE `#__newsletter_mailinglist_emails` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `mid` INTEGER DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `confirmed` INTEGER DEFAULT 0,
  `date_added` datetime DEFAULT NULL,
  `date_confirmed` datetime DEFAULT NULL
);

--
-- Table: `#__newsletter_mailinglist_unsubscribes`
--

CREATE TABLE `#__newsletter_mailinglist_unsubscribes` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `mid` INTEGER DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `reason` text DEFAULT NULL
);

--
-- Table: `#__newsletter_mailinglists`
--

CREATE TABLE `#__newsletter_mailinglists` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `private` INTEGER DEFAULT NULL,
  `deleted` INTEGER DEFAULT 0
);

--
-- Table: `#__newsletter_mailings`
--

CREATE TABLE `#__newsletter_mailings` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `nid` INTEGER DEFAULT NULL,
  `lid` INTEGER DEFAULT NULL,
  `subject` varchar(250) DEFAULT NULL,
  `html_body` longtext DEFAULT NULL,
  `plain_body` longtext DEFAULT NULL,
  `headers` text DEFAULT NULL,
  `args` text DEFAULT NULL,
  `tracking` INTEGER DEFAULT 1,
  `date` datetime DEFAULT NULL,
  `deleted` INTEGER DEFAULT 0
);

--
-- Table: `#__newsletter_primary_story`
--

CREATE TABLE `#__newsletter_primary_story` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `nid` INTEGER NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `story` text DEFAULT NULL,
  `readmore_title` varchar(100) DEFAULT NULL,
  `readmore_link` varchar(200) DEFAULT NULL,
  `order` INTEGER DEFAULT NULL,
  `deleted` INTEGER DEFAULT 0
);

--
-- Table: `#__newsletter_secondary_story`
--

CREATE TABLE `#__newsletter_secondary_story` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `nid` INTEGER NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `story` text DEFAULT NULL,
  `readmore_title` varchar(100) DEFAULT NULL,
  `readmore_link` varchar(200) DEFAULT NULL,
  `order` INTEGER DEFAULT NULL,
  `deleted` INTEGER DEFAULT 0
);

--
-- Table: `#__newsletter_templates`
--

CREATE TABLE `#__newsletter_templates` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `editable` INTEGER DEFAULT 1,
  `name` varchar(100) DEFAULT NULL,
  `template` text DEFAULT NULL,
  `primary_title_color` varchar(100) DEFAULT NULL,
  `primary_text_color` varchar(100) DEFAULT NULL,
  `secondary_title_color` varchar(100) DEFAULT NULL,
  `secondary_text_color` varchar(100) DEFAULT NULL,
  `deleted` INTEGER DEFAULT 0
);

--
-- Table: `#__newsletters`
--

CREATE TABLE `#__newsletters` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `alias` varchar(150) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `issue` INTEGER DEFAULT NULL,
  `type` varchar(50) DEFAULT 'html',
  `template` INTEGER DEFAULT NULL,
  `published` INTEGER DEFAULT 1,
  `sent` INTEGER DEFAULT 0,
  `html_content` mediumtext DEFAULT NULL,
  `plain_content` mediumtext DEFAULT NULL,
  `tracking` INTEGER DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` INTEGER DEFAULT NULL,
  `deleted` INTEGER DEFAULT 0,
  `params` text DEFAULT NULL
);

--
-- Table: `#__oauthp_consumers`
--

CREATE TABLE `#__oauthp_consumers` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `state` INTEGER NOT NULL,
  `token` varchar(250) NOT NULL,
  `secret` varchar(250) NOT NULL,
  `callback_url` varchar(250) NOT NULL,
  `xauth` INTEGER NOT NULL,
  `xauth_grant` INTEGER NOT NULL
);

--
-- Table: `#__oauthp_nonces`
--

CREATE TABLE `#__oauthp_nonces` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `nonce` varchar(250) NOT NULL,
  `stamp` INTEGER NOT NULL,
  `created` datetime NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_nonce_stamp` ON `#__oauthp_nonces` (`nonce`,`stamp`);

--
-- Table: `#__oauthp_tokens`
--

CREATE TABLE `#__oauthp_tokens` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `consumer_id` INTEGER NOT NULL,
  `user_id` INTEGER NOT NULL,
  `state` INTEGER NOT NULL,
  `token` varchar(250) NOT NULL,
  `token_secret` varchar(250) NOT NULL,
  `callback_url` varchar(250) NOT NULL,
  `verifier` varchar(250) NOT NULL,
  `created` datetime NOT NULL
);

--
-- Table: `#__order_items`
--

CREATE TABLE `#__order_items` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `oid` INTEGER NOT NULL DEFAULT 0,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `itemid` INTEGER NOT NULL DEFAULT 0,
  `price` INTEGER NOT NULL DEFAULT 0,
  `quantity` INTEGER NOT NULL DEFAULT 0,
  `selections` text DEFAULT NULL
);

--
-- Table: `#__orders`
--

CREATE TABLE `#__orders` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `type` varchar(20) DEFAULT NULL,
  `total` INTEGER DEFAULT 0,
  `status` INTEGER NOT NULL DEFAULT 0,
  `details` text DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `ordered` datetime NOT NULL DEFAULT NULL,
  `status_changed` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL
);

--
-- Table: `#__overrider`
--

CREATE TABLE `#__overrider` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `constant` varchar(255) NOT NULL,
  `string` text NOT NULL,
  `file` varchar(255) NOT NULL
);

--
-- Table: `#__password_blacklist`
--

CREATE TABLE `#__password_blacklist` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `word` char(32) NOT NULL
);

--
-- Table: `#__password_character_class`
--

CREATE TABLE `#__password_character_class` (
  `flag` INTEGER NOT NULL,
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` char(32) NOT NULL,
  `regex` char(255) NOT NULL
);

--
-- Table: `#__password_rule`
--

CREATE TABLE `#__password_rule` (
  `class` char(255) DEFAULT NULL,
  `description` char(255) DEFAULT NULL,
  `enabled` INTEGER NOT NULL DEFAULT 0,
  `failuremsg` char(255) DEFAULT NULL,
  `grp` char(32) NOT NULL,
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `rule` char(255) DEFAULT NULL,
  `value` char(255) DEFAULT NULL
);

--
-- Table: `#__plugin_params`
--

CREATE TABLE `#__plugin_params` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `object_id` INTEGER DEFAULT 0,
  `folder` varchar(100) DEFAULT NULL,
  `element` varchar(100) DEFAULT NULL,
  `params` text DEFAULT NULL
);

--
-- Table: `#__poll_data`
--

CREATE TABLE `#__poll_data` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `pollid` INTEGER NOT NULL DEFAULT 0,
  `text` text NOT NULL,
  `hits` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_pollid_text` ON `#__poll_data` (`pollid`,`text`);

--
-- Table: `#__poll_date`
--

CREATE TABLE `#__poll_date` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `date` datetime NOT NULL DEFAULT NULL,
  `vote_id` INTEGER NOT NULL DEFAULT 0,
  `poll_id` INTEGER NOT NULL DEFAULT 0,
  `voter_ip` varchar(50) DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_poll_id` ON `#__poll_date` (`poll_id`);

--
-- Table: `#__poll_menu`
--

CREATE TABLE `#__poll_menu` (
  `pollid` INTEGER NOT NULL DEFAULT 0,
  `menuid` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`pollid`,`menuid`)
);

--
-- Table: `#__polls`
--

CREATE TABLE `#__polls` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(150) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `voters` INTEGER NOT NULL DEFAULT 0,
  `checked_out` INTEGER NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT NULL,
  `published` INTEGER NOT NULL DEFAULT 0,
  `access` INTEGER NOT NULL DEFAULT 0,
  `lag` INTEGER NOT NULL DEFAULT 0,
  `open` INTEGER NOT NULL DEFAULT 0,
  `opened` date DEFAULT NULL,
  `closed` date DEFAULT NULL
);

--
-- Table: `#__profile_completion_awards`
--

CREATE TABLE `#__profile_completion_awards` (
  `user_id` INTEGER NOT NULL,
  `name` INTEGER NOT NULL DEFAULT 0,
  `orgtype` INTEGER NOT NULL DEFAULT 0,
  `organization` INTEGER NOT NULL DEFAULT 0,
  `countryresident` INTEGER NOT NULL DEFAULT 0,
  `countryorigin` INTEGER NOT NULL DEFAULT 0,
  `gender` INTEGER NOT NULL DEFAULT 0,
  `url` INTEGER NOT NULL DEFAULT 0,
  `reason` INTEGER NOT NULL DEFAULT 0,
  `race` INTEGER NOT NULL DEFAULT 0,
  `phone` INTEGER NOT NULL DEFAULT 0,
  `picture` INTEGER NOT NULL DEFAULT 0,
  `opted_out` INTEGER NOT NULL DEFAULT 0,
  `logins` INTEGER NOT NULL DEFAULT 1,
  `invocations` INTEGER NOT NULL DEFAULT 0,
  `last_bothered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `bothered_times` INTEGER NOT NULL DEFAULT 0,
  `edited_profile` INTEGER NOT NULL DEFAULT 0,
  `mailPreferenceOption` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`)
);

--
-- Table: `#__project_activity`
--

CREATE TABLE `#__project_activity` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `projectid` INTEGER NOT NULL DEFAULT 0,
  `userid` INTEGER NOT NULL DEFAULT 0,
  `referenceid` varchar(255) NOT NULL DEFAULT '0',
  `managers_only` INTEGER DEFAULT 0,
  `admin` INTEGER DEFAULT 0,
  `commentable` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `recorded` datetime NOT NULL DEFAULT NULL,
  `activity` varchar(255) NOT NULL DEFAULT '',
  `highlighted` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(255) DEFAULT NULL,
  `class` varchar(150) DEFAULT NULL
);

--
-- Table: `#__project_comments`
--

CREATE TABLE `#__project_comments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `itemid` INTEGER NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `activityid` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `parent_activity` INTEGER DEFAULT 0,
  `anonymous` INTEGER DEFAULT 0,
  `admin` INTEGER DEFAULT 0,
  `tbl` varchar(50) NOT NULL DEFAULT 'blog'
);

--
-- Table: `#__project_database_versions`
--

CREATE TABLE `#__project_database_versions` (
  `id` INTEGER NOT NULL,
  `database_name` varchar(64) NOT NULL,
  `version` INTEGER NOT NULL DEFAULT 1,
  `data_definition` text DEFAULT NULL,
  PRIMARY KEY (`id`,`database_name`,`version`)
);

--
-- Table: `#__project_databases`
--

CREATE TABLE `#__project_databases` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `project` INTEGER NOT NULL,
  `database_name` varchar(64) NOT NULL,
  `title` varchar(127) NOT NULL DEFAULT '',
  `source_file` varchar(127) NOT NULL,
  `source_dir` varchar(127) NOT NULL,
  `source_revision` varchar(56) NOT NULL,
  `description` text DEFAULT NULL,
  `data_definition` text DEFAULT NULL,
  `revision` INTEGER DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updated_by` INTEGER DEFAULT NULL
);

--
-- Table: `#__project_logs`
--

CREATE TABLE `#__project_logs` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `projectid` INTEGER NOT NULL DEFAULT 0,
  `userid` INTEGER NOT NULL DEFAULT 0,
  `ajax` INTEGER DEFAULT 0,
  `owner` INTEGER DEFAULT 0,
  `ip` varchar(15) DEFAULT '0',
  `section` varchar(100) DEFAULT 'general',
  `layout` varchar(100) DEFAULT '',
  `action` varchar(100) DEFAULT '',
  `time` datetime NOT NULL DEFAULT NULL,
  `request_uri` tinytext DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_projectid` ON `#__project_logs` (`projectid`);

--
-- Table: `#__project_microblog`
--

CREATE TABLE `#__project_microblog` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `blogentry` text DEFAULT NULL,
  `posted` datetime NOT NULL DEFAULT NULL,
  `posted_by` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER DEFAULT 0,
  `params` tinytext DEFAULT NULL,
  `projectid` INTEGER NOT NULL DEFAULT 0,
  `activityid` INTEGER NOT NULL DEFAULT 0,
  `managers_only` INTEGER DEFAULT 0
);

--
-- Table: `#__project_owners`
--

CREATE TABLE `#__project_owners` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `projectid` INTEGER NOT NULL DEFAULT 0,
  `userid` INTEGER NOT NULL DEFAULT 0,
  `groupid` INTEGER DEFAULT 0,
  `invited_name` varchar(100) DEFAULT NULL,
  `invited_email` varchar(100) DEFAULT NULL,
  `invited_code` varchar(10) DEFAULT NULL,
  `added` datetime NOT NULL,
  `lastvisit` datetime DEFAULT NULL,
  `prev_visit` datetime DEFAULT NULL,
  `status` INTEGER NOT NULL DEFAULT 0,
  `num_visits` INTEGER NOT NULL DEFAULT 0,
  `role` INTEGER NOT NULL DEFAULT 0,
  `native` INTEGER NOT NULL DEFAULT 0,
  `params` text DEFAULT NULL
);

--
-- Table: `#__project_public_stamps`
--

CREATE TABLE `#__project_public_stamps` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `stamp` varchar(30) NOT NULL DEFAULT '0',
  `projectid` INTEGER NOT NULL DEFAULT 0,
  `listed` INTEGER NOT NULL DEFAULT 0,
  `type` varchar(50) NOT NULL DEFAULT 'files',
  `reference` text NOT NULL,
  `expires` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_stamp` ON `#__project_public_stamps` (`stamp`);

--
-- Table: `#__project_remote_files`
--

CREATE TABLE `#__project_remote_files` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `projectid` INTEGER NOT NULL DEFAULT 0,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified_by` INTEGER DEFAULT 0,
  `paired` INTEGER DEFAULT 0,
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
  `remote_editing` INTEGER NOT NULL DEFAULT 0,
  `remote_id` varchar(100) NOT NULL,
  `original_id` varchar(100) NOT NULL,
  `remote_parent` varchar(100) DEFAULT NULL,
  `remote_title` varchar(140) DEFAULT NULL,
  `remote_md5` varchar(32) DEFAULT NULL,
  `remote_format` varchar(200) DEFAULT NULL,
  `remote_author` varchar(100) DEFAULT NULL,
  `remote_modified` datetime DEFAULT NULL
);

--
-- Table: `#__project_stats`
--

CREATE TABLE `#__project_stats` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `month` INTEGER DEFAULT NULL,
  `year` INTEGER DEFAULT NULL,
  `week` INTEGER DEFAULT NULL,
  `processed` datetime NOT NULL DEFAULT NULL,
  `stats` text DEFAULT NULL
);

--
-- Table: `#__project_todo`
--

CREATE TABLE `#__project_todo` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `projectid` INTEGER NOT NULL DEFAULT 0,
  `todolist` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `duedate` datetime DEFAULT NULL,
  `closed` datetime DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `assigned_to` INTEGER DEFAULT 0,
  `closed_by` INTEGER DEFAULT 0,
  `priority` INTEGER DEFAULT 0,
  `activityid` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0,
  `milestone` INTEGER NOT NULL DEFAULT 0,
  `private` INTEGER NOT NULL DEFAULT 0,
  `details` text DEFAULT NULL,
  `content` varchar(255) NOT NULL,
  `color` varchar(20) DEFAULT NULL
);

--
-- Table: `#__project_types`
--

CREATE TABLE `#__project_types` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `type` varchar(150) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `params` text DEFAULT NULL
);

--
-- Table: `#__projects`
--

CREATE TABLE `#__projects` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `alias` varchar(30) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `picture` varchar(255) DEFAULT '',
  `about` text DEFAULT NULL,
  `state` INTEGER NOT NULL DEFAULT 0,
  `type` INTEGER NOT NULL DEFAULT 1,
  `provisioned` INTEGER NOT NULL DEFAULT 0,
  `private` INTEGER NOT NULL DEFAULT 1,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `owned_by_user` INTEGER NOT NULL DEFAULT 0,
  `created_by_user` INTEGER NOT NULL,
  `owned_by_group` INTEGER DEFAULT 0,
  `modified_by` INTEGER DEFAULT 0,
  `setup_stage` INTEGER NOT NULL DEFAULT 0,
  `params` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_alias` ON `#__projects` (`alias`);

--
-- Table: `#__publication_access`
--

CREATE TABLE `#__publication_access` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `publication_version_id` INTEGER NOT NULL DEFAULT 0,
  `group_id` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__publication_attachments`
--

CREATE TABLE `#__publication_attachments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `publication_version_id` INTEGER NOT NULL DEFAULT 0,
  `publication_id` INTEGER NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified_by` INTEGER DEFAULT 0,
  `object_id` INTEGER DEFAULT 0,
  `object_name` varchar(64) DEFAULT '0',
  `object_instance` INTEGER DEFAULT 0,
  `object_revision` INTEGER DEFAULT 0,
  `role` INTEGER DEFAULT 0,
  `path` varchar(255) NOT NULL,
  `vcs_hash` varchar(255) DEFAULT NULL,
  `vcs_revision` varchar(255) DEFAULT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'file',
  `params` text DEFAULT NULL,
  `attribs` text DEFAULT NULL,
  `ordering` INTEGER DEFAULT 0,
  `content_hash` varchar(255) DEFAULT NULL,
  `element_id` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__publication_audience`
--

CREATE TABLE `#__publication_audience` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `publication_id` INTEGER NOT NULL DEFAULT 0,
  `publication_version_id` INTEGER DEFAULT 0,
  `level0` INTEGER NOT NULL DEFAULT 0,
  `level1` INTEGER NOT NULL DEFAULT 0,
  `level2` INTEGER NOT NULL DEFAULT 0,
  `level3` INTEGER NOT NULL DEFAULT 0,
  `level4` INTEGER NOT NULL DEFAULT 0,
  `level5` INTEGER NOT NULL DEFAULT 0,
  `comments` varchar(255) DEFAULT '',
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__publication_audience_levels`
--

CREATE TABLE `#__publication_audience_levels` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `label` varchar(11) NOT NULL DEFAULT '0',
  `title` varchar(100) DEFAULT '',
  `description` varchar(255) DEFAULT ''
);

--
-- Table: `#__publication_authors`
--

CREATE TABLE `#__publication_authors` (
  `publication_version_id` INTEGER NOT NULL DEFAULT 0,
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `project_owner_id` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `firstName` varchar(255) DEFAULT NULL,
  `lastName` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `credit` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified_by` INTEGER DEFAULT 0,
  `status` INTEGER NOT NULL DEFAULT 1
);

--
-- Table: `#__publication_blocks`
--

CREATE TABLE `#__publication_blocks` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `block` varchar(100) NOT NULL DEFAULT '',
  `label` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` INTEGER NOT NULL DEFAULT 0,
  `minimum` INTEGER NOT NULL DEFAULT 0,
  `maximum` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `params` text DEFAULT NULL,
  `manifest` text DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `block` ON `#__publication_blocks` (`block`);

--
-- Table: `#__publication_categories`
--

CREATE TABLE `#__publication_categories` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `dc_type` varchar(200) NOT NULL DEFAULT 'Dataset',
  `alias` varchar(200) NOT NULL DEFAULT '',
  `url_alias` varchar(200) NOT NULL DEFAULT '',
  `description` tinytext DEFAULT NULL,
  `contributable` INTEGER DEFAULT 1,
  `state` INTEGER DEFAULT 1,
  `customFields` text DEFAULT NULL,
  `params` text DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_name` ON `#__publication_categories` (`name`);
CREATE UNIQUE INDEX IF NOT EXISTS `uidx_alias` ON `#__publication_categories` (`alias`);
CREATE UNIQUE INDEX IF NOT EXISTS `uidx_url_alias` ON `#__publication_categories` (`url_alias`);

--
-- Table: `#__publication_curation`
--

CREATE TABLE `#__publication_curation` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `publication_id` INTEGER NOT NULL DEFAULT 0,
  `publication_version_id` INTEGER NOT NULL DEFAULT 0,
  `updated` datetime DEFAULT NULL,
  `updated_by` INTEGER DEFAULT 0,
  `update` text DEFAULT NULL,
  `reviewed` datetime DEFAULT NULL,
  `reviewed_by` INTEGER DEFAULT 0,
  `review` text DEFAULT NULL,
  `review_status` INTEGER NOT NULL DEFAULT 0,
  `block` varchar(100) NOT NULL DEFAULT '',
  `step` INTEGER DEFAULT 0,
  `element` INTEGER DEFAULT 0
);

--
-- Table: `#__publication_curation_history`
--

CREATE TABLE `#__publication_curation_history` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `publication_version_id` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `changelog` text NOT NULL,
  `curator` INTEGER NOT NULL DEFAULT 0,
  `oldstatus` INTEGER NOT NULL DEFAULT 0,
  `newstatus` INTEGER NOT NULL DEFAULT 0,
  `comment` text DEFAULT NULL
);

--
-- Table: `#__publication_handlers`
--

CREATE TABLE `#__publication_handlers` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `label` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` INTEGER NOT NULL DEFAULT 0,
  `about` text DEFAULT NULL,
  `params` text DEFAULT NULL
);

--
-- Table: `#__publication_licenses`
--

CREATE TABLE `#__publication_licenses` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(100) NOT NULL,
  `text` text DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `info` text DEFAULT NULL,
  `ordering` INTEGER DEFAULT NULL,
  `active` INTEGER NOT NULL DEFAULT 0,
  `apps_only` INTEGER NOT NULL DEFAULT 0,
  `main` INTEGER NOT NULL DEFAULT 0,
  `agreement` INTEGER DEFAULT 0,
  `customizable` INTEGER DEFAULT 0,
  `icon` varchar(250) DEFAULT NULL,
  `opensource` INTEGER NOT NULL DEFAULT 0,
  `restriction` varchar(100) DEFAULT NULL
);

--
-- Table: `#__publication_logs`
--

CREATE TABLE `#__publication_logs` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `publication_id` INTEGER NOT NULL,
  `publication_version_id` INTEGER NOT NULL,
  `month` INTEGER NOT NULL,
  `year` INTEGER NOT NULL,
  `modified` datetime NOT NULL DEFAULT NULL,
  `page_views` INTEGER DEFAULT 0,
  `primary_accesses` INTEGER DEFAULT 0,
  `support_accesses` INTEGER DEFAULT 0,
  `page_views_unfiltered` INTEGER DEFAULT NULL,
  `primary_accesses_unfiltered` INTEGER DEFAULT NULL,
  `page_views_unique` INTEGER DEFAULT NULL,
  `primary_accesses_unique` INTEGER DEFAULT NULL
);

--
-- Table: `#__publication_master_types`
--

CREATE TABLE `#__publication_master_types` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `type` varchar(200) NOT NULL DEFAULT '',
  `alias` varchar(200) NOT NULL DEFAULT '',
  `description` tinytext DEFAULT NULL,
  `contributable` INTEGER DEFAULT 0,
  `supporting` INTEGER DEFAULT 0,
  `ordering` INTEGER DEFAULT 0,
  `params` text DEFAULT NULL,
  `curation` text DEFAULT NULL,
  `curatorgroup` INTEGER DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_alias` ON `#__publication_master_types` (`alias`);

--
-- Table: `#__publication_ratings`
--

CREATE TABLE `#__publication_ratings` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `publication_id` INTEGER NOT NULL DEFAULT 0,
  `publication_version_id` INTEGER NOT NULL DEFAULT 0,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `comment` text NOT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `anonymous` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__publication_screenshots`
--

CREATE TABLE `#__publication_screenshots` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `publication_version_id` INTEGER NOT NULL DEFAULT 0,
  `publication_id` INTEGER NOT NULL DEFAULT 0,
  `title` varchar(127) DEFAULT '',
  `ordering` INTEGER DEFAULT 0,
  `filename` varchar(100) NOT NULL,
  `srcfile` varchar(100) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `created_by` varchar(127) DEFAULT NULL,
  `modified_by` varchar(127) DEFAULT NULL
);

--
-- Table: `#__publication_stats`
--

CREATE TABLE `#__publication_stats` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `publication_id` INTEGER NOT NULL,
  `publication_version` INTEGER DEFAULT NULL,
  `users` INTEGER DEFAULT NULL,
  `downloads` INTEGER DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT NULL,
  `period` INTEGER NOT NULL DEFAULT -1,
  `processed_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_publication_id_datetime_period` ON `#__publication_stats` (`publication_id`,`datetime`,`period`);

--
-- Table: `#__publication_versions`
--

CREATE TABLE `#__publication_versions` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `publication_id` INTEGER NOT NULL DEFAULT 0,
  `main` INTEGER NOT NULL DEFAULT 0,
  `doi` varchar(255) DEFAULT '',
  `ark` varchar(255) DEFAULT '',
  `state` INTEGER NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `abstract` text NOT NULL,
  `metadata` text DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `published_up` datetime DEFAULT NULL,
  `published_down` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `accepted` datetime DEFAULT NULL,
  `archived` datetime NOT NULL DEFAULT NULL,
  `submitted` datetime DEFAULT NULL,
  `modified_by` INTEGER DEFAULT 0,
  `version_label` varchar(100) NOT NULL DEFAULT '1.0',
  `secret` varchar(10) NOT NULL DEFAULT '',
  `version_number` INTEGER NOT NULL DEFAULT 0,
  `params` text DEFAULT NULL,
  `release_notes` text DEFAULT NULL,
  `license_text` text DEFAULT NULL,
  `license_type` INTEGER DEFAULT NULL,
  `access` INTEGER NOT NULL DEFAULT 0,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `times_rated` INTEGER NOT NULL DEFAULT 0,
  `ranking` float NOT NULL DEFAULT 0,
  `curation` text DEFAULT NULL,
  `reviewed` datetime DEFAULT NULL,
  `reviewed_by` INTEGER DEFAULT NULL,
  `curator` INTEGER DEFAULT NULL
);

--
-- Table: `#__publications`
--

CREATE TABLE `#__publications` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `category` INTEGER NOT NULL DEFAULT 0,
  `master_type` INTEGER NOT NULL DEFAULT 1,
  `project_id` INTEGER NOT NULL DEFAULT 0,
  `access` INTEGER NOT NULL DEFAULT 0,
  `checked_out` INTEGER NOT NULL DEFAULT 0,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `times_rated` INTEGER NOT NULL DEFAULT 0,
  `alias` varchar(100) NOT NULL DEFAULT '',
  `ranking` float NOT NULL DEFAULT 0,
  `group_owner` INTEGER NOT NULL DEFAULT 0,
  `master_doi` varchar(255) DEFAULT ''
);

--
-- Table: `#__recent_tools`
--

CREATE TABLE `#__recent_tools` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `tool` varchar(200) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT NULL
);

--
-- Table: `#__recommendation`
--

CREATE TABLE `#__recommendation` (
  `fromID` INTEGER NOT NULL,
  `toID` INTEGER NOT NULL,
  `contentScore` float zerofill DEFAULT NULL,
  `tagScore` float zerofill DEFAULT NULL,
  `titleScore` float zerofill DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`fromID`,`toID`)
);

--
-- Table: `#__redirect_links`
--

CREATE TABLE `#__redirect_links` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `old_url` varchar(255) NOT NULL,
  `new_url` varchar(255) NOT NULL,
  `referer` varchar(150) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `hits` INTEGER NOT NULL DEFAULT 0,
  `published` INTEGER NOT NULL,
  `created_date` datetime NOT NULL DEFAULT NULL,
  `modified_date` datetime NOT NULL DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_link_old` ON `#__redirect_links` (`old_url`);
CREATE INDEX IF NOT EXISTS `idx_link_modifed` ON `#__redirect_links` (`modified_date`);

--
-- Table: `#__redirection`
--

CREATE TABLE `#__redirection` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `cpt` INTEGER NOT NULL DEFAULT 0,
  `oldurl` varchar(100) NOT NULL DEFAULT '',
  `newurl` varchar(150) NOT NULL DEFAULT '',
  `dateadd` date NOT NULL DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_newurl` ON `#__redirection` (`newurl`);

--
-- Table: `#__resource_assoc`
--

CREATE TABLE `#__resource_assoc` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `parent_id` INTEGER NOT NULL DEFAULT 0,
  `child_id` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `grouping` INTEGER NOT NULL DEFAULT 0
);

CREATE UNIQUE INDEX IF NOT EXISTS `id` ON `#__resource_assoc` (`id`);
CREATE INDEX IF NOT EXISTS `idx_parent_id_child_id` ON `#__resource_assoc` (`parent_id`,`child_id`);

--
-- Table: `#__resource_import_hooks`
--

CREATE TABLE `#__resource_import_hooks` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `type` varchar(25) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `file` varchar(100) DEFAULT NULL,
  `state` INTEGER DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER DEFAULT NULL
);

--
-- Table: `#__resource_import_runs`
--

CREATE TABLE `#__resource_import_runs` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `import_id` INTEGER DEFAULT NULL,
  `processed` INTEGER DEFAULT NULL,
  `count` INTEGER DEFAULT NULL,
  `ran_by` INTEGER DEFAULT NULL,
  `ran_at` datetime DEFAULT NULL,
  `dry_run` INTEGER DEFAULT 0
);

--
-- Table: `#__resource_imports`
--

CREATE TABLE `#__resource_imports` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `file` varchar(255) DEFAULT '',
  `count` INTEGER DEFAULT NULL,
  `created_by` INTEGER DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `state` INTEGER DEFAULT 1,
  `mode` varchar(10) DEFAULT 'UPDATE',
  `params` text DEFAULT NULL,
  `hooks` text DEFAULT NULL
);

--
-- Table: `#__resource_licenses`
--

CREATE TABLE `#__resource_licenses` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `apps_only` INTEGER NOT NULL DEFAULT 0,
  `main` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `agreement` INTEGER NOT NULL DEFAULT 0,
  `info` text DEFAULT NULL
);

--
-- Table: `#__resource_ratings`
--

CREATE TABLE `#__resource_ratings` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `resource_id` INTEGER NOT NULL DEFAULT 0,
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `comment` text NOT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `anonymous` INTEGER NOT NULL DEFAULT 0,
  `state` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__resource_sponsors`
--

CREATE TABLE `#__resource_sponsors` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `alias` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `state` INTEGER NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL
);

--
-- Table: `#__resource_stats`
--

CREATE TABLE `#__resource_stats` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `resid` INTEGER NOT NULL,
  `restype` INTEGER DEFAULT NULL,
  `users` INTEGER DEFAULT NULL,
  `jobs` INTEGER DEFAULT NULL,
  `avg_wall` INTEGER DEFAULT NULL,
  `tot_wall` INTEGER DEFAULT NULL,
  `avg_cpu` INTEGER DEFAULT NULL,
  `tot_cpu` INTEGER DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT NULL,
  `period` INTEGER NOT NULL DEFAULT -1,
  `processed_on` datetime NOT NULL DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_resid_restype_datetime_period` ON `#__resource_stats` (`resid`,`restype`,`datetime`,`period`);

--
-- Table: `#__resource_stats_clusters`
--

CREATE TABLE `#__resource_stats_clusters` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `cluster` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(32) NOT NULL DEFAULT '',
  `uidNumber` INTEGER NOT NULL DEFAULT 0,
  `toolname` varchar(80) NOT NULL DEFAULT '',
  `resid` INTEGER NOT NULL DEFAULT 0,
  `clustersize` varchar(255) NOT NULL DEFAULT '',
  `cluster_start` datetime NOT NULL DEFAULT NULL,
  `cluster_end` datetime NOT NULL DEFAULT NULL,
  `institution` varchar(255) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS `idx_cluster` ON `#__resource_stats_clusters` (`cluster`);
CREATE INDEX IF NOT EXISTS `idx_username` ON `#__resource_stats_clusters` (`username`);
CREATE INDEX IF NOT EXISTS `idx_uidNumber` ON `#__resource_stats_clusters` (`uidNumber`);
CREATE INDEX IF NOT EXISTS `idx_toolname` ON `#__resource_stats_clusters` (`toolname`);
CREATE INDEX IF NOT EXISTS `idx_resid` ON `#__resource_stats_clusters` (`resid`);
CREATE INDEX IF NOT EXISTS `idx_clustersize` ON `#__resource_stats_clusters` (`clustersize`);
CREATE INDEX IF NOT EXISTS `idx_cluster_start` ON `#__resource_stats_clusters` (`cluster_start`);
CREATE INDEX IF NOT EXISTS `idx_cluster_end` ON `#__resource_stats_clusters` (`cluster_end`);
CREATE INDEX IF NOT EXISTS `idx_institution` ON `#__resource_stats_clusters` (`institution`);

--
-- Table: `#__resource_stats_tools`
--

CREATE TABLE `#__resource_stats_tools` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `resid` INTEGER NOT NULL,
  `restype` INTEGER NOT NULL,
  `users` INTEGER DEFAULT NULL,
  `sessions` INTEGER DEFAULT NULL,
  `simulations` INTEGER DEFAULT NULL,
  `jobs` INTEGER DEFAULT NULL,
  `avg_wall` double DEFAULT 0,
  `tot_wall` double DEFAULT 0,
  `avg_cpu` double DEFAULT 0,
  `tot_cpu` double DEFAULT 0,
  `avg_view` double DEFAULT 0,
  `tot_view` double DEFAULT 0,
  `avg_wait` double DEFAULT 0,
  `tot_wait` double DEFAULT 0,
  `avg_cpus` INTEGER DEFAULT NULL,
  `tot_cpus` INTEGER DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT NULL,
  `period` INTEGER NOT NULL DEFAULT -1,
  `processed_on` datetime NOT NULL DEFAULT NULL
);

--
-- Table: `#__resource_stats_tools_tops`
--

CREATE TABLE `#__resource_stats_tools_tops` (
  `top` INTEGER NOT NULL DEFAULT 0,
  `name` varchar(128) NOT NULL DEFAULT '',
  `valfmt` INTEGER NOT NULL DEFAULT 0,
  `size` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`top`)
);

--
-- Table: `#__resource_stats_tools_topvals`
--

CREATE TABLE `#__resource_stats_tools_topvals` (
  `id` INTEGER NOT NULL,
  `top` INTEGER NOT NULL DEFAULT 0,
  `rank` INTEGER NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `value` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__resource_stats_tools_users`
--

CREATE TABLE `#__resource_stats_tools_users` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `resid` INTEGER NOT NULL,
  `restype` INTEGER NOT NULL,
  `user` varchar(32) NOT NULL DEFAULT '',
  `sessions` INTEGER DEFAULT NULL,
  `simulations` INTEGER DEFAULT NULL,
  `jobs` INTEGER DEFAULT NULL,
  `tot_wall` double DEFAULT 0,
  `tot_cpu` double DEFAULT 0,
  `tot_view` double DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT NULL,
  `period` INTEGER NOT NULL DEFAULT -1
);

--
-- Table: `#__resource_taxonomy_audience`
--

CREATE TABLE `#__resource_taxonomy_audience` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `rid` INTEGER NOT NULL DEFAULT 0,
  `versionid` INTEGER DEFAULT 0,
  `level0` INTEGER NOT NULL DEFAULT 0,
  `level1` INTEGER NOT NULL DEFAULT 0,
  `level2` INTEGER NOT NULL DEFAULT 0,
  `level3` INTEGER NOT NULL DEFAULT 0,
  `level4` INTEGER NOT NULL DEFAULT 0,
  `level5` INTEGER NOT NULL DEFAULT 0,
  `comments` varchar(255) DEFAULT '',
  `added` datetime NOT NULL DEFAULT NULL,
  `addedBy` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__resource_taxonomy_audience_levels`
--

CREATE TABLE `#__resource_taxonomy_audience_levels` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `label` varchar(11) NOT NULL DEFAULT '0',
  `title` varchar(100) DEFAULT '',
  `description` varchar(255) DEFAULT ''
);

--
-- Table: `#__resource_types`
--

CREATE TABLE `#__resource_types` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `alias` varchar(100) DEFAULT NULL,
  `type` varchar(200) NOT NULL DEFAULT '',
  `category` INTEGER NOT NULL DEFAULT 0,
  `description` tinytext DEFAULT NULL,
  `contributable` INTEGER DEFAULT 1,
  `customFields` text DEFAULT NULL,
  `params` text DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_category` ON `#__resource_types` (`category`);

--
-- Table: `#__resources`
--

CREATE TABLE `#__resources` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `type` INTEGER NOT NULL DEFAULT 0,
  `logical_type` INTEGER NOT NULL DEFAULT 0,
  `introtext` text NOT NULL,
  `fulltxt` text NOT NULL,
  `footertext` text NOT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0,
  `published` INTEGER NOT NULL DEFAULT 0,
  `publish_up` datetime NOT NULL DEFAULT NULL,
  `publish_down` datetime NOT NULL DEFAULT NULL,
  `access` INTEGER NOT NULL DEFAULT 0,
  `hits` INTEGER NOT NULL DEFAULT 0,
  `path` varchar(200) NOT NULL DEFAULT '',
  `checked_out` INTEGER NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT NULL,
  `standalone` INTEGER NOT NULL DEFAULT 0,
  `group_owner` varchar(250) NOT NULL DEFAULT '',
  `group_access` text DEFAULT NULL,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `times_rated` INTEGER NOT NULL DEFAULT 0,
  `params` text DEFAULT NULL,
  `attribs` text DEFAULT NULL,
  `alias` varchar(100) NOT NULL DEFAULT '',
  `ranking` float NOT NULL DEFAULT 0
);

--
-- Table: `#__schemas`
--

CREATE TABLE `#__schemas` (
  `extension_id` INTEGER NOT NULL,
  `version_id` varchar(20) NOT NULL,
  PRIMARY KEY (`extension_id`,`version_id`)
);

--
-- Table: `#__screenshots`
--

CREATE TABLE `#__screenshots` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `versionid` INTEGER DEFAULT 0,
  `title` varchar(127) DEFAULT '',
  `ordering` INTEGER DEFAULT 0,
  `filename` varchar(100) NOT NULL,
  `resourceid` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__session`
--

CREATE TABLE `#__session` (
  `session_id` varchar(200) NOT NULL DEFAULT '',
  `client_id` INTEGER NOT NULL DEFAULT 0,
  `guest` INTEGER DEFAULT 1,
  `time` varchar(14) DEFAULT '',
  `data` mediumtext DEFAULT NULL,
  `userid` INTEGER DEFAULT 0,
  `username` varchar(150) DEFAULT '',
  `usertype` varchar(50) DEFAULT '',
  `ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`session_id`)
);

CREATE INDEX IF NOT EXISTS `whosonline` ON `#__session` (`guest`,`usertype`);
CREATE INDEX IF NOT EXISTS `userid` ON `#__session` (`userid`);
CREATE INDEX IF NOT EXISTS `time` ON `#__session` (`time`);

--
-- Table: `#__session_geo`
--

CREATE TABLE `#__session_geo` (
  `session_id` varchar(200) NOT NULL DEFAULT '0',
  `username` varchar(150) DEFAULT '',
  `time` varchar(14) DEFAULT '',
  `guest` INTEGER DEFAULT 1,
  `userid` INTEGER DEFAULT 0,
  `ip` varchar(15) DEFAULT NULL,
  `host` varchar(128) DEFAULT NULL,
  `domain` varchar(128) DEFAULT NULL,
  `signed` INTEGER DEFAULT 0,
  `countrySHORT` char(2) DEFAULT NULL,
  `countryLONG` varchar(64) DEFAULT NULL,
  `ipREGION` varchar(128) DEFAULT NULL,
  `ipCITY` varchar(128) DEFAULT NULL,
  `ipLATITUDE` double DEFAULT NULL,
  `ipLONGITUDE` double DEFAULT NULL,
  `bot` INTEGER DEFAULT 0,
  PRIMARY KEY (`session_id`)
);

CREATE INDEX IF NOT EXISTS `idx_userid` ON `#__session_geo` (`userid`);
CREATE INDEX IF NOT EXISTS `idx_time` ON `#__session_geo` (`time`);
CREATE INDEX IF NOT EXISTS `idx_ip` ON `#__session_geo` (`ip`);

--
-- Table: `#__session_log`
--

CREATE TABLE `#__session_log` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `clientid` INTEGER DEFAULT NULL,
  `session_id` char(64) DEFAULT NULL,
  `psid` char(64) DEFAULT NULL,
  `rsid` char(64) DEFAULT NULL,
  `ssid` char(64) DEFAULT NULL,
  `user_id` INTEGER DEFAULT NULL,
  `authenticator` char(64) DEFAULT NULL,
  `source` char(64) DEFAULT NULL,
  `ip` char(64) DEFAULT NULL,
  `created` datetime DEFAULT NULL
);

--
-- Table: `#__stats_tops`
--

CREATE TABLE `#__stats_tops` (
  `id` INTEGER NOT NULL DEFAULT 0,
  `name` varchar(128) NOT NULL DEFAULT '',
  `valfmt` INTEGER NOT NULL DEFAULT 0,
  `size` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);

--
-- Table: `#__stats_topvals`
--

CREATE TABLE `#__stats_topvals` (
  `top` INTEGER NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT NULL,
  `period` INTEGER NOT NULL DEFAULT 1,
  `rank` INTEGER NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `value` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_top` ON `#__stats_topvals` (`top`);
CREATE INDEX IF NOT EXISTS `idx_top_rank` ON `#__stats_topvals` (`top`,`rank`);
CREATE INDEX IF NOT EXISTS `idx_top_datetime` ON `#__stats_topvals` (`top`,`datetime`);
CREATE INDEX IF NOT EXISTS `idx_top_datetime_rank` ON `#__stats_topvals` (`top`,`datetime`,`rank`);
CREATE INDEX IF NOT EXISTS `idx_top_datetime_period` ON `#__stats_topvals` (`top`,`datetime`,`period`);

--
-- Table: `#__store`
--

CREATE TABLE `#__store` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(127) NOT NULL DEFAULT '',
  `price` INTEGER NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `published` INTEGER NOT NULL DEFAULT 0,
  `featured` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `available` INTEGER NOT NULL DEFAULT 0,
  `params` text DEFAULT NULL,
  `special` INTEGER DEFAULT 0,
  `type` INTEGER DEFAULT 1,
  `category` varchar(127) DEFAULT ''
);

--
-- Table: `#__storefront_collections`
--

CREATE TABLE `#__storefront_collections` (
  `cId` char(50) NOT NULL,
  `cName` varchar(64) DEFAULT NULL,
  `cParent` INTEGER DEFAULT NULL,
  `cActive` INTEGER DEFAULT NULL,
  `cType` char(10) DEFAULT NULL,
  PRIMARY KEY (`cId`)
);

CREATE INDEX IF NOT EXISTS `idx_cActive` ON `#__storefront_collections` (`cActive`);
CREATE INDEX IF NOT EXISTS `idx_cParent` ON `#__storefront_collections` (`cParent`);

--
-- Table: `#__storefront_coupon_actions`
--

CREATE TABLE `#__storefront_coupon_actions` (
  `cnId` INTEGER NOT NULL,
  `cnaAction` char(25) DEFAULT NULL,
  `cnaVal` char(255) DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_cnId_cnaAction` ON `#__storefront_coupon_actions` (`cnId`,`cnaAction`);

--
-- Table: `#__storefront_coupon_conditions`
--

CREATE TABLE `#__storefront_coupon_conditions` (
  `cnId` INTEGER NOT NULL,
  `cncRule` char(100) DEFAULT NULL,
  `cncVal` char(255) DEFAULT NULL
);

--
-- Table: `#__storefront_coupon_objects`
--

CREATE TABLE `#__storefront_coupon_objects` (
  `cnId` INTEGER NOT NULL,
  `cnoObjectId` INTEGER DEFAULT NULL,
  `cnoObjectsLimit` INTEGER DEFAULT 0
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_cnId_cnoObjectId` ON `#__storefront_coupon_objects` (`cnId`,`cnoObjectId`);

--
-- Table: `#__storefront_coupons`
--

CREATE TABLE `#__storefront_coupons` (
  `cnId` INTEGER PRIMARY KEY AUTOINCREMENT,
  `cnCode` char(25) DEFAULT NULL,
  `cnDescription` char(255) DEFAULT NULL,
  `cnExpires` date DEFAULT NULL,
  `cnUseLimit` INTEGER DEFAULT NULL,
  `cnObject` char(15) NOT NULL,
  `cnActive` INTEGER DEFAULT 1
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_cnCode` ON `#__storefront_coupons` (`cnCode`);

--
-- Table: `#__storefront_option_groups`
--

CREATE TABLE `#__storefront_option_groups` (
  `ogId` INTEGER PRIMARY KEY AUTOINCREMENT,
  `ogName` char(16) DEFAULT NULL
);

--
-- Table: `#__storefront_options`
--

CREATE TABLE `#__storefront_options` (
  `oId` INTEGER PRIMARY KEY AUTOINCREMENT,
  `ogId` INTEGER DEFAULT NULL,
  `oName` char(255) DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_ogId_oName` ON `#__storefront_options` (`ogId`,`oName`);

--
-- Table: `#__storefront_product_collections`
--

CREATE TABLE `#__storefront_product_collections` (
  `cllId` INTEGER NOT NULL,
  `pId` INTEGER NOT NULL,
  `cId` char(50) NOT NULL,
  PRIMARY KEY (`cllId`,`pId`,`cId`)
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_pId_cId` ON `#__storefront_product_collections` (`pId`,`cId`);

--
-- Table: `#__storefront_product_option_groups`
--

CREATE TABLE `#__storefront_product_option_groups` (
  `pId` INTEGER NOT NULL,
  `ogId` INTEGER NOT NULL,
  PRIMARY KEY (`pId`,`ogId`)
);

--
-- Table: `#__storefront_product_types`
--

CREATE TABLE `#__storefront_product_types` (
  `ptId` INTEGER PRIMARY KEY AUTOINCREMENT,
  `ptName` char(128) DEFAULT NULL,
  `ptModel` char(25) DEFAULT 'normal'
);

--
-- Table: `#__storefront_products`
--

CREATE TABLE `#__storefront_products` (
  `pId` INTEGER PRIMARY KEY AUTOINCREMENT,
  `ptId` INTEGER NOT NULL,
  `pName` char(128) DEFAULT NULL,
  `pTagline` tinytext DEFAULT NULL,
  `pDescription` text DEFAULT NULL,
  `pFeatures` text DEFAULT NULL,
  `pActive` INTEGER DEFAULT 1
);

CREATE INDEX IF NOT EXISTS `idx_pActive` ON `#__storefront_products` (`pActive`);

--
-- Table: `#__storefront_sku_meta`
--

CREATE TABLE `#__storefront_sku_meta` (
  `smId` INTEGER PRIMARY KEY AUTOINCREMENT,
  `sId` INTEGER NOT NULL,
  `smKey` varchar(100) DEFAULT NULL,
  `smValue` varchar(100) DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_sId_smKey` ON `#__storefront_sku_meta` (`sId`,`smKey`);

--
-- Table: `#__storefront_sku_options`
--

CREATE TABLE `#__storefront_sku_options` (
  `sId` INTEGER NOT NULL,
  `oId` INTEGER NOT NULL,
  PRIMARY KEY (`sId`,`oId`)
);

--
-- Table: `#__storefront_skus`
--

CREATE TABLE `#__storefront_skus` (
  `sId` INTEGER PRIMARY KEY AUTOINCREMENT,
  `pId` INTEGER DEFAULT NULL,
  `sSku` char(16) DEFAULT NULL,
  `sWeight` decimal(10,2) DEFAULT NULL,
  `sPrice` decimal(10,2) DEFAULT NULL,
  `sDescriprtion` text DEFAULT NULL,
  `sFeatures` text DEFAULT NULL,
  `sTrackInventory` INTEGER DEFAULT 0,
  `sInventory` INTEGER DEFAULT 0,
  `sEnumerable` INTEGER DEFAULT 1,
  `sAllowMultiple` INTEGER DEFAULT 1,
  `sActive` INTEGER DEFAULT 1
);

--
-- Table: `#__support_acl_acos`
--

CREATE TABLE `#__support_acl_acos` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `model` varchar(100) NOT NULL DEFAULT '',
  `foreign_key` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__support_acl_aros`
--

CREATE TABLE `#__support_acl_aros` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `model` varchar(100) NOT NULL DEFAULT '',
  `foreign_key` INTEGER NOT NULL DEFAULT 0,
  `alias` varchar(255) NOT NULL DEFAULT ''
);

CREATE INDEX IF NOT EXISTS `idx_model_foreign_key` ON `#__support_acl_aros` (`model`,`foreign_key`);

--
-- Table: `#__support_acl_aros_acos`
--

CREATE TABLE `#__support_acl_aros_acos` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `aro_id` INTEGER NOT NULL DEFAULT 0,
  `aco_id` INTEGER NOT NULL DEFAULT 0,
  `action_create` INTEGER NOT NULL DEFAULT 0,
  `action_read` INTEGER NOT NULL DEFAULT 0,
  `action_update` INTEGER NOT NULL DEFAULT 0,
  `action_delete` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_aco_id` ON `#__support_acl_aros_acos` (`aco_id`);
CREATE INDEX IF NOT EXISTS `idx_aro_id` ON `#__support_acl_aros_acos` (`aro_id`);

--
-- Table: `#__support_attachments`
--

CREATE TABLE `#__support_attachments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `ticket` INTEGER NOT NULL DEFAULT 0,
  `filename` varchar(255) DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `comment_id` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_ticket` ON `#__support_attachments` (`ticket`);

--
-- Table: `#__support_categories`
--

CREATE TABLE `#__support_categories` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `section_id` INTEGER NOT NULL DEFAULT 0,
  `alias` varchar(250) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__support_comments`
--

CREATE TABLE `#__support_comments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `ticket` INTEGER NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `changelog` text NOT NULL,
  `access` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_ticket` ON `#__support_comments` (`ticket`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__support_comments` (`created_by`);

--
-- Table: `#__support_messages`
--

CREATE TABLE `#__support_messages` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `message` text NOT NULL
);

--
-- Table: `#__support_queries`
--

CREATE TABLE `#__support_queries` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `conditions` text NOT NULL,
  `query` text NOT NULL,
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `sort` varchar(100) NOT NULL DEFAULT '',
  `sort_dir` varchar(100) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT NULL,
  `iscore` INTEGER NOT NULL DEFAULT 0,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `folder_id` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_user_id` ON `#__support_queries` (`user_id`);
CREATE INDEX IF NOT EXISTS `idx_iscore` ON `#__support_queries` (`iscore`);

--
-- Table: `#__support_query_folders`
--

CREATE TABLE `#__support_query_folders` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `title` varchar(200) NOT NULL DEFAULT '',
  `alias` varchar(200) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `iscore` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `#__support_resolutions`
--

CREATE TABLE `#__support_resolutions` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `alias` varchar(100) NOT NULL DEFAULT ''
);

--
-- Table: `#__support_sections`
--

CREATE TABLE `#__support_sections` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `section` varchar(50) DEFAULT NULL
);

--
-- Table: `#__support_statuses`
--

CREATE TABLE `#__support_statuses` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `open` INTEGER NOT NULL DEFAULT 0,
  `title` varchar(250) NOT NULL DEFAULT '',
  `alias` varchar(250) NOT NULL DEFAULT '',
  `color` varchar(50) NOT NULL DEFAULT ''
);

CREATE INDEX IF NOT EXISTS `idx_open` ON `#__support_statuses` (`open`);

--
-- Table: `#__support_tickets`
--

CREATE TABLE `#__support_tickets` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `status` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `closed` datetime NOT NULL DEFAULT NULL,
  `login` varchar(200) NOT NULL DEFAULT '',
  `severity` varchar(30) NOT NULL DEFAULT '',
  `owner` INTEGER NOT NULL DEFAULT 0,
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
  `cookies` INTEGER NOT NULL DEFAULT 0,
  `instances` INTEGER NOT NULL DEFAULT 1,
  `section` INTEGER NOT NULL DEFAULT 1,
  `type` INTEGER NOT NULL DEFAULT 0,
  `group` varchar(250) NOT NULL DEFAULT '',
  `open` INTEGER NOT NULL DEFAULT 1
);

CREATE INDEX IF NOT EXISTS `idx_owner` ON `#__support_tickets` (`owner`);

--
-- Table: `#__support_watching`
--

CREATE TABLE `#__support_watching` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `ticket_id` INTEGER NOT NULL DEFAULT 0,
  `user_id` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_ticket_id` ON `#__support_watching` (`ticket_id`);
CREATE INDEX IF NOT EXISTS `idx_user_id` ON `#__support_watching` (`user_id`);

--
-- Table: `#__tags`
--

CREATE TABLE `#__tags` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `tag` varchar(100) NOT NULL DEFAULT '',
  `raw_tag` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `admin` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `modified_by` INTEGER NOT NULL DEFAULT 0
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_tag` ON `#__tags` (`tag`);

--
-- Table: `#__tags_log`
--

CREATE TABLE `#__tags_log` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `tag_id` INTEGER NOT NULL DEFAULT 0,
  `timestamp` datetime NOT NULL DEFAULT NULL,
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `action` varchar(50) NOT NULL DEFAULT '',
  `comments` text NOT NULL,
  `actorid` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_tag_id` ON `#__tags_log` (`tag_id`);
CREATE INDEX IF NOT EXISTS `idx_user_id` ON `#__tags_log` (`user_id`);

--
-- Table: `#__tags_object`
--

CREATE TABLE `#__tags_object` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `objectid` INTEGER NOT NULL DEFAULT 0,
  `tagid` INTEGER NOT NULL DEFAULT 0,
  `strength` INTEGER NOT NULL DEFAULT 0,
  `taggerid` INTEGER NOT NULL DEFAULT 0,
  `taggedon` datetime NOT NULL DEFAULT NULL,
  `tbl` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(30) NOT NULL DEFAULT ''
);

CREATE INDEX IF NOT EXISTS `idx_objectid_tbl` ON `#__tags_object` (`objectid`,`tbl`);
CREATE INDEX IF NOT EXISTS `idx_label_tagid` ON `#__tags_object` (`label`,`tagid`);
CREATE INDEX IF NOT EXISTS `idx_tbl_objectid_label_tagid` ON `#__tags_object` (`tbl`,`objectid`,`label`,`tagid`);
CREATE INDEX IF NOT EXISTS `idx_tagid` ON `#__tags_object` (`tagid`);

--
-- Table: `#__tags_substitute`
--

CREATE TABLE `#__tags_substitute` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `tag_id` INTEGER NOT NULL DEFAULT 0,
  `tag` varchar(100) NOT NULL DEFAULT '',
  `raw_tag` varchar(100) NOT NULL DEFAULT '',
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_tag_id` ON `#__tags_substitute` (`tag_id`);
CREATE INDEX IF NOT EXISTS `idx_tag` ON `#__tags_substitute` (`tag`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__tags_substitute` (`created_by`);

--
-- Table: `#__template_styles`
--

CREATE TABLE `#__template_styles` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `template` varchar(50) NOT NULL DEFAULT '',
  `client_id` INTEGER NOT NULL DEFAULT 0,
  `home` char(7) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `params` text NOT NULL
);

CREATE INDEX IF NOT EXISTS `idx_template` ON `#__template_styles` (`template`);
CREATE INDEX IF NOT EXISTS `idx_home` ON `#__template_styles` (`home`);

--
-- Table: `#__tool`
--

CREATE TABLE `#__tool` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `toolname` varchar(64) NOT NULL DEFAULT '',
  `title` varchar(127) NOT NULL DEFAULT '',
  `version` varchar(15) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `fulltxt` text DEFAULT NULL,
  `license` text DEFAULT NULL,
  `toolaccess` varchar(15) DEFAULT NULL,
  `codeaccess` varchar(15) DEFAULT NULL,
  `wikiaccess` varchar(15) DEFAULT NULL,
  `published` INTEGER DEFAULT 0,
  `state` INTEGER DEFAULT NULL,
  `priority` INTEGER DEFAULT 3,
  `team` text DEFAULT NULL,
  `registered` datetime DEFAULT NULL,
  `registered_by` varchar(31) DEFAULT NULL,
  `mw` varchar(31) DEFAULT NULL,
  `vnc_geometry` varchar(31) DEFAULT NULL,
  `ticketid` INTEGER DEFAULT NULL,
  `state_changed` datetime DEFAULT NULL,
  `revision` INTEGER DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_toolname` ON `#__tool` (`toolname`);

--
-- Table: `#__tool_authors`
--

CREATE TABLE `#__tool_authors` (
  `toolname` varchar(50) NOT NULL DEFAULT '',
  `revision` INTEGER NOT NULL DEFAULT 0,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `ordering` INTEGER DEFAULT 0,
  `version_id` INTEGER NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`toolname`,`revision`,`uid`,`version_id`)
);

--
-- Table: `#__tool_groups`
--

CREATE TABLE `#__tool_groups` (
  `cn` varchar(255) NOT NULL DEFAULT '',
  `toolid` INTEGER NOT NULL DEFAULT 0,
  `role` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`cn`,`toolid`,`role`)
);

--
-- Table: `#__tool_licenses`
--

CREATE TABLE `#__tool_licenses` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `ordering` INTEGER DEFAULT NULL
);

--
-- Table: `#__tool_statusviews`
--

CREATE TABLE `#__tool_statusviews` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `ticketid` varchar(15) NOT NULL DEFAULT '',
  `uid` varchar(31) NOT NULL DEFAULT '',
  `viewed` datetime DEFAULT NULL,
  `elapsed` INTEGER DEFAULT 500000
);

--
-- Table: `#__tool_version`
--

CREATE TABLE `#__tool_version` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `toolname` varchar(64) NOT NULL DEFAULT '',
  `instance` varchar(31) NOT NULL DEFAULT '',
  `title` varchar(127) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `fulltxt` text DEFAULT NULL,
  `version` varchar(15) DEFAULT NULL,
  `revision` INTEGER DEFAULT NULL,
  `toolaccess` varchar(15) DEFAULT NULL,
  `codeaccess` varchar(15) DEFAULT NULL,
  `wikiaccess` varchar(15) DEFAULT NULL,
  `state` INTEGER DEFAULT NULL,
  `released_by` varchar(31) DEFAULT NULL,
  `released` datetime DEFAULT NULL,
  `unpublished` datetime DEFAULT NULL,
  `exportControl` varchar(16) DEFAULT NULL,
  `license` text DEFAULT NULL,
  `vnc_geometry` varchar(31) DEFAULT NULL,
  `vnc_depth` INTEGER DEFAULT NULL,
  `vnc_timeout` INTEGER DEFAULT NULL,
  `vnc_command` varchar(100) DEFAULT NULL,
  `mw` varchar(31) DEFAULT NULL,
  `toolid` INTEGER DEFAULT NULL,
  `priority` INTEGER DEFAULT NULL,
  `params` text DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_toolname_instance` ON `#__tool_version` (`toolname`,`instance`);
CREATE INDEX IF NOT EXISTS `idx_instance` ON `#__tool_version` (`instance`);

--
-- Table: `#__tool_version_alias`
--

CREATE TABLE `#__tool_version_alias` (
  `tool_version_id` INTEGER NOT NULL,
  `alias` varchar(255) NOT NULL
);

--
-- Table: `#__tool_version_hostreq`
--

CREATE TABLE `#__tool_version_hostreq` (
  `tool_version_id` INTEGER NOT NULL,
  `hostreq` varchar(255) NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_tool_version_id_hostreq` ON `#__tool_version_hostreq` (`tool_version_id`,`hostreq`);

--
-- Table: `#__tool_version_middleware`
--

CREATE TABLE `#__tool_version_middleware` (
  `tool_version_id` INTEGER NOT NULL,
  `middleware` varchar(255) NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_tool_version_id_middleware` ON `#__tool_version_middleware` (`tool_version_id`,`middleware`);

--
-- Table: `#__tool_version_tracperm`
--

CREATE TABLE `#__tool_version_tracperm` (
  `tool_version_id` INTEGER NOT NULL,
  `tracperm` varchar(64) NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_tool_version_id_tracperm` ON `#__tool_version_tracperm` (`tool_version_id`,`tracperm`);

--
-- Table: `#__tool_version_zone`
--

CREATE TABLE `#__tool_version_zone` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `tool_version_id` INTEGER NOT NULL,
  `zone_id` INTEGER NOT NULL,
  `publish_up` datetime DEFAULT NULL,
  `publish_down` datetime DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_zoneid_toolversionid` ON `#__tool_version_zone` (`zone_id`,`tool_version_id`);

--
-- Table: `#__trac_group_permission`
--

CREATE TABLE `#__trac_group_permission` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `group_id` INTEGER NOT NULL,
  `action` varchar(255) NOT NULL,
  `trac_project_id` INTEGER NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_group_id_action_trac_project_id` ON `#__trac_group_permission` (`group_id`,`action`,`trac_project_id`);

--
-- Table: `#__trac_project`
--

CREATE TABLE `#__trac_project` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(255) NOT NULL
);

--
-- Table: `#__trac_projects`
--

CREATE TABLE `#__trac_projects` (
  `id` INTEGER NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` INTEGER NOT NULL
);

--
-- Table: `#__trac_user_permission`
--

CREATE TABLE `#__trac_user_permission` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `trac_project_id` INTEGER DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_user_id_action_trac_project_id` ON `#__trac_user_permission` (`user_id`,`action`,`trac_project_id`);

--
-- Table: `#__update_categories`
--

CREATE TABLE `#__update_categories` (
  `categoryid` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(20) DEFAULT '',
  `description` text NOT NULL,
  `parent` INTEGER DEFAULT 0,
  `updatesite` INTEGER DEFAULT 0
);

--
-- Table: `#__update_sites`
--

CREATE TABLE `#__update_sites` (
  `update_site_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(100) DEFAULT '',
  `type` varchar(20) DEFAULT '',
  `location` text NOT NULL,
  `enabled` INTEGER DEFAULT 0,
  `last_check_timestamp` INTEGER DEFAULT 0
);

--
-- Table: `#__update_sites_extensions`
--

CREATE TABLE `#__update_sites_extensions` (
  `update_site_id` INTEGER NOT NULL DEFAULT 0,
  `extension_id` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`update_site_id`,`extension_id`)
);

--
-- Table: `#__updates`
--

CREATE TABLE `#__updates` (
  `update_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `update_site_id` INTEGER DEFAULT 0,
  `extension_id` INTEGER DEFAULT 0,
  `categoryid` INTEGER DEFAULT 0,
  `name` varchar(100) DEFAULT '',
  `description` text NOT NULL,
  `element` varchar(100) DEFAULT '',
  `type` varchar(20) DEFAULT '',
  `folder` varchar(20) DEFAULT '',
  `client_id` INTEGER DEFAULT 0,
  `version` varchar(10) DEFAULT '',
  `data` text NOT NULL,
  `detailsurl` text NOT NULL,
  `infourl` text NOT NULL
);

--
-- Table: `#__user_notes`
--

CREATE TABLE `#__user_notes` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `catid` INTEGER NOT NULL DEFAULT 0,
  `subject` varchar(100) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `state` INTEGER NOT NULL DEFAULT 0,
  `checked_out` INTEGER NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT NULL,
  `created_user_id` INTEGER NOT NULL DEFAULT 0,
  `created_time` datetime NOT NULL DEFAULT NULL,
  `modified_user_id` INTEGER NOT NULL,
  `modified_time` datetime NOT NULL DEFAULT NULL,
  `review_time` datetime NOT NULL DEFAULT NULL,
  `publish_up` datetime NOT NULL DEFAULT NULL,
  `publish_down` datetime NOT NULL DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_user_id` ON `#__user_notes` (`user_id`);
CREATE INDEX IF NOT EXISTS `idx_category_id` ON `#__user_notes` (`catid`);

--
-- Table: `#__user_profiles`
--

CREATE TABLE `#__user_profiles` (
  `user_id` INTEGER NOT NULL,
  `profile_key` varchar(100) NOT NULL,
  `profile_value` text NOT NULL,
  `ordering` INTEGER NOT NULL DEFAULT 0
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_user_id_profile_key` ON `#__user_profiles` (`user_id`,`profile_key`);

--
-- Table: `#__user_roles`
--

CREATE TABLE `#__user_roles` (
  `user_id` INTEGER NOT NULL,
  `role` varchar(20) NOT NULL,
  `group_id` INTEGER DEFAULT NULL,
  `id` INTEGER PRIMARY KEY AUTOINCREMENT
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_role_user_id_group_id` ON `#__user_roles` (`role`,`user_id`,`group_id`);

--
-- Table: `#__user_usergroup_map`
--

CREATE TABLE `#__user_usergroup_map` (
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `group_id` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`group_id`)
);

--
-- Table: `#__usergroups`
--

CREATE TABLE `#__usergroups` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `parent_id` INTEGER NOT NULL DEFAULT 0,
  `lft` INTEGER NOT NULL DEFAULT 0,
  `rgt` INTEGER NOT NULL DEFAULT 0,
  `title` varchar(100) NOT NULL DEFAULT ''
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_usergroup_parent_title_lookup` ON `#__usergroups` (`parent_id`,`title`);
CREATE INDEX IF NOT EXISTS `idx_usergroup_title_lookup` ON `#__usergroups` (`title`);
CREATE INDEX IF NOT EXISTS `idx_usergroup_adjacency_lookup` ON `#__usergroups` (`parent_id`);
CREATE INDEX IF NOT EXISTS `idx_usergroup_nested_set_lookup` ON `#__usergroups` (`lft`,`rgt`);

--
-- Table: `#__users`
--

CREATE TABLE `#__users` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(150) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(127) NOT NULL DEFAULT '',
  `usertype` varchar(25) NOT NULL DEFAULT '',
  `block` INTEGER NOT NULL DEFAULT 0,
  `approved` INTEGER NOT NULL DEFAULT 2,
  `sendEmail` INTEGER DEFAULT 0,
  `registerDate` datetime NOT NULL DEFAULT NULL,
  `lastvisitDate` datetime NOT NULL DEFAULT NULL,
  `activation` varchar(100) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  `lastResetTime` datetime NOT NULL DEFAULT NULL,
  `resetCount` INTEGER NOT NULL DEFAULT 0
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_username` ON `#__users` (`username`);
CREATE INDEX IF NOT EXISTS `usertype` ON `#__users` (`usertype`);
CREATE INDEX IF NOT EXISTS `idx_name` ON `#__users` (`name`);
CREATE INDEX IF NOT EXISTS `idx_block` ON `#__users` (`block`);
CREATE INDEX IF NOT EXISTS `username` ON `#__users` (`username`);
CREATE INDEX IF NOT EXISTS `email` ON `#__users` (`email`);

--
-- Table: `#__users_merge_log`
--

CREATE TABLE `#__users_merge_log` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `source` varchar(150) NOT NULL DEFAULT '',
  `destination` varchar(150) NOT NULL DEFAULT '',
  `table` varchar(255) NOT NULL DEFAULT '',
  `column` varchar(255) NOT NULL DEFAULT '',
  `table_pk` varchar(255) DEFAULT NULL,
  `table_id` INTEGER DEFAULT NULL,
  `logged` datetime NOT NULL
);

--
-- Table: `#__users_password`
--

CREATE TABLE `#__users_password` (
  `user_id` INTEGER NOT NULL,
  `passhash` char(127) NOT NULL,
  `shadowExpire` INTEGER DEFAULT NULL,
  `shadowFlag` INTEGER DEFAULT NULL,
  `shadowInactive` INTEGER DEFAULT NULL,
  `shadowLastChange` INTEGER DEFAULT NULL,
  `shadowMax` INTEGER DEFAULT NULL,
  `shadowMin` INTEGER DEFAULT NULL,
  `shadowWarning` INTEGER DEFAULT NULL,
  PRIMARY KEY (`user_id`)
);

--
-- Table: `#__users_password_history`
--

CREATE TABLE `#__users_password_history` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER NOT NULL,
  `passhash` char(127) NOT NULL DEFAULT '',
  `action` INTEGER DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER DEFAULT NULL,
  `invalidated` datetime DEFAULT NULL,
  `invalidated_by` INTEGER DEFAULT NULL
);

--
-- Table: `#__users_points`
--

CREATE TABLE `#__users_points` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `balance` INTEGER NOT NULL DEFAULT 0,
  `earnings` INTEGER NOT NULL DEFAULT 0,
  `credit` INTEGER DEFAULT 0
);

--
-- Table: `#__users_points_config`
--

CREATE TABLE `#__users_points_config` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `points` INTEGER DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `alias` varchar(50) DEFAULT NULL
);

--
-- Table: `#__users_points_services`
--

CREATE TABLE `#__users_points_services` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `category` varchar(50) NOT NULL DEFAULT '',
  `alias` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `unitprice` float(6,2) DEFAULT 0.00,
  `pointsprice` INTEGER DEFAULT 0,
  `currency` varchar(50) DEFAULT 'points',
  `maxunits` INTEGER DEFAULT 0,
  `minunits` INTEGER DEFAULT 0,
  `unitsize` INTEGER DEFAULT 0,
  `status` INTEGER DEFAULT 0,
  `restricted` INTEGER DEFAULT 0,
  `ordering` INTEGER DEFAULT 0,
  `params` text DEFAULT NULL,
  `unitmeasure` varchar(200) NOT NULL DEFAULT '',
  `changed` datetime DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_alias` ON `#__users_points_services` (`alias`);

--
-- Table: `#__users_points_subscriptions`
--

CREATE TABLE `#__users_points_subscriptions` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `serviceid` INTEGER NOT NULL DEFAULT 0,
  `units` INTEGER NOT NULL DEFAULT 1,
  `status` INTEGER NOT NULL DEFAULT 0,
  `pendingunits` INTEGER DEFAULT 0,
  `pendingpayment` float(6,2) DEFAULT 0.00,
  `totalpaid` float(6,2) DEFAULT 0.00,
  `installment` INTEGER DEFAULT 0,
  `contact` varchar(20) DEFAULT '',
  `code` varchar(10) DEFAULT '',
  `usepoints` INTEGER DEFAULT 0,
  `notes` text DEFAULT NULL,
  `added` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `expires` datetime DEFAULT NULL
);

--
-- Table: `#__users_quotas`
--

CREATE TABLE `#__users_quotas` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER NOT NULL,
  `class_id` INTEGER DEFAULT NULL,
  `hard_files` INTEGER NOT NULL,
  `soft_files` INTEGER NOT NULL,
  `hard_blocks` INTEGER NOT NULL,
  `soft_blocks` INTEGER NOT NULL
);

--
-- Table: `#__users_quotas_classes`
--

CREATE TABLE `#__users_quotas_classes` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `hard_files` INTEGER NOT NULL,
  `soft_files` INTEGER NOT NULL,
  `hard_blocks` INTEGER NOT NULL,
  `soft_blocks` INTEGER NOT NULL
);

--
-- Table: `#__users_quotas_classes_groups`
--

CREATE TABLE `#__users_quotas_classes_groups` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `class_id` INTEGER NOT NULL DEFAULT 0,
  `group_id` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_class_id` ON `#__users_quotas_classes_groups` (`class_id`);
CREATE INDEX IF NOT EXISTS `idx_group_id` ON `#__users_quotas_classes_groups` (`group_id`);

--
-- Table: `#__users_quotas_log`
--

CREATE TABLE `#__users_quotas_log` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `object_type` varchar(255) NOT NULL DEFAULT '',
  `object_id` INTEGER NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(255) NOT NULL DEFAULT '',
  `actor_id` INTEGER NOT NULL,
  `soft_blocks` INTEGER NOT NULL,
  `hard_blocks` INTEGER NOT NULL,
  `soft_files` INTEGER NOT NULL,
  `hard_files` INTEGER NOT NULL
);

--
-- Table: `#__users_tracperms`
--

CREATE TABLE `#__users_tracperms` (
  `user_id` INTEGER NOT NULL,
  `action` varchar(255) NOT NULL,
  `project_id` INTEGER NOT NULL,
  PRIMARY KEY (`user_id`,`action`)
);

--
-- Table: `#__users_transactions`
--

CREATE TABLE `#__users_transactions` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `type` varchar(20) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `referenceid` INTEGER DEFAULT 0,
  `amount` INTEGER DEFAULT 0,
  `balance` INTEGER DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_referenceid_category_type` ON `#__users_transactions` (`referenceid`,`category`,`type`);

--
-- Table: `#__viewlevels`
--

CREATE TABLE `#__viewlevels` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `rules` varchar(5120) NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_assetgroup_title_lookup` ON `#__viewlevels` (`title`);

--
-- Table: `#__vote_log`
--

CREATE TABLE `#__vote_log` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `referenceid` INTEGER NOT NULL DEFAULT 0,
  `voted` datetime NOT NULL DEFAULT NULL,
  `voter` INTEGER DEFAULT NULL,
  `helpful` varchar(11) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_referenceid` ON `#__vote_log` (`referenceid`);

--
-- Table: `#__wiki_attachments`
--

CREATE TABLE `#__wiki_attachments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `pageid` INTEGER DEFAULT 0,
  `filename` varchar(255) DEFAULT NULL,
  `description` tinytext DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_pageid` ON `#__wiki_attachments` (`pageid`);

--
-- Table: `#__wiki_comments`
--

CREATE TABLE `#__wiki_comments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `pageid` INTEGER NOT NULL DEFAULT 0,
  `version` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `ctext` text DEFAULT NULL,
  `chtml` text DEFAULT NULL,
  `rating` INTEGER NOT NULL DEFAULT 0,
  `anonymous` INTEGER NOT NULL DEFAULT 0,
  `parent` INTEGER NOT NULL DEFAULT 0,
  `status` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_pageid` ON `#__wiki_comments` (`pageid`);
CREATE INDEX IF NOT EXISTS `idx_version` ON `#__wiki_comments` (`version`);
CREATE INDEX IF NOT EXISTS `idx_status` ON `#__wiki_comments` (`status`);

--
-- Table: `#__wiki_log`
--

CREATE TABLE `#__wiki_log` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `pid` INTEGER NOT NULL DEFAULT 0,
  `timestamp` datetime NOT NULL DEFAULT NULL,
  `uid` INTEGER DEFAULT 0,
  `action` varchar(50) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `actorid` INTEGER DEFAULT 0
);

--
-- Table: `#__wiki_math`
--

CREATE TABLE `#__wiki_math` (
  `inputhash` varchar(32) NOT NULL DEFAULT '',
  `outputhash` varchar(32) NOT NULL DEFAULT '',
  `conservativeness` INTEGER NOT NULL,
  `html` text DEFAULT NULL,
  `mathml` text DEFAULT NULL,
  `id` INTEGER PRIMARY KEY AUTOINCREMENT
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_inputhash` ON `#__wiki_math` (`inputhash`);

--
-- Table: `#__wiki_page`
--

CREATE TABLE `#__wiki_page` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `pagename` varchar(100) DEFAULT NULL,
  `hits` INTEGER NOT NULL DEFAULT 0,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `times_rated` INTEGER NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `scope` varchar(255) NOT NULL,
  `params` tinytext DEFAULT NULL,
  `ranking` float DEFAULT 0,
  `authors` varchar(255) DEFAULT NULL,
  `access` INTEGER DEFAULT 0,
  `group_cn` varchar(255) DEFAULT NULL,
  `state` INTEGER DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT NULL,
  `version_id` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_group_cn` ON `#__wiki_page` (`group_cn`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__wiki_page` (`state`);

--
-- Table: `#__wiki_page_author`
--

CREATE TABLE `#__wiki_page_author` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER DEFAULT 0,
  `page_id` INTEGER DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_page_id` ON `#__wiki_page_author` (`page_id`);
CREATE INDEX IF NOT EXISTS `idx_user_id` ON `#__wiki_page_author` (`user_id`);

--
-- Table: `#__wiki_page_links`
--

CREATE TABLE `#__wiki_page_links` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `page_id` INTEGER NOT NULL DEFAULT 0,
  `timestamp` datetime NOT NULL DEFAULT NULL,
  `scope` varchar(50) NOT NULL DEFAULT '',
  `scope_id` INTEGER NOT NULL DEFAULT 0,
  `link` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(250) NOT NULL DEFAULT ''
);

CREATE INDEX IF NOT EXISTS `idx_page_id` ON `#__wiki_page_links` (`page_id`);
CREATE INDEX IF NOT EXISTS `idx_scope_scope_id` ON `#__wiki_page_links` (`scope`,`scope_id`);

--
-- Table: `#__wiki_page_metrics`
--

CREATE TABLE `#__wiki_page_metrics` (
  `pageid` INTEGER NOT NULL DEFAULT 0,
  `pagename` varchar(100) DEFAULT NULL,
  `hits` INTEGER NOT NULL DEFAULT 0,
  `visitors` INTEGER NOT NULL DEFAULT 0,
  `visits` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`pageid`)
);

--
-- Table: `#__wiki_version`
--

CREATE TABLE `#__wiki_version` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `pageid` INTEGER NOT NULL DEFAULT 0,
  `version` INTEGER NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `minor_edit` INTEGER NOT NULL DEFAULT 0,
  `pagetext` text DEFAULT NULL,
  `pagehtml` text DEFAULT NULL,
  `approved` INTEGER NOT NULL DEFAULT 0,
  `summary` varchar(255) DEFAULT NULL,
  `length` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_pageid` ON `#__wiki_version` (`pageid`);
CREATE INDEX IF NOT EXISTS `idx_approved` ON `#__wiki_version` (`approved`);

--
-- Table: `#__wish_attachments`
--

CREATE TABLE `#__wish_attachments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `wish` INTEGER NOT NULL DEFAULT 0,
  `filename` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_wish` ON `#__wish_attachments` (`wish`);

--
-- Table: `#__wishlist`
--

CREATE TABLE `#__wishlist` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `category` varchar(50) NOT NULL,
  `referenceid` INTEGER NOT NULL DEFAULT 0,
  `title` varchar(150) NOT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT NULL,
  `state` INTEGER NOT NULL DEFAULT 0,
  `public` INTEGER NOT NULL DEFAULT 1,
  `description` varchar(255) DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_category_referenceid` ON `#__wishlist` (`category`,`referenceid`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__wishlist` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_state` ON `#__wishlist` (`state`);

--
-- Table: `#__wishlist_implementation`
--

CREATE TABLE `#__wishlist_implementation` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `wishid` INTEGER NOT NULL DEFAULT 0,
  `version` INTEGER NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `minor_edit` INTEGER NOT NULL DEFAULT 0,
  `pagetext` text DEFAULT NULL,
  `pagehtml` text DEFAULT NULL,
  `approved` INTEGER NOT NULL DEFAULT 0,
  `summary` varchar(255) DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_wishid` ON `#__wishlist_implementation` (`wishid`);
CREATE INDEX IF NOT EXISTS `idx_created_by` ON `#__wishlist_implementation` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_approved` ON `#__wishlist_implementation` (`approved`);

--
-- Table: `#__wishlist_item`
--

CREATE TABLE `#__wishlist_item` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `wishlist` INTEGER DEFAULT 0,
  `subject` varchar(200) NOT NULL,
  `about` text DEFAULT NULL,
  `proposed_by` INTEGER DEFAULT 0,
  `granted_by` INTEGER DEFAULT 0,
  `assigned` INTEGER DEFAULT 0,
  `granted_vid` INTEGER DEFAULT 0,
  `proposed` datetime NOT NULL DEFAULT NULL,
  `granted` datetime DEFAULT NULL,
  `status` INTEGER NOT NULL DEFAULT 0,
  `due` datetime DEFAULT NULL,
  `anonymous` INTEGER DEFAULT 0,
  `ranking` INTEGER DEFAULT 0,
  `points` INTEGER DEFAULT 0,
  `private` INTEGER DEFAULT 0,
  `accepted` INTEGER DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_wishlist` ON `#__wishlist_item` (`wishlist`);

--
-- Table: `#__wishlist_ownergroups`
--

CREATE TABLE `#__wishlist_ownergroups` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `wishlist` INTEGER NOT NULL DEFAULT 0,
  `groupid` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_wishlist` ON `#__wishlist_ownergroups` (`wishlist`);
CREATE INDEX IF NOT EXISTS `idx_groupid` ON `#__wishlist_ownergroups` (`groupid`);

--
-- Table: `#__wishlist_owners`
--

CREATE TABLE `#__wishlist_owners` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `wishlist` INTEGER NOT NULL DEFAULT 0,
  `userid` INTEGER NOT NULL DEFAULT 0,
  `type` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_wishlist` ON `#__wishlist_owners` (`wishlist`);
CREATE INDEX IF NOT EXISTS `idx_userid` ON `#__wishlist_owners` (`userid`);
CREATE INDEX IF NOT EXISTS `idx_type` ON `#__wishlist_owners` (`type`);

--
-- Table: `#__wishlist_vote`
--

CREATE TABLE `#__wishlist_vote` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `wishid` INTEGER NOT NULL DEFAULT 0,
  `userid` INTEGER NOT NULL DEFAULT 0,
  `voted` datetime NOT NULL DEFAULT NULL,
  `importance` INTEGER NOT NULL DEFAULT 0,
  `effort` INTEGER NOT NULL DEFAULT 0,
  `due` datetime DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_wishid` ON `#__wishlist_vote` (`wishid`);
CREATE INDEX IF NOT EXISTS `idx_userid` ON `#__wishlist_vote` (`userid`);

--
-- Table: `#__xdomain_users`
--

CREATE TABLE `#__xdomain_users` (
  `domain_id` INTEGER NOT NULL,
  `domain_username` varchar(150) NOT NULL DEFAULT '',
  `uidNumber` INTEGER DEFAULT NULL,
  PRIMARY KEY (`domain_id`,`domain_username`)
);

--
-- Table: `#__xdomains`
--

CREATE TABLE `#__xdomains` (
  `domain_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `domain` varchar(150) NOT NULL DEFAULT ''
);

--
-- Table: `#__xgroups`
--

CREATE TABLE `#__xgroups` (
  `gidNumber` INTEGER PRIMARY KEY AUTOINCREMENT,
  `cn` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `published` INTEGER DEFAULT 0,
  `approved` INTEGER DEFAULT 1,
  `type` INTEGER DEFAULT 0,
  `public_desc` text DEFAULT NULL,
  `private_desc` text DEFAULT NULL,
  `restrict_msg` text DEFAULT NULL,
  `join_policy` INTEGER DEFAULT 0,
  `discoverability` INTEGER DEFAULT NULL,
  `discussion_email_autosubscribe` INTEGER DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `plugins` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER DEFAULT NULL,
  `params` text DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `idx_cn` ON `#__xgroups` (`cn`);

--
-- Table: `#__xgroups_applicants`
--

CREATE TABLE `#__xgroups_applicants` (
  `gidNumber` INTEGER NOT NULL,
  `uidNumber` INTEGER NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
);

--
-- Table: `#__xgroups_inviteemails`
--

CREATE TABLE `#__xgroups_inviteemails` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `email` varchar(150) NOT NULL,
  `gidNumber` INTEGER NOT NULL,
  `token` varchar(255) NOT NULL
);

--
-- Table: `#__xgroups_invitees`
--

CREATE TABLE `#__xgroups_invitees` (
  `gidNumber` INTEGER NOT NULL,
  `uidNumber` INTEGER NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
);

--
-- Table: `#__xgroups_log`
--

CREATE TABLE `#__xgroups_log` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `gidNumber` INTEGER DEFAULT NULL,
  `timestamp` datetime NOT NULL DEFAULT NULL,
  `userid` INTEGER DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `actorid` INTEGER DEFAULT 0
);

--
-- Table: `#__xgroups_managers`
--

CREATE TABLE `#__xgroups_managers` (
  `gidNumber` INTEGER NOT NULL,
  `uidNumber` INTEGER NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
);

--
-- Table: `#__xgroups_member_roles`
--

CREATE TABLE `#__xgroups_member_roles` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `roleid` INTEGER DEFAULT NULL,
  `uidNumber` INTEGER DEFAULT NULL
);

--
-- Table: `#__xgroups_memberoption`
--

CREATE TABLE `#__xgroups_memberoption` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `gidNumber` INTEGER DEFAULT NULL,
  `userid` INTEGER DEFAULT NULL,
  `optionname` varchar(100) DEFAULT NULL,
  `optionvalue` varchar(100) DEFAULT NULL
);

--
-- Table: `#__xgroups_members`
--

CREATE TABLE `#__xgroups_members` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `gidNumber` INTEGER NOT NULL,
  `uidNumber` INTEGER NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `id` ON `#__xgroups_members` (`id`);
CREATE UNIQUE INDEX IF NOT EXISTS `idx_gidNumber_uidNumber` ON `#__xgroups_members` (`gidNumber`,`uidNumber`);

--
-- Table: `#__xgroups_modules`
--

CREATE TABLE `#__xgroups_modules` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `gidNumber` INTEGER DEFAULT NULL,
  `title` varchar(255) DEFAULT '',
  `content` text DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `ordering` INTEGER DEFAULT NULL,
  `state` INTEGER DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` INTEGER DEFAULT NULL,
  `approved` INTEGER DEFAULT 1,
  `approved_on` datetime DEFAULT NULL,
  `approved_by` INTEGER DEFAULT NULL,
  `checked_errors` INTEGER DEFAULT 0,
  `scanned` INTEGER DEFAULT 0
);

--
-- Table: `#__xgroups_modules_menu`
--

CREATE TABLE `#__xgroups_modules_menu` (
  `moduleid` INTEGER DEFAULT NULL,
  `pageid` INTEGER DEFAULT NULL
);

--
-- Table: `#__xgroups_pages`
--

CREATE TABLE `#__xgroups_pages` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `gidNumber` INTEGER DEFAULT NULL,
  `parent` INTEGER DEFAULT 0,
  `lft` INTEGER DEFAULT NULL,
  `rgt` INTEGER DEFAULT NULL,
  `depth` INTEGER DEFAULT 1,
  `category` INTEGER DEFAULT NULL,
  `template` varchar(100) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `state` INTEGER DEFAULT 1,
  `privacy` varchar(10) DEFAULT NULL,
  `home` INTEGER DEFAULT 0,
  `comments` INTEGER DEFAULT NULL
);

--
-- Table: `#__xgroups_pages_categories`
--

CREATE TABLE `#__xgroups_pages_categories` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `gidNumber` INTEGER DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `color` varchar(6) DEFAULT NULL
);

--
-- Table: `#__xgroups_pages_checkout`
--

CREATE TABLE `#__xgroups_pages_checkout` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `pageid` INTEGER DEFAULT NULL,
  `userid` INTEGER DEFAULT NULL,
  `when` datetime DEFAULT NULL
);

--
-- Table: `#__xgroups_pages_hits`
--

CREATE TABLE `#__xgroups_pages_hits` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `gidNumber` INTEGER DEFAULT NULL,
  `pageid` INTEGER DEFAULT NULL,
  `userid` INTEGER DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL
);

--
-- Table: `#__xgroups_pages_versions`
--

CREATE TABLE `#__xgroups_pages_versions` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `pageid` INTEGER DEFAULT NULL,
  `version` INTEGER DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER DEFAULT NULL,
  `approved` INTEGER DEFAULT 1,
  `approved_on` datetime DEFAULT NULL,
  `approved_by` INTEGER DEFAULT NULL,
  `checked_errors` INTEGER DEFAULT 0,
  `scanned` INTEGER DEFAULT 0
);

--
-- Table: `#__xgroups_reasons`
--

CREATE TABLE `#__xgroups_reasons` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uidNumber` INTEGER NOT NULL,
  `gidNumber` INTEGER NOT NULL,
  `reason` text DEFAULT NULL,
  `date` datetime DEFAULT NULL
);

--
-- Table: `#__xgroups_roles`
--

CREATE TABLE `#__xgroups_roles` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `gidNumber` INTEGER DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `permissions` text DEFAULT NULL
);

--
-- Table: `#__xgroups_tracperm`
--

CREATE TABLE `#__xgroups_tracperm` (
  `group_id` INTEGER NOT NULL,
  `action` varchar(255) NOT NULL,
  `project_id` INTEGER NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `id` ON `#__xgroups_tracperm` (`group_id`,`action`);

--
-- Table: `#__xmessage`
--

CREATE TABLE `#__xmessage` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `created` datetime DEFAULT NULL,
  `created_by` INTEGER NOT NULL DEFAULT 0,
  `message` mediumtext DEFAULT NULL,
  `subject` varchar(250) DEFAULT NULL,
  `component` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `group_id` INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_component` ON `#__xmessage` (`component`);
CREATE INDEX IF NOT EXISTS `idx_group_id` ON `#__xmessage` (`group_id`);

--
-- Table: `#__xmessage_action`
--

CREATE TABLE `#__xmessage_action` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `class` varchar(20) NOT NULL DEFAULT '',
  `element` INTEGER NOT NULL DEFAULT 0,
  `description` mediumtext DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_class` ON `#__xmessage_action` (`class`);
CREATE INDEX IF NOT EXISTS `idx_element` ON `#__xmessage_action` (`element`);

--
-- Table: `#__xmessage_component`
--

CREATE TABLE `#__xmessage_component` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `component` varchar(50) NOT NULL DEFAULT '',
  `action` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT ''
);

CREATE INDEX IF NOT EXISTS `idx_component` ON `#__xmessage_component` (`component`);

--
-- Table: `#__xmessage_notify`
--

CREATE TABLE `#__xmessage_notify` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `method` varchar(250) DEFAULT NULL,
  `type` varchar(250) DEFAULT NULL,
  `priority` INTEGER DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_uid` ON `#__xmessage_notify` (`uid`);
CREATE INDEX IF NOT EXISTS `idx_method` ON `#__xmessage_notify` (`method`);

--
-- Table: `#__xmessage_recipient`
--

CREATE TABLE `#__xmessage_recipient` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `mid` INTEGER DEFAULT 0,
  `uid` INTEGER DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  `actionid` INTEGER DEFAULT 0,
  `state` INTEGER DEFAULT 0
);

CREATE INDEX IF NOT EXISTS `idx_mid` ON `#__xmessage_recipient` (`mid`);
CREATE INDEX IF NOT EXISTS `idx_uid` ON `#__xmessage_recipient` (`uid`);

--
-- Table: `#__xmessage_seen`
--

CREATE TABLE `#__xmessage_seen` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `mid` INTEGER NOT NULL DEFAULT 0,
  `uid` INTEGER NOT NULL DEFAULT 0,
  `whenseen` datetime DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS `idx_mid` ON `#__xmessage_seen` (`mid`);
CREATE INDEX IF NOT EXISTS `idx_uid` ON `#__xmessage_seen` (`uid`);

--
-- Table: `#__xorganization_types`
--

CREATE TABLE `#__xorganization_types` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `type` varchar(150) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL
);

--
-- Table: `#__xorganizations`
--

CREATE TABLE `#__xorganizations` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `organization` varchar(255) DEFAULT NULL
);

--
-- Table: `#__xprofiles`
--

CREATE TABLE `#__xprofiles` (
  `uidNumber` INTEGER NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(150) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `registerDate` datetime NOT NULL DEFAULT NULL,
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
  `reason` text DEFAULT NULL,
  `mailPreferenceOption` INTEGER NOT NULL DEFAULT -1,
  `usageAgreement` INTEGER NOT NULL DEFAULT 0,
  `jobsAllowed` INTEGER NOT NULL DEFAULT 0,
  `modifiedDate` datetime NOT NULL DEFAULT NULL,
  `emailConfirmed` INTEGER NOT NULL DEFAULT 0,
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
  `vip` INTEGER NOT NULL DEFAULT 0,
  `public` INTEGER NOT NULL DEFAULT 0,
  `params` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `shadowExpire` INTEGER DEFAULT NULL,
  `locked` INTEGER NOT NULL DEFAULT 0,
  `orcid` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`)
);

CREATE INDEX IF NOT EXISTS `idx_username` ON `#__xprofiles` (`username`);

--
-- Table: `#__xprofiles_address`
--

CREATE TABLE `#__xprofiles_address` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uidNumber` INTEGER DEFAULT NULL,
  `addressTo` varchar(200) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `addressCity` varchar(200) DEFAULT NULL,
  `addressRegion` varchar(200) DEFAULT NULL,
  `addressPostal` varchar(200) DEFAULT NULL,
  `addressCountry` varchar(200) DEFAULT NULL,
  `addressLatitude` float DEFAULT NULL,
  `addressLongitude` float DEFAULT NULL
);

--
-- Table: `#__xprofiles_admin`
--

CREATE TABLE `#__xprofiles_admin` (
  `uidNumber` INTEGER NOT NULL,
  `admin` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`admin`)
);

--
-- Table: `#__xprofiles_bio`
--

CREATE TABLE `#__xprofiles_bio` (
  `uidNumber` INTEGER NOT NULL,
  `bio` text DEFAULT NULL,
  PRIMARY KEY (`uidNumber`)
);

--
-- Table: `#__xprofiles_dashboard_preferences`
--

CREATE TABLE `#__xprofiles_dashboard_preferences` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uidNumber` INTEGER NOT NULL,
  `preferences` text DEFAULT NULL,
  `modified` datetime DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidNumber` ON `#__xprofiles_dashboard_preferences` (`uidNumber`);

--
-- Table: `#__xprofiles_disability`
--

CREATE TABLE `#__xprofiles_disability` (
  `uidNumber` INTEGER NOT NULL,
  `disability` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`disability`)
);

--
-- Table: `#__xprofiles_edulevel`
--

CREATE TABLE `#__xprofiles_edulevel` (
  `uidNumber` INTEGER NOT NULL,
  `edulevel` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`edulevel`)
);

--
-- Table: `#__xprofiles_hispanic`
--

CREATE TABLE `#__xprofiles_hispanic` (
  `uidNumber` INTEGER NOT NULL,
  `hispanic` varchar(255) NOT NULL,
  PRIMARY KEY (`uidNumber`,`hispanic`)
);

--
-- Table: `#__xprofiles_host`
--

CREATE TABLE `#__xprofiles_host` (
  `uidNumber` INTEGER NOT NULL,
  `host` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`host`)
);

--
-- Table: `#__xprofiles_race`
--

CREATE TABLE `#__xprofiles_race` (
  `uidNumber` INTEGER NOT NULL,
  `race` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`race`)
);

--
-- Table: `#__xprofiles_role`
--

CREATE TABLE `#__xprofiles_role` (
  `uidNumber` INTEGER NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uidNumber`,`role`)
);

--
-- Table: `#__xsession`
--

CREATE TABLE `#__xsession` (
  `session_id` varchar(200) NOT NULL DEFAULT '0',
  `ip` varchar(15) DEFAULT NULL,
  `host` varchar(128) DEFAULT NULL,
  `domain` varchar(128) DEFAULT NULL,
  `signed` INTEGER DEFAULT 0,
  `countrySHORT` char(2) DEFAULT NULL,
  `countryLONG` varchar(64) DEFAULT NULL,
  `ipREGION` varchar(128) DEFAULT NULL,
  `ipCITY` varchar(128) DEFAULT NULL,
  `ipLATITUDE` double DEFAULT NULL,
  `ipLONGITUDE` double DEFAULT NULL,
  `bot` INTEGER DEFAULT 0,
  PRIMARY KEY (`session_id`)
);

CREATE INDEX IF NOT EXISTS `idx_ip` ON `#__xsession` (`ip`);

--
-- Table: `#__ysearch_plugin_weights`
--

CREATE TABLE `#__ysearch_plugin_weights` (
  `plugin` varchar(20) NOT NULL,
  `weight` float NOT NULL,
  PRIMARY KEY (`plugin`)
);

--
-- Table: `#__ysearch_site_map`
--

CREATE TABLE `#__ysearch_site_map` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `link` varchar(200) NOT NULL
);

--
-- Table: `app`
--

CREATE TABLE `app` (
  `appname` varchar(80) NOT NULL DEFAULT '',
  `geometry` varchar(9) NOT NULL DEFAULT '',
  `depth` INTEGER NOT NULL DEFAULT 16,
  `hostreq` INTEGER NOT NULL DEFAULT 0,
  `userreq` INTEGER NOT NULL DEFAULT 0,
  `timeout` INTEGER NOT NULL DEFAULT 0,
  `command` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT ''
);

--
-- Table: `display`
--

CREATE TABLE `display` (
  `hostname` varchar(40) NOT NULL DEFAULT '',
  `dispnum` INTEGER DEFAULT 0,
  `geometry` varchar(9) NOT NULL DEFAULT '',
  `depth` INTEGER NOT NULL DEFAULT 16,
  `sessnum` INTEGER DEFAULT 0,
  `vncpass` varchar(16) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT ''
);

CREATE INDEX IF NOT EXISTS `idx_hostname` ON `display` (`hostname`);

--
-- Table: `domainclass`
--

CREATE TABLE `domainclass` (
  `class` INTEGER NOT NULL DEFAULT 0,
  `country` varchar(4) NOT NULL,
  `domain` varchar(64) NOT NULL,
  `name` tinytext NOT NULL,
  `state` varchar(4) NOT NULL,
  PRIMARY KEY (`domain`)
);

CREATE INDEX IF NOT EXISTS `idx_class` ON `domainclass` (`class`);
CREATE INDEX IF NOT EXISTS `idx_domain_class` ON `domainclass` (`domain`,`class`);

--
-- Table: `domainclasses`
--

CREATE TABLE `domainclasses` (
  `class` INTEGER NOT NULL DEFAULT 0,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`class`)
);

--
-- Table: `fileperm`
--

CREATE TABLE `fileperm` (
  `sessnum` INTEGER NOT NULL DEFAULT 0,
  `fileuser` varchar(32) NOT NULL DEFAULT '',
  `fwhost` varchar(40) NOT NULL DEFAULT '',
  `fwport` INTEGER NOT NULL DEFAULT 0,
  `cookie` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`sessnum`,`fileuser`)
);

--
-- Table: `host`
--

CREATE TABLE `host` (
  `hostname` varchar(40) NOT NULL DEFAULT '',
  `provisions` INTEGER NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT '',
  `uses` INTEGER NOT NULL DEFAULT 0,
  `portbase` INTEGER NOT NULL DEFAULT 0,
  `zone_id` INTEGER DEFAULT NULL,
  `max_uses` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`hostname`)
);

--
-- Table: `hosttype`
--

CREATE TABLE `hosttype` (
  `name` varchar(40) NOT NULL DEFAULT '',
  `value` INTEGER NOT NULL DEFAULT 0,
  `description` varchar(255) NOT NULL DEFAULT ''
);

--
-- Table: `job`
--

CREATE TABLE `job` (
  `sessnum` INTEGER NOT NULL DEFAULT 0,
  `jobid` INTEGER PRIMARY KEY AUTOINCREMENT,
  `superjob` INTEGER NOT NULL DEFAULT 0,
  `username` varchar(32) NOT NULL DEFAULT '',
  `event` varchar(40) NOT NULL DEFAULT '',
  `ncpus` INTEGER NOT NULL DEFAULT 0,
  `venue` varchar(80) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT NULL,
  `heartbeat` datetime NOT NULL DEFAULT NULL,
  `active` INTEGER NOT NULL DEFAULT 1,
  `jobtoken` varchar(32) DEFAULT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS `uidx_jobid` ON `job` (`jobid`);
CREATE INDEX IF NOT EXISTS `idx_start` ON `job` (`start`);
CREATE INDEX IF NOT EXISTS `idx_heartbeat` ON `job` (`heartbeat`);
CREATE INDEX IF NOT EXISTS `idx_username_jobtoken` ON `job` (`username`,`jobtoken`);

--
-- Table: `joblog`
--

CREATE TABLE `joblog` (
  `sessnum` INTEGER NOT NULL DEFAULT 0,
  `job` INTEGER NOT NULL DEFAULT 0,
  `superjob` INTEGER NOT NULL DEFAULT 0,
  `event` varchar(40) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT NULL,
  `walltime` double DEFAULT 0,
  `cputime` double DEFAULT 0,
  `ncpus` INTEGER NOT NULL DEFAULT 0,
  `status` INTEGER DEFAULT 0,
  `venue` varchar(80) NOT NULL DEFAULT '',
  `zone_id` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`sessnum`,`job`,`event`,`venue`)
);

CREATE INDEX IF NOT EXISTS `idx_sessnum` ON `joblog` (`sessnum`);
CREATE INDEX IF NOT EXISTS `idx_event` ON `joblog` (`event`);

--
-- Table: `session`
--

CREATE TABLE `session` (
  `sessnum` INTEGER PRIMARY KEY AUTOINCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '',
  `remoteip` varchar(40) NOT NULL DEFAULT '',
  `exechost` varchar(40) NOT NULL DEFAULT '',
  `dispnum` INTEGER DEFAULT 0,
  `start` datetime NOT NULL DEFAULT NULL,
  `accesstime` datetime NOT NULL DEFAULT NULL,
  `timeout` INTEGER DEFAULT 86400,
  `appname` varchar(80) NOT NULL DEFAULT '',
  `sessname` varchar(100) NOT NULL DEFAULT '',
  `sesstoken` varchar(32) NOT NULL DEFAULT '',
  `params` text DEFAULT NULL,
  `zone_id` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `sessionlog`
--

CREATE TABLE `sessionlog` (
  `sessnum` INTEGER PRIMARY KEY AUTOINCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '',
  `remoteip` varchar(40) NOT NULL DEFAULT '',
  `remotehost` varchar(40) NOT NULL DEFAULT '',
  `exechost` varchar(40) NOT NULL DEFAULT '',
  `dispnum` INTEGER DEFAULT 0,
  `start` datetime NOT NULL DEFAULT NULL,
  `appname` varchar(80) NOT NULL DEFAULT '',
  `walltime` double DEFAULT 0,
  `viewtime` double DEFAULT 0,
  `cputime` double DEFAULT 0,
  `status` INTEGER DEFAULT 0,
  `zone_id` INTEGER NOT NULL DEFAULT 0
);

--
-- Table: `sessionpriv`
--

CREATE TABLE `sessionpriv` (
  `privid` INTEGER PRIMARY KEY AUTOINCREMENT,
  `sessnum` INTEGER NOT NULL DEFAULT 0,
  `privilege` varchar(40) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT NULL
);

--
-- Table: `venue`
--

CREATE TABLE `venue` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `venue` varchar(40) DEFAULT NULL,
  `state` varchar(15) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `mw_version` varchar(3) DEFAULT NULL,
  `ssh_key_path` varchar(200) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `master` varchar(255) DEFAULT NULL
);

--
-- Table: `view`
--

CREATE TABLE `view` (
  `viewid` INTEGER PRIMARY KEY AUTOINCREMENT,
  `sessnum` INTEGER NOT NULL DEFAULT 0,
  `username` varchar(32) NOT NULL DEFAULT '',
  `remoteip` varchar(40) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT NULL,
  `heartbeat` datetime NOT NULL DEFAULT NULL
);

--
-- Table: `viewlog`
--

CREATE TABLE `viewlog` (
  `sessnum` INTEGER NOT NULL DEFAULT 0,
  `username` varchar(32) NOT NULL DEFAULT '',
  `remoteip` varchar(40) NOT NULL DEFAULT '',
  `remotehost` varchar(40) NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT NULL,
  `duration` float DEFAULT 0
);

--
-- Table: `viewperm`
--

CREATE TABLE `viewperm` (
  `sessnum` INTEGER NOT NULL DEFAULT 0,
  `viewuser` varchar(32) NOT NULL DEFAULT '',
  `viewtoken` varchar(32) NOT NULL DEFAULT '',
  `geometry` varchar(9) NOT NULL DEFAULT '0',
  `fwhost` varchar(40) NOT NULL DEFAULT '',
  `fwport` INTEGER NOT NULL DEFAULT 0,
  `vncpass` varchar(16) NOT NULL DEFAULT '',
  `readonly` varchar(4) NOT NULL DEFAULT 'Yes',
  PRIMARY KEY (`sessnum`,`viewuser`)
);

--
-- Table: `zone_locations`
--

CREATE TABLE `zone_locations` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `zone_id` INTEGER NOT NULL,
  `ipFROM` INTEGER zerofill NOT NULL DEFAULT 0000000000,
  `ipTO` INTEGER zerofill NOT NULL DEFAULT 0000000000,
  `continent` char(2) NOT NULL,
  `countrySHORT` char(2) NOT NULL,
  `countryLONG` varchar(64) NOT NULL,
  `ipREGION` varchar(128) NOT NULL,
  `ipCITY` varchar(128) NOT NULL,
  `ipLATITUDE` double DEFAULT NULL,
  `ipLONGITUDE` double DEFAULT NULL,
  `notes` varchar(128) DEFAULT NULL
);

--
-- Table: `zones`
--

CREATE TABLE `zones` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `zone` varchar(40) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `state` varchar(15) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `master` varchar(255) DEFAULT NULL,
  `mw_version` varchar(3) DEFAULT NULL,
  `ssh_key_path` varchar(200) DEFAULT NULL
);
--
-- Temporary table structure for view `#__contributor_ids_view`
--
--
-- Temporary table structure for view `#__contributors_view`
--
--
-- Temporary table structure for view `#__courses_form_latest_responses_view`
--
--
-- Temporary table structure for view `#__resource_contributors_view`
--
--
-- Temporary table structure for view `#__wiki_contributors_view`
--
--
-- Final view structure for view `#__contributor_ids_view`
--
--
-- Final view structure for view `#__contributors_view`
--
--
-- Final view structure for view `#__courses_form_latest_responses_view`
--
--
-- Final view structure for view `#__resource_contributors_view`
--
--
-- Final view structure for view `#__wiki_contributors_view`
--

--
-- View: `#__courses_form_latest_responses_view`
--

DROP VIEW IF EXISTS `#__courses_form_latest_responses_view`;

CREATE VIEW `#__courses_form_latest_responses_view` AS
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
-- View: `#__resource_contributors_view`
--

DROP VIEW IF EXISTS `#__resource_contributors_view`;

CREATE VIEW `#__resource_contributors_view` AS
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
-- View: `#__wiki_contributors_view`
--

DROP VIEW IF EXISTS `#__wiki_contributors_view`;

CREATE VIEW `#__wiki_contributors_view` AS
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

--
-- View: `#__contributor_ids_view`
--

DROP VIEW IF EXISTS `#__contributor_ids_view`;

CREATE VIEW `#__contributor_ids_view` AS
  SELECT `#__resource_contributors_view`.`uidnumber` AS `uidnumber`
  FROM   `#__resource_contributors_view`
  UNION
  SELECT `#__wiki_contributors_view`.`uidnumber` AS `uidnumber`
  FROM   `#__wiki_contributors_view`;

--
-- View: `#__contributors_view`
--

DROP VIEW IF EXISTS `#__contributors_view`;

CREATE VIEW `#__contributors_view` AS
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
