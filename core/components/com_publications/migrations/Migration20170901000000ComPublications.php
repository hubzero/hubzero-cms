<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing publications tables
 **/
class Migration20170901000000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__publications'))
		{
			$query = "CREATE TABLE `#__publications` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `category` int(11) NOT NULL DEFAULT '0',
			  `master_type` int(11) NOT NULL DEFAULT '1',
			  `project_id` int(11) NOT NULL DEFAULT '0',
			  `access` int(11) NOT NULL DEFAULT '0',
			  `checked_out` int(11) NOT NULL DEFAULT '0',
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `checked_out_time` datetime DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
			  `times_rated` int(11) NOT NULL DEFAULT '0',
			  `alias` varchar(100) NOT NULL DEFAULT '',
			  `ranking` float NOT NULL DEFAULT '0',
			  `group_owner` int(11) NOT NULL DEFAULT '0',
			  `master_doi` varchar(255) DEFAULT '',
			  `featured` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_master_type` (`master_type`),
			  KEY `idx_project_id` (`project_id`),
			  KEY `idx_category` (`category`),
			  KEY `idx_group_owner` (`group_owner`),
			  KEY `idx_access` (`access`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_versions'))
		{
			$query = "CREATE TABLE `#__publication_versions` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `publication_id` int(11) NOT NULL DEFAULT '0',
			  `main` int(1) NOT NULL DEFAULT '0',
			  `doi` varchar(255) DEFAULT '',
			  `state` int(1) NOT NULL DEFAULT '0',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `description` text NOT NULL,
			  `abstract` text NOT NULL,
			  `metadata` text,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `published_up` datetime DEFAULT NULL,
			  `published_down` datetime DEFAULT NULL,
			  `modified` datetime DEFAULT NULL,
			  `accepted` datetime DEFAULT NULL,
			  `archived` datetime DEFAULT NULL,
			  `submitted` datetime DEFAULT NULL,
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
			  `curator` int(11) DEFAULT NULL,
			  `curation_version_id` int(11) DEFAULT NULL,
			  `forked_from` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_publication_id` (`publication_id`),
			  KEY `idx_main` (`main`),
			  KEY `idx_state` (`state`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_version_number` (`version_number`),
			  FULLTEXT KEY `idx_fulltxt_title_description_abstract` (`title`,`description`,`abstract`),
			  FULLTEXT KEY `ftidx_title` (`title`),
			  FULLTEXT KEY `ftidx_abstract_description` (`abstract`,`description`),
			  FULLTEXT KEY `ftidx_title_abstract_description` (`title`,`abstract`,`description`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_stats'))
		{
			$query = "CREATE TABLE `#__publication_stats` (
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
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_screenshots'))
		{
			$query = "CREATE TABLE `#__publication_screenshots` (
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
			  PRIMARY KEY (`id`),
			  KEY `idx_publication_id` (`publication_id`),
			  KEY `idx_publication_version_id` (`publication_version_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_master_types'))
		{
			$query = "CREATE TABLE `#__publication_master_types` (
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
			  UNIQUE KEY `uidx_alias` (`alias`),
			  KEY `idx_contributable` (`contributable`),
			  KEY `idx_supporting` (`supporting`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_logs'))
		{
			$query = "CREATE TABLE `#__publication_logs` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `publication_id` int(11) NOT NULL,
			  `publication_version_id` int(11) NOT NULL,
			  `month` int(2) NOT NULL,
			  `year` int(2) NOT NULL,
			  `modified` datetime DEFAULT NULL,
			  `page_views` int(11) DEFAULT '0',
			  `primary_accesses` int(11) DEFAULT '0',
			  `support_accesses` int(11) DEFAULT '0',
			  `page_views_unfiltered` int(11) DEFAULT NULL,
			  `primary_accesses_unfiltered` int(11) DEFAULT NULL,
			  `page_views_unique` int(11) DEFAULT NULL,
			  `primary_accesses_unique` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_publication_id` (`publication_id`),
			  KEY `idx_publication_version_id` (`publication_version_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_licenses'))
		{
			$query = "CREATE TABLE `#__publication_licenses` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) NOT NULL,
			  `text` text,
			  `title` varchar(100) DEFAULT NULL,
			  `url` varchar(250) DEFAULT NULL,
			  `info` text,
			  `ordering` int(11) DEFAULT NULL,
			  `active` int(11) NOT NULL DEFAULT '0',
			  `main` int(11) NOT NULL DEFAULT '0',
			  `agreement` int(11) DEFAULT '0',
			  `customizable` int(11) DEFAULT '0',
			  `icon` varchar(250) DEFAULT NULL,
			  `opensource` tinyint(1) NOT NULL DEFAULT '0',
			  `restriction` varchar(100) DEFAULT NULL,
			  `derivatives` tinyint(2) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_active` (`active`),
			  KEY `idx_main` (`main`),
			  KEY `idx_agreement` (`agreement`),
			  KEY `idx_customizable` (`customizable`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_handlers'))
		{
			$query = "CREATE TABLE `#__publication_handlers` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) NOT NULL DEFAULT '',
			  `label` varchar(100) NOT NULL DEFAULT '',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `status` int(11) NOT NULL DEFAULT '0',
			  `about` text,
			  `params` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_handler_assoc'))
		{
			$query = "CREATE TABLE `#__publication_handler_assoc` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `publication_version_id` int(11) NOT NULL,
			  `element_id` int(11) NOT NULL,
			  `handler_id` int(11) NOT NULL,
			  `params` text NOT NULL,
			  `ordering` int(11) NOT NULL DEFAULT '1',
			  `status` tinyint(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_curation_versions'))
		{
			$query = "CREATE TABLE `#__publication_curation_versions` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `type_id` int(11) NOT NULL DEFAULT '0',
			  `version_number` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `curation` text NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_type_id_version_number` (`type_id`,`version_number`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_curation_history'))
		{
			$query = "CREATE TABLE `#__publication_curation_history` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `publication_version_id` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `changelog` text NOT NULL,
			  `curator` tinyint(3) NOT NULL DEFAULT '0',
			  `oldstatus` int(11) NOT NULL DEFAULT '0',
			  `newstatus` int(11) NOT NULL DEFAULT '0',
			  `comment` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_curation'))
		{
			$query = "CREATE TABLE `#__publication_curation` (
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
			  PRIMARY KEY (`id`),
			  KEY `idx_publication_id` (`publication_id`),
			  KEY `idx_publication_version_id` (`publication_version_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_categories'))
		{
			$query = "CREATE TABLE `#__publication_categories` (
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
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_blocks'))
		{
			$query = "CREATE TABLE `#__publication_blocks` (
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
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_authors'))
		{
			$query = "CREATE TABLE `#__publication_authors` (
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
			  `repository_contact` tinyint(2) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_publication_version_id` (`publication_version_id`),
			  KEY `idx_user_id` (`user_id`),
			  KEY `idx_project_owner_id` (`project_owner_id`),
			  KEY `idx_status` (`status`),
			  KEY `idx_repository_contact` (`repository_contact`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_audience'))
		{
			$query = "CREATE TABLE `#__publication_audience` (
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
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_audience_levels'))
		{
			$query = "CREATE TABLE `#__publication_audience_levels` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `label` varchar(11) NOT NULL DEFAULT '0',
			  `title` varchar(100) DEFAULT '',
			  `description` varchar(255) DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_attachments'))
		{
			$query = "CREATE TABLE `#__publication_attachments` (
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
			  PRIMARY KEY (`id`),
			  KEY `idx_publication_id` (`publication_id`),
			  KEY `idx_publication_version_id` (`publication_version_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__publication_access'))
		{
			$query = "CREATE TABLE `#__publication_access` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `publication_version_id` int(11) NOT NULL DEFAULT '0',
			  `group_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
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
		if ($this->db->tableExists('#__publications'))
		{
			$query = "DROP TABLE IF EXISTS `#__publications`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_versions'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_versions`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_stats'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_stats`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_screenshots'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_screenshots`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_master_types'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_master_types`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_logs'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_logs`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_licenses'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_licenses`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_handlers'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_handlers`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_handler_assoc'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_handler_assoc`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_curation_versions'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_curation_versions`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_curation_history'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_curation_history`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_curation'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_curation`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_categories'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_categories`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_blocks'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_blocks`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_authors'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_authors`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_audience'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_audience`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_audience_levels'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_audience_levels`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_attachments'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_attachments`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__publication_access'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_access`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
