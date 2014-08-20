<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for 2011/12 create table statements
 **/
class Migration20120101000001Core extends Base
{
	public function up()
	{
		if (!$this->db->tableExists('#__author_role_types'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__author_role_types` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `role_id` INT(11) NOT NULL DEFAULT '0' ,
				  `type_id` INT(11) NOT NULL DEFAULT '0' ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__author_roles'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__author_roles` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `title` VARCHAR(255) NULL DEFAULT NULL ,
				  `alias` VARCHAR(255) NULL DEFAULT NULL ,
				  `state` TINYINT(3) NOT NULL DEFAULT '0' ,
				  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  `created_by` INT(11) NOT NULL DEFAULT '0' ,
				  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  `modified_by` INT(11) NOT NULL DEFAULT '0' ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__billboard_collection'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__billboard_collection` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `name` VARCHAR(255) CHARACTER SET 'latin1' NULL DEFAULT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__billboards'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__billboards` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `collection_id` INT(11) NULL DEFAULT NULL ,
				  `name` VARCHAR(255) NULL DEFAULT NULL ,
				  `header` VARCHAR(255) NULL DEFAULT NULL ,
				  `text` TEXT NULL DEFAULT NULL ,
				  `learn_more_text` VARCHAR(255) NULL DEFAULT NULL ,
				  `learn_more_target` VARCHAR(255) NULL DEFAULT NULL ,
				  `learn_more_class` VARCHAR(255) NULL DEFAULT NULL ,
				  `learn_more_location` VARCHAR(255) NULL DEFAULT NULL ,
				  `background_img` VARCHAR(255) NULL DEFAULT NULL ,
				  `padding` VARCHAR(255) NULL DEFAULT NULL ,
				  `alias` VARCHAR(255) NULL DEFAULT NULL ,
				  `css` TEXT NULL DEFAULT NULL ,
				  `published` TINYINT(1) NULL DEFAULT '0' ,
				  `ordering` INT(11) NULL DEFAULT NULL ,
				  `checked_out` INT(11) NULL DEFAULT '0' ,
				  `checked_out_time` DATETIME NULL DEFAULT '0000-00-00 00:00:00' ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__citations_sponsors'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__citations_sponsors` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `sponsor` VARCHAR(150) NULL DEFAULT NULL ,
				  `link` VARCHAR(200) NULL DEFAULT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__citations_sponsors_assoc'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__citations_sponsors_assoc` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `cid` INT(11) NULL DEFAULT NULL ,
				  `sid` INT(11) NULL DEFAULT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__citations_types'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__citations_types` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `type` VARCHAR(255) NULL DEFAULT NULL ,
				  `type_title` VARCHAR(255) NULL DEFAULT NULL ,
				  `type_desc` TEXT NULL DEFAULT NULL ,
				  `type_export` VARCHAR(255) NULL DEFAULT NULL ,
				  `fields` TEXT NULL DEFAULT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__document_resource_rel'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__document_resource_rel` (
				  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `document_id` INT(11) NOT NULL ,
				  `resource_id` INT(11) NOT NULL ,
				  PRIMARY KEY (`id`) ,
				  UNIQUE INDEX `id` (`id` ASC) ,
				  UNIQUE INDEX `jos_document_resource_rel_document_id_resource_id_uidx` (`document_id` ASC, `resource_id` ASC) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__document_text_data'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__document_text_data` (
				  `id` INT(11) NOT NULL AUTO_INCREMENT ,
				  `body` TEXT NULL DEFAULT NULL ,
				  `hash` CHAR(40) NOT NULL ,
				  PRIMARY KEY (`id`) ,
				  UNIQUE INDEX `jos_document_text_data_hash_uidx` (`hash` ASC) ,
				  FULLTEXT INDEX `jos_document_text_data_body_ftidx` (`body` ASC) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__focus_area_resource_type_rel'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__focus_area_resource_type_rel` (
				  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `focus_area_id` INT(11) NOT NULL ,
				  `resource_type_id` INT(11) NOT NULL ,
				  PRIMARY KEY (`id`) ,
				  UNIQUE INDEX `id` (`id` ASC) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__focus_areas'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__focus_areas` (
				  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `tag_id` INT(11) NOT NULL ,
				  `mandatory_depth` INT(11) NULL DEFAULT NULL ,
				  `multiple_depth` INT(11) NULL DEFAULT NULL ,
				  PRIMARY KEY (`id`) ,
				  UNIQUE INDEX `id` (`id` ASC) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__forum_attachments'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__forum_attachments` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `parent` INT(11) NOT NULL DEFAULT '0' ,
				  `post_id` INT(11) NOT NULL DEFAULT '0' ,
				  `filename` VARCHAR(255) NULL DEFAULT NULL ,
				  `description` VARCHAR(255) NULL DEFAULT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__forum_categories'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__forum_categories` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `title` VARCHAR(255) NULL DEFAULT NULL ,
				  `alias` VARCHAR(255) NULL DEFAULT NULL ,
				  `description` TEXT NULL DEFAULT NULL ,
				  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  `created_by` INT(11) NOT NULL DEFAULT '0' ,
				  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  `modified_by` INT(11) NOT NULL DEFAULT '0' ,
				  `access` TINYINT(2) NOT NULL DEFAULT '0' ,
				  `state` TINYINT(3) NOT NULL DEFAULT '0' ,
				  `group_id` INT(11) NOT NULL DEFAULT '0' ,
				  `section_id` INT(11) NOT NULL DEFAULT '0' ,
				  `closed` TINYINT(2) NOT NULL DEFAULT '0' ,
				  `asset_id` INT(11) NOT NULL DEFAULT '0' ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__forum_posts'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__forum_posts` (
				  `id` INT(11) NOT NULL AUTO_INCREMENT ,
				  `category_id` INT(11) NOT NULL DEFAULT '0' ,
				  `title` VARCHAR(255) NULL DEFAULT NULL ,
				  `comment` TEXT NULL DEFAULT NULL ,
				  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  `created_by` INT(11) NOT NULL DEFAULT '0' ,
				  `state` TINYINT(3) NOT NULL DEFAULT '0' ,
				  `sticky` TINYINT(2) NOT NULL DEFAULT '0' ,
				  `parent` INT(11) NOT NULL DEFAULT '0' ,
				  `hits` INT(11) NOT NULL DEFAULT '0' ,
				  `group_id` INT(11) NOT NULL DEFAULT '0' ,
				  `access` TINYINT(2) NOT NULL DEFAULT '0' ,
				  `anonymous` TINYINT(2) NOT NULL DEFAULT '0' ,
				  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  `modified_by` INT(11) NOT NULL DEFAULT '0' ,
				  `last_activity` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  `asset_id` INT(11) NOT NULL DEFAULT '0' ,
				  PRIMARY KEY (`id`) ,
				  FULLTEXT INDEX `question` (`comment` ASC) ,
				  FULLTEXT INDEX `comment_title_fidx` (`comment` ASC, `title` ASC) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__forum_sections'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__forum_sections` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `title` VARCHAR(255) NULL DEFAULT NULL ,
				  `alias` VARCHAR(255) NULL DEFAULT NULL ,
				  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  `created_by` INT(11) NOT NULL DEFAULT '0' ,
				  `access` TINYINT(2) NOT NULL DEFAULT '0' ,
				  `state` TINYINT(3) NOT NULL DEFAULT '0' ,
				  `group_id` INT(11) NOT NULL DEFAULT '0' ,
				  `asset_id` INT(11) NOT NULL DEFAULT '0' ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__incremental_registration_group_label_rel'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__incremental_registration_group_label_rel` (
				  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `group_id` INT(11) NOT NULL ,
				  `label_id` INT(11) NOT NULL ,
				  PRIMARY KEY (`id`) ,
				  UNIQUE INDEX `id` (`id` ASC) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__incremental_registration_groups'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__incremental_registration_groups` (
				  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `hours` INT(11) NOT NULL ,
				  PRIMARY KEY (`id`) ,
				  UNIQUE INDEX `id` (`id` ASC) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__incremental_registration_labels'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__incremental_registration_labels` (
				  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `field` VARCHAR(50) NOT NULL ,
				  `label` VARCHAR(100) NOT NULL ,
				  PRIMARY KEY (`id`) ,
				  UNIQUE INDEX `id` (`id` ASC) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__incremental_registration_options'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__incremental_registration_options` (
				  `added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
				  `popover_text` TEXT NOT NULL ,
				  `award_per` INT(11) NOT NULL ,
				  `test_group` INT(11) NOT NULL )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__incremental_registration_popover_recurrence'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__incremental_registration_popover_recurrence` (
				  `idx` INT(11) NOT NULL ,
				  `hours` INT(11) NOT NULL )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__metrics_ipgeo_cache'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__metrics_ipgeo_cache` (
				  `ip` INT(10) NOT NULL DEFAULT '0' ,
				  `countrySHORT` CHAR(2) NOT NULL DEFAULT '' ,
				  `countryLONG` VARCHAR(64) NOT NULL DEFAULT '' ,
				  `ipREGION` VARCHAR(128) NOT NULL DEFAULT '' ,
				  `ipCITY` VARCHAR(128) NOT NULL DEFAULT '' ,
				  `ipLATITUDE` DOUBLE NULL DEFAULT NULL ,
				  `ipLONGITUDE` DOUBLE NULL DEFAULT NULL ,
				  `lookup_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
				  PRIMARY KEY (`ip`) ,
				  INDEX `lookup_datetime` (`lookup_datetime` ASC) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__oauthp_consumers'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__oauthp_consumers` (
				  `id` INT(11) NOT NULL AUTO_INCREMENT ,
				  `state` TINYINT(4) NOT NULL ,
				  `token` VARCHAR(250) NOT NULL ,
				  `secret` VARCHAR(250) NOT NULL ,
				  `callback_url` VARCHAR(250) NOT NULL ,
				  `xauth` TINYINT(4) NOT NULL ,
				  `xauth_grant` TINYINT(4) NOT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__oauthp_nonces'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__oauthp_nonces` (
				  `id` INT(11) NOT NULL AUTO_INCREMENT ,
				  `nonce` VARCHAR(250) NOT NULL ,
				  `stamp` INT(11) NOT NULL ,
				  `created` DATETIME NOT NULL ,
				  PRIMARY KEY (`id`) ,
				  UNIQUE INDEX `unonce` (`nonce` ASC, `stamp` ASC) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__oauthp_tokens'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__oauthp_tokens` (
				  `id` INT(11) NOT NULL AUTO_INCREMENT ,
				  `consumer_id` INT(11) NOT NULL ,
				  `user_id` INT(11) NOT NULL ,
				  `state` TINYINT(4) NOT NULL ,
				  `token` VARCHAR(250) NOT NULL ,
				  `token_secret` VARCHAR(250) NOT NULL ,
				  `callback_url` VARCHAR(250) NOT NULL ,
				  `verifier` VARCHAR(250) NOT NULL ,
				  `created` DATETIME NOT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__profile_completion_awards'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__profile_completion_awards` (
				  `user_id` INT(11) NOT NULL ,
				  `name` TINYINT(4) NOT NULL DEFAULT '0' ,
				  `orgtype` TINYINT(4) NOT NULL DEFAULT '0' ,
				  `organization` TINYINT(4) NOT NULL DEFAULT '0' ,
				  `countryresident` TINYINT(4) NOT NULL DEFAULT '0' ,
				  `countryorigin` TINYINT(4) NOT NULL DEFAULT '0' ,
				  `gender` TINYINT(4) NOT NULL DEFAULT '0' ,
				  `url` TINYINT(4) NOT NULL DEFAULT '0' ,
				  `reason` TINYINT(4) NOT NULL DEFAULT '0' ,
				  `race` TINYINT(4) NOT NULL DEFAULT '0' ,
				  `phone` TINYINT(4) NOT NULL DEFAULT '0' ,
				  `picture` TINYINT(4) NOT NULL DEFAULT '0' ,
				  `opted_out` TINYINT(4) NOT NULL DEFAULT '0' ,
				  `logins` INT(11) NOT NULL DEFAULT '1' ,
				  `invocations` INT(11) NOT NULL DEFAULT '0' ,
				  `last_bothered` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
				  `bothered_times` INT(11) NOT NULL DEFAULT '0' ,
				  `edited_profile` TINYINT(4) NOT NULL DEFAULT '0' ,
				  PRIMARY KEY (`user_id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__recommendation'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__recommendation` (
				  `fromID` INT(11) NOT NULL ,
				  `toID` INT(11) NOT NULL ,
				  `contentScore` FLOAT(10) UNSIGNED ZEROFILL NULL DEFAULT NULL ,
				  `tagScore` FLOAT(10) UNSIGNED ZEROFILL NULL DEFAULT NULL ,
				  `titleScore` FLOAT(10) UNSIGNED ZEROFILL NULL DEFAULT NULL ,
				  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
				  PRIMARY KEY (`fromID`, `toID`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__resource_licenses'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__resource_licenses` (
				  `id` INT(11) NOT NULL AUTO_INCREMENT ,
				  `name` VARCHAR(100) NULL DEFAULT NULL ,
				  `text` TEXT NULL DEFAULT NULL ,
				  `title` VARCHAR(100) NULL DEFAULT NULL ,
				  `ordering` INT(11) NOT NULL DEFAULT '0' ,
				  `apps_only` TINYINT(3) NOT NULL DEFAULT '0' ,
				  `main` VARCHAR(255) NULL DEFAULT NULL ,
				  `icon` VARCHAR(255) NULL DEFAULT NULL ,
				  `url` VARCHAR(255) NULL DEFAULT NULL ,
				  `agreement` TINYINT(2) NOT NULL DEFAULT '0' ,
				  `info` TEXT NULL DEFAULT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__resource_sponsors'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__resource_sponsors` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `alias` VARCHAR(255) NULL DEFAULT NULL ,
				  `title` VARCHAR(255) NULL DEFAULT NULL ,
				  `state` TINYINT(3) NOT NULL DEFAULT '1' ,
				  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  `created_by` INT(11) NOT NULL DEFAULT '0' ,
				  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  `modified_by` INT(11) NOT NULL DEFAULT '0' ,
				  `description` TEXT NULL DEFAULT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__resource_stats_clusters'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__resource_stats_clusters` (
				  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `cluster` VARCHAR(255) NOT NULL DEFAULT '' ,
				  `username` VARCHAR(32) NOT NULL DEFAULT '' ,
				  `uidNumber` INT(11) NOT NULL DEFAULT '0' ,
				  `toolname` VARCHAR(80) NOT NULL DEFAULT '' ,
				  `resid` INT(11) NOT NULL DEFAULT '0' ,
				  `clustersize` VARCHAR(255) NOT NULL DEFAULT '' ,
				  `cluster_start` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  `cluster_end` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  `institution` VARCHAR(255) NOT NULL DEFAULT '' ,
				  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
				  PRIMARY KEY (`id`) ,
				  INDEX `cluster` (`cluster` ASC) ,
				  INDEX `username` (`username` ASC) ,
				  INDEX `uidNumber` (`uidNumber` ASC) ,
				  INDEX `toolname` (`toolname` ASC) ,
				  INDEX `resid` (`resid` ASC) ,
				  INDEX `clustersize` (`clustersize` ASC) ,
				  INDEX `cluster_start` (`cluster_start` ASC) ,
				  INDEX `cluster_end` (`cluster_end` ASC) ,
				  INDEX `institution` (`institution` ASC) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__session_geo'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__session_geo` (
				  `session_id` VARCHAR(200) NOT NULL DEFAULT '0' ,
				  `username` VARCHAR(150) NULL DEFAULT '' ,
				  `time` VARCHAR(14) NULL DEFAULT '' ,
				  `guest` TINYINT(4) NULL DEFAULT '1' ,
				  `userid` INT(11) NULL DEFAULT '0' ,
				  `ip` VARCHAR(15) NULL DEFAULT NULL ,
				  `host` VARCHAR(128) NULL DEFAULT NULL ,
				  `domain` VARCHAR(128) NULL DEFAULT NULL ,
				  `signed` TINYINT(3) NULL DEFAULT '0' ,
				  `countrySHORT` CHAR(2) NULL DEFAULT NULL ,
				  `countryLONG` VARCHAR(64) NULL DEFAULT NULL ,
				  `ipREGION` VARCHAR(128) NULL DEFAULT NULL ,
				  `ipCITY` VARCHAR(128) NULL DEFAULT NULL ,
				  `ipLATITUDE` DOUBLE NULL DEFAULT NULL ,
				  `ipLONGITUDE` DOUBLE NULL DEFAULT NULL ,
				  `bot` TINYINT(4) NULL DEFAULT '0' ,
				  PRIMARY KEY (`session_id`) ,
				  INDEX `userid` (`userid` ASC) ,
				  INDEX `time` (`time` ASC) ,
				  INDEX `ip` (`ip` ASC) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__session_log'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__session_log` (
				  `id` INT(11) NOT NULL AUTO_INCREMENT ,
				  `clientid` TINYINT(4) NULL DEFAULT NULL ,
				  `session_id` CHAR(64) NULL DEFAULT NULL ,
				  `psid` CHAR(64) NULL DEFAULT NULL ,
				  `rsid` CHAR(64) NULL DEFAULT NULL ,
				  `ssid` CHAR(64) NULL DEFAULT NULL ,
				  `user_id` INT(11) NULL DEFAULT NULL ,
				  `authenticator` CHAR(64) NULL DEFAULT NULL ,
				  `source` CHAR(64) NULL DEFAULT NULL ,
				  `ip` CHAR(64) NULL DEFAULT NULL ,
				  `created` DATETIME NULL DEFAULT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__support_queries'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__support_queries` (
				  `id` INT(11) NOT NULL AUTO_INCREMENT ,
				  `title` VARCHAR(250) NULL DEFAULT NULL ,
				  `conditions` TEXT NULL DEFAULT NULL ,
				  `query` TEXT NULL DEFAULT NULL ,
				  `user_id` INT(11) NOT NULL DEFAULT '0' ,
				  `sort` VARCHAR(100) NULL DEFAULT NULL ,
				  `sort_dir` VARCHAR(100) NULL DEFAULT NULL ,
				  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  `iscore` INT(3) NOT NULL DEFAULT '0' ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tags_log'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__tags_log` (
				  `id` INT(11) NOT NULL AUTO_INCREMENT ,
				  `tag_id` INT(11) NOT NULL DEFAULT '0' ,
				  `timestamp` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  `user_id` INT(11) NULL DEFAULT '0' ,
				  `action` VARCHAR(50) NULL DEFAULT NULL ,
				  `comments` TEXT NULL DEFAULT NULL ,
				  `actorid` INT(11) NULL DEFAULT '0' ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tags_substitute'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__tags_substitute` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `tag_id` INT(11) NOT NULL DEFAULT '0' ,
				  `tag` VARCHAR(100) NULL DEFAULT NULL ,
				  `raw_tag` VARCHAR(100) NULL DEFAULT NULL ,
				  `created_by` INT(11) NOT NULL DEFAULT '0' ,
				  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__user_roles'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__user_roles` (
				  `user_id` INT(11) NOT NULL ,
				  `role` VARCHAR(20) NOT NULL ,
				  `group_id` INT(11) NULL DEFAULT NULL ,
				  `id` INT(11) NOT NULL AUTO_INCREMENT ,
				  PRIMARY KEY (`id`) ,
				  UNIQUE INDEX `jos_user_roles_role_user_id_group_id_uidx` (`role` ASC, `user_id` ASC, `group_id` ASC) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wiki_page_author'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__wiki_page_author` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `user_id` INT(11) NULL DEFAULT '0' ,
				  `page_id` INT(11) NULL DEFAULT '0' ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__wiki_page_metrics'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__wiki_page_metrics` (
				  `pageid` INT(11) NOT NULL DEFAULT '0' ,
				  `pagename` VARCHAR(100) NULL DEFAULT NULL ,
				  `hits` INT(11) NOT NULL DEFAULT '0' ,
				  `visitors` INT(11) NOT NULL DEFAULT '0' ,
				  `visits` INT(11) NOT NULL DEFAULT '0' ,
				  PRIMARY KEY (`pageid`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_memberoption'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__xgroups_memberoption` (
				  `id` INT(11) NOT NULL AUTO_INCREMENT ,
				  `gidNumber` INT(11) NULL DEFAULT NULL ,
				  `userid` INT(11) NULL DEFAULT NULL ,
				  `optionname` VARCHAR(100) NULL DEFAULT NULL ,
				  `optionvalue` VARCHAR(100) NULL DEFAULT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_pages_hits'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__xgroups_pages_hits` (
				  `id` INT(11) NOT NULL AUTO_INCREMENT ,
				  `gid` INT(11) NULL DEFAULT NULL ,
				  `pid` INT(11) NULL DEFAULT NULL ,
				  `uid` INT(11) NULL DEFAULT NULL ,
				  `datetime` DATETIME NULL DEFAULT NULL ,
				  `ip` VARCHAR(15) NULL DEFAULT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xorganization_types'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__xorganization_types` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
				  `type` VARCHAR(150) NULL DEFAULT NULL ,
				  `title` VARCHAR(255) NULL DEFAULT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}