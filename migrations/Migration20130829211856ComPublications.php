<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130829211856ComPublications extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$queries = array();

		if (!$db->tableExists('#__publications'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publications` (
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
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__publication_access'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_access` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`publication_version_id` int(11) NOT NULL DEFAULT '0',
				`group_id` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__publication_attachments'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_attachments` (
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
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__publication_audience'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_audience` (
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
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__publication_audience_levels'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_audience_levels` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`label` varchar(11) NOT NULL DEFAULT '0',
				`title` varchar(100) DEFAULT '',
				`description` varchar(255) DEFAULT '',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";

			// Set audience level defaults
			$queries[] = "INSERT INTO `#__publication_audience_levels` (`label`,`title`,`description`) 
						  VALUES ('level0','K12','Middle/High School')";
			$queries[] = "INSERT INTO `#__publication_audience_levels` (`label`,`title`,`description`) 
						  VALUES ('level1','Easy','Freshmen/Sophomores')";
			$queries[] = "INSERT INTO `#__publication_audience_levels` (`label`,`title`,`description`) 
						  VALUES ('level2','Intermediate','Juniors/Seniors')";
			$queries[] = "INSERT INTO `#__publication_audience_levels` (`label`,`title`,`description`) 
				    	  VALUES ('level3','Advanced','Graduate Students')";
			$queries[] = "INSERT INTO `#__publication_audience_levels` (`label`,`title`,`description`) 
						  VALUES ('level4','Expert','PhD Experts')";
			$queries[] = "INSERT INTO `#__publication_audience_levels` (`label`,`title`,`description`) 
						  VALUES ('level5','Professional','Beyond PhD')";
		}

		if (!$db->tableExists('#__publication_authors'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_authors` (
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
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__publication_categories'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_categories` (
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
				UNIQUE KEY `type` (`name`),
				UNIQUE KEY `alias` (`alias`),
				UNIQUE KEY `url_alias` (`url_alias`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";

			$queries[] = "INSERT INTO `#__publication_categories` (`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('Datasets','Dataset','dataset','datasets','A collection of research data','1','1','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1\nplg_wishlist=1\nplg_citations=1\nplg_usage = 1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('Workshops','Event','workshop','workshops','A collection of lectures, seminars, and materials that were presented at a workshop.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('Publications','Dataset','publication','publications','A publication is a paper relevant to the community that has been published in some manner.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('Learning Modules','InteractiveResource','learning module','learningmodules','A combination of presentations, tools, assignments, etc. geared toward teaching a specific concept.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('Animations','MovingImage','animation','animations','An animation is a Flash-based demo or short movie that illustrates some concept.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('Courses','Collection','course','courses','University courses that make videos of lectures and associated teaching materials available.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('Tools','Software','tool','tools','A simulation tool is software that allows users to run a specific type of calculation.','0','1','poweredby=Powered by=textarea=0\nbio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('Downloads','PhysicalObject','download','downloads','A download is a type of resource that users can download and use on their own computer.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('Notes','Text','note','notes','Notes are typically a category for any resource that might not fit any of the other categories.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('Series','Collection','series','series','Series are collections of other resources, typically online presentations, that cover a specific topic.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('Teaching Materials','Text','teaching material','teachingmaterials','Supplementary materials (study notes, guides, etc.) that don\'t quite fit into any of the other categories.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
		}

		if (!$db->tableExists('#__publication_master_types'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_master_types` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `type` varchar(200) NOT NULL DEFAULT '',
			  `alias` varchar(200) NOT NULL DEFAULT '',
			  `description` tinytext,
			  `contributable` int(2) DEFAULT '0',
			  `supporting` int(2) DEFAULT '0',
			  `ordering` int(2) DEFAULT '0',
			  `params` text,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `alias` (`alias`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";

			$queries[] = "INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) 
						  VALUES ('File(s)','files','uploaded material','1','1','1','peer_review=1')";
			$queries[] = "INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) 
						  VALUES ('Link','links','external content','0','0','3','')";
			$queries[] = "INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) 
						  VALUES ('Wiki','notes','from project notes','0','0','5','')";
			$queries[] = "INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) 
						  VALUES ('Application','apps','simulation tool','0','0','4','')";
			$queries[] = "INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) 
						  VALUES ('Series','series','publication collection','0','0','6','')";
			$queries[] = "INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) 
						  VALUES ('Gallery','gallery','image/photo gallery','0','0','7','')";
			$queries[] = "INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) 
						  VALUES ('Databases','databases','project database','0','0','2','')";
		}

		if (!$db->tableExists('#__publication_ratings'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_ratings` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`publication_id` int(11) NOT NULL DEFAULT '0',
				`publication_version_id` int(11) NOT NULL DEFAULT '0',
				`created_by` int(11) NOT NULL DEFAULT '0',
				`rating` decimal(2,1) NOT NULL DEFAULT '0.0',
				`comment` text NOT NULL,
				`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`anonymous` tinyint(3) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__publication_screenshots'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_screenshots` (
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
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__publication_stats'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_stats` (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`publication_id` bigint(20) NOT NULL,
				`publication_version` tinyint(4) DEFAULT NULL,
				`users` bigint(20) DEFAULT NULL,
				`downloads` bigint(20) DEFAULT NULL,
				`datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`period` tinyint(4) NOT NULL DEFAULT '-1',
				`processed_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				UNIQUE KEY `id` (`id`),
				UNIQUE KEY `pub_stats` (`publication_id`,`datetime`,`period`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__publication_versions'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_versions` (
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
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		if (!$db->tableExists('#__publication_licenses'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_licenses` (
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
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";

			$queries[] = "INSERT INTO `#__publication_licenses` (`name`,`text`,`title`,`url`,`info`,`ordering`,`active`,`apps_only`,`main`,`agreement`,`customizable`,`icon`) VALUES ('custom','[ONE LINE DESCRIPTION]\r\nCopyright (C) [YEAR] [OWNER]','Custom','http://creativecommons.org/about/cc0','Custom license','3','1','0','0','0','1','/components/com_publications/assets/img/logos/license.gif')";
			$queries[] = "INSERT INTO `#__publication_licenses` (`name`,`text`,`title`,`url`,`info`,`ordering`,`active`,`apps_only`,`main`,`agreement`,`customizable`,`icon`) VALUES ('cc','','CC0 - Creative Commons','http://creativecommons.org/about/cc0','CC0 enables scientists, educators, artists and other creators and owners of copyright- or database-protected content to waive those interests in their works and thereby place them as completely as possible in the public domain, so that others may freely build upon, enhance and reuse the works for any purposes without restriction under copyright or database law.','2','1','0','1','1','0','/components/com_publications/assets/img/logos/cc.gif')";
			$queries[] = "INSERT INTO `#__publication_licenses` (`name`,`text`,`title`,`url`,`info`,`ordering`,`active`,`apps_only`,`main`,`agreement`,`customizable`,`icon`) VALUES ('standard','All rights reserved.','Standard HUB License','http://nanohub.org','Standard HUB license.','1','0','0','0','0','0','/components/com_publications/images/logos/license.gif')";
		}

		if (count($queries) > 0)
		{
			// Run queries
			foreach ($queries as $query)
			{
				$db->setQuery($query);
				$db->query();
			}
		}

		$params = array(
			"enabled" => "1",
			"autoapprove" => "1",
			"autoapproved_users" => "",
			"email" => "0",
			"default_category" => "dataset",
			"defaultpic" => "/components/com_publications/assets/img/resource_thumb.gif",
			"toolpic" => "/components/com_publications/assets/img/tool_thumb.gif",
			"video_thumb" => "/components/com_publications/images/video_thumb.gif",
			"gallery_thumb" => "/components/com_publications/images/gallery_thumb.gif",
			"webpath" => "/site/publications",
			"aboutdoi" => "",
			"doi_shoulder" => "",
			"doi_prefix" => "",
			"doi_service" => "",
			"doi_userpw" => "",
			"doi_xmlschema" => "",
			"doi_publisher" => "",
			"doi_resolve" => "http://dx.doi.org/",
			"doi_verify" => "http://n2t.net/ezid/id/",
			"supportedtag" => "",
			"supportedlink" => "",
			"google_id" => "",
			"show_authors" => "1",
			"show_ranking" => "1",
			"show_rating" => "1",
			"show_date" => "3",
			"show_citation" => "1",
			"panels" => "content, description, authors, audience, gallery, tags, access, license, notes",
			"suggest_licence" => "0",
			"show_tags" => "1",
			"show_metadata" => "1",
			"show_notes" => "1",
			"show_license" => "1",
			"show_access" => "0",
			"show_gallery" => "1",
			"show_audience" => "0",
			"audiencelink" => "",
			"documentation" => "/kb/publications",
			"deposit_terms" => "/legal/termsofdeposit",
			"dbcheck" => "0",
			"repository" => "0",
			"aip_path" => "/srv/AIP"
		);

		self::addComponentEntry('Publications', 'com_publications', 1, $params);

		self::addPluginEntry('publications', 'related');
		self::addPluginEntry('publications', 'recommendations');
		self::addPluginEntry('publications', 'supportingdocs');
		self::addPluginEntry('publications', 'versions');
		self::addPluginEntry('publications', 'questions');
		self::addPluginEntry('publications', 'citations');
		self::addPluginEntry('publications', 'usage');
		self::addPluginEntry('publications', 'share');
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$queries = array();

		if ($db->tableExists('#__publications'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publications`";
		}

		if ($db->tableExists('#__publication_access'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_access`";
		}

		if ($db->tableExists('#__publication_attachments'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_attachments`";
		}

		if ($db->tableExists('#__publication_audience'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_audience`";
		}

		if ($db->tableExists('#__publication_audience_levels'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_audience_levels`";
		}

		if ($db->tableExists('#__publication_authors'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_authors`";
		}

		if ($db->tableExists('#__publication_categories'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_categories`";
		}

		if ($db->tableExists('#__publication_master_types'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_master_types`";
		}

		if ($db->tableExists('#__publication_ratings'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_ratings`";
		}

		if ($db->tableExists('#__publication_screenshots'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_screenshots`";
		}

		if ($db->tableExists('#__publication_stats'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_stats`";
		}

		if ($db->tableExists('#__publication_versions'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_versions`";
		}

		if ($db->tableExists('#__publication_licenses'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_licenses`";
		}

		if (count($queries) > 0)
		{
			// Run queries
			foreach ($queries as $query)
			{
				$db->setQuery($query);
				$db->query();
			}
		}

		self::deleteComponentEntry('Publications');

		self::deletePluginEntry('publications');
	}
}