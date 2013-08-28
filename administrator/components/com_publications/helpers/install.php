<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Publications install helper class
 */
class PubInstall extends JObject {
	
	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db = NULL;
	
	/**
	 * List of available database tables
	 * 
	 * @var array
	 */
	private $tables = NULL;
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */	
	public function __construct( &$db, $tables = array() )
	{
		$this->_db =& $db;
		$this->tables = $tables;
	}
	
	/**
	 * Run query
	 * 
	 * @return     void
	 */	
	public function runQuery( $query = '' ) 
	{
		if (!$query)
		{
			return false;
		}
		
		$this->_db->setQuery( $query );
		$this->_db->query();
	}
	
	/**
	 * Install J1.6 extension
	 * 
	 * @return     void
	 */	
	public function installExtension( $name = '', $type = '', $element = '', $folder = '', $ordering = 0, $params = '', $enabled = 1, $client_id = 0) 
	{
		$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)
		SELECT $name, $type, $element, $folder, $client_id, $enabled, 1, 0, null, $params, null, null, 0, '0000-00-00 00:00:00', $ordering, 0
		FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__extensions` WHERE name = '$name'";
		
		$this->runQuery($query);
	}
	
	/**
	 * Install project logs
	 * 
	 * @return     void
	 */	
	public function installLogs( ) 
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__publication_logs` (
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
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
		
		$this->runQuery($query);
	}
		
	/**
	 * Install publishing
	 * 
	 * @return     void
	 */	
	public function installPublishing( ) 
	{
		$queries 	= array();
		$prefix 	= $this->_db->getPrefix();
		$iniSetup 	= 0;
		
		// Access
		if (!in_array($prefix . 'publication_access', $this->tables)) 
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_access` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `publication_version_id` int(11) NOT NULL DEFAULT '0',
			  `group_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}
		
		// Attachments
		if (!in_array($prefix . 'publication_attachments', $this->tables)) 
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
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
		}
		
		// Audience records
		if (!in_array($prefix . 'publication_audience', $this->tables)) 
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
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
		}
		
		// Audience levels
		if (!in_array($prefix . 'publication_audience_levels', $this->tables)) 
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_audience_levels` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `label` varchar(11) NOT NULL DEFAULT '0',
			  `title` varchar(100) DEFAULT '',
			  `description` varchar(255) DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8";

			// Set audience level defaults
			$queries[] = "INSERT INTO `#__publication_audience_levels` (`id`,`label`,`title`,`description`) 
						  VALUES ('1','level0','K12','Middle/High School')";
			$queries[] = "INSERT INTO `#__publication_audience_levels` (`id`,`label`,`title`,`description`) 
						  VALUES ('2','level1','Easy','Freshmen/Sophomores')";
			$queries[] = "INSERT INTO `#__publication_audience_levels` (`id`,`label`,`title`,`description`) 
						  VALUES ('3','level2','Intermediate','Juniors/Seniors')";
			$queries[] = "INSERT INTO `#__publication_audience_levels` (`id`,`label`,`title`,`description`) 
				    	  VALUES ('4','level3','Advanced','Graduate Students')";
			$queries[] = "INSERT INTO `#__publication_audience_levels` (`id`,`label`,`title`,`description`) 
						  VALUES ('5','level4','Expert','PhD Experts')";
			$queries[] = "INSERT INTO `#__publication_audience_levels` (`id`,`label`,`title`,`description`) 
						  VALUES ('6','level5','Professional','Beyond PhD')";
		}
		
		// Authors
		if (!in_array($prefix . 'publication_authors', $this->tables)) 
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
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
		}
		
		// Categories
		if (!in_array($prefix . 'publication_categories', $this->tables)) 
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
			) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8";

			$queries[] = "INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('2','Workshops','Event','workshop','workshops','A collection of lectures, seminars, and materials that were presented at a workshop.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('3','Publications','Dataset','publication','publications','A publication is a paper relevant to the community that has been published in some manner.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('4','Learning Modules','InteractiveResource','learning module','learningmodules','A combination of presentations, tools, assignments, etc. geared toward teaching a specific concept.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('5','Animations','MovingImage','animation','animations','An animation is a Flash-based demo or short movie that illustrates some concept.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('6','Courses','Collection','course','courses','University courses that make videos of lectures and associated teaching materials available.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('7','Tools','Software','tool','tools','A simulation tool is software that allows users to run a specific type of calculation.','0','1','poweredby=Powered by=textarea=0\nbio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('9','Downloads','PhysicalObject','download','downloads','A download is a type of resource that users can download and use on their own computer.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('10','Notes','Text','note','notes','Notes are typically a category for any resource that might not fit any of the other categories.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('11','Series','Collection','series','series','Series are collections of other resources, typically online presentations, that cover a specific topic.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('12','Teaching Materials','Text','teaching material','teachingmaterials','Supplementary materials (study notes, guides, etc.) that don\'t quite fit into any of the other categories.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')";
			$queries[] = "INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('1','Datasets','Dataset','dataset','datasets','A collection of research data','1','1','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1\nplg_wishlist=1\nplg_citations=1\nplg_usage = 1')";
		}
		
		// Types
		if (!in_array($prefix . 'publication_master_types', $this->tables)) 
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
			) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8";

			$queries[] = "INSERT INTO `#__publication_master_types` (`id`,`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) 
						  VALUES ('1','File(s)','files','uploaded material','1','1','1','peer_review=1')";
			$queries[] = "INSERT INTO `#__publication_master_types` (`id`,`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) 
						  VALUES ('2','Link','links','external content','0','0','3','')";
			$queries[] = "INSERT INTO `#__publication_master_types` (`id`,`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) 
						  VALUES ('3','Wiki','notes','from project notes','0','0','5','')";
			$queries[] = "INSERT INTO `#__publication_master_types` (`id`,`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) 
						  VALUES ('4','Application','apps','simulation tool','0','0','4','')";
			$queries[] = "INSERT INTO `#__publication_master_types` (`id`,`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) 
						  VALUES ('5','Series','series','publication collection','0','0','6','')";
			$queries[] = "INSERT INTO `#__publication_master_types` (`id`,`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) 
						  VALUES ('6','Gallery','gallery','image/photo gallery','0','0','7','')";
			$queries[] = "INSERT INTO `#__publication_master_types` (`id`,`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`) 
						  VALUES ('7','Databases','databases','project database','0','0','2','')";
		}
		
		// Reviews
		if (!in_array($prefix . 'publication_ratings', $this->tables)) 
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
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
		}
		
		// Screenshots
		if (!in_array($prefix . 'publication_screenshots', $this->tables)) 
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
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
		}
		
		// Stats
		if (!in_array($prefix . 'publication_stats', $this->tables)) 
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
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
		}
		
		// Versions
		if (!in_array($prefix . 'publication_versions', $this->tables)) 
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
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
		}
		
		// Publications
		if (!in_array($prefix . 'publications', $this->tables)) 
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
			) ENGINE=MyISAM AUTO_INCREMENT=9000 DEFAULT CHARSET=utf8";
			
			$iniSetup = 1;
		}
		
		// Licenses
		if (!in_array($prefix . 'publication_licenses', $this->tables)) 
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
			) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8";

			$queries[] = "INSERT INTO `#__publication_licenses` (`id`,`name`,`text`,`title`,`url`,`info`,`ordering`,`active`,`apps_only`,`main`,`agreement`,`customizable`,`icon`) VALUES ('2','cc','','CC0 - Creative Commons','http://creativecommons.org/about/cc0','CC0 enables scientists, educators, artists and other creators and owners of copyright- or database-protected content to waive those interests in their works and thereby place them as completely as possible in the public domain, so that others may freely build upon, enhance and reuse the works for any purposes without restriction under copyright or database law.','2','1','0','1','1','0','/components/com_publications/assets/img/logos/cc.gif')";
		
			$queries[] = "INSERT INTO `#__publication_licenses` (`id`,`name`,`text`,`title`,`url`,`info`,`ordering`,`active`,`apps_only`,`main`,`agreement`,`customizable`,`icon`) VALUES ('1','custom','[ONE LINE DESCRIPTION]\r\nCopyright (C) [YEAR] [OWNER]','Custom','http://creativecommons.org/about/cc0','Custom license','3','1','0','0','0','1','/components/com_publications/assets/img/logos/license.gif')";
		
			$queries[] = "INSERT INTO `#__publication_licenses` (`id`,`name`,`text`,`title`,`url`,`info`,`ordering`,`active`,`apps_only`,`main`,`agreement`,`customizable`,`icon`) VALUES ('3','standard','All rights reserved.','Standard HUB License','http://nanohub.org','Standard HUB license.','1','0','0','0','0','0','/components/com_publications/images/logos/license.gif')";
		}
						
		// Enable component
		if ($iniSetup == 1)
		{			
			// Added condition for J1.5 - J1.6 compatibility
			if (version_compare(JVERSION, '1.6', 'lt'))
			{
				$queries[] = "INSERT INTO `#__components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`)
				SELECT 'Publications', 'option=com_publications', 0, 0, 'option=com_publications', 'Publications', 'com_publications', 0, 'js/ThemeOffice/component.png', 0, 'enabled=1\nautoapprove=0\nautoapproved_users=\nemail=0\ndefault_category=dataset\ndefaultpic=/components/com_publications/assets/img/resource_thumb.gif\nvideo_thumb=/components/com_publications/assets/img/video_thumb.gif\ngallery_thumb=/components/com_publications/assets/img/gallery_thumb.gif\nwebpath=/site/publications/\naboutdoi=\ndoi_shoulder=\ndoi_prefix=\ndoi_service=\ndoi_publisher=hub\ndoi_resolve=http://dx.doi.org/\ndoi_verify=http://n2t.net/ezid/id/\nissue_arch=0\nark_shoulder=\nark_prefix=\nsupportedtag=\nsupportedlink=\ngoogle_id=\nshow_authors=1\nshow_ranking=0\nshow_rating=0\nshow_date=3\nshow_citation=1\npanels=content, description, authors, audience, gallery, tags, access, license, notes\nsuggest_licence=1\nshow_tags=1\nshow_metadata=0\nshow_notes=1\nshow_license=1\nshow_access=0\nshow_gallery=1\nshow_audience=0\naudiencelink=audiencelevels\ndocumentation=\ndeposit_terms=\n\n', 1
				FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__components` WHERE name = 'Publications')";

				// Pub list
				$queries[] = "INSERT INTO `#__components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`)
				SELECT 'Publication List', '', 0, 0, 'option=com_publications', 'Publications', 'com_publications', 1, 'js/ThemeOffice/component.png', 0, '', 1
				FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__components` WHERE name = 'Publication List')";

				// Pub licenses
				$queries[] = "INSERT INTO `#__components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`)
				SELECT 'Publication Licenses', '', 0, 0, 'option=com_publications&controller=licenses', 'Publications Licenses', 'com_publications', 2, 'js/ThemeOffice/component.png', 0, '', 1
				FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__components` WHERE name = 'Publication Licenses')";

				// Pub categories
				$queries[] = "INSERT INTO `#__components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`)
				SELECT 'Publication Categories', '', 0, 0, 'option=com_publications&controller=categories', 'Publication Categories', 'com_publications', 3, 'js/ThemeOffice/component.png', 0, '', 1
				FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__components` WHERE name = 'Publication Categories')";

				// Pub master types
				$queries[] = "INSERT INTO `#__components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`)
				SELECT 'Master Types', '', 0, 0, 'option=com_publications&controller=types', 'Publication Master Types', 'com_publications', 4, 'js/ThemeOffice/component.png', 0, '', 1
				FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__components` WHERE name = 'Master Types')";

				// Enable plugins					
				$queries[] = "INSERT INTO `#__plugins`(`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) SELECT 'Publications - Related', 'related', 'publications', 0, 0, 1, 0, 0, 0, NULL, '' FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE name = 'Publications - Related')";

				$queries[] = "INSERT INTO `#__plugins`(`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) SELECT 'Publications - Recommendations', 'recommendations', 'publications', 0, 1, 1, 0, 0, 0, NULL, '' FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE name = 'Publications - Recommendations')";

				$queries[] = "INSERT INTO `#__plugins`(`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) SELECT 'Publications -Supporting Documents', 'supportingdocs', 'publications', 0, 2, 1, 0, 0, 0, NULL, '' FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE name = 'Publications -Supporting Documents')";

				$queries[] = "INSERT INTO `#__plugins`(`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) SELECT 'Publications - Versions', 'versions', 'publications', 0, 3, 1, 0, 0, 0, NULL, '' FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE name = 'Publications - Versions')";

				$queries[] = "INSERT INTO `#__plugins`(`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) SELECT 'Publications - Reviews', 'reviews', 'publications', 0, 4, 1, 0, 0, 0, NULL, '' FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE name = 'Publications - Reviews')";

				$queries[] = "INSERT INTO `#__plugins`(`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) SELECT 'Publications - Questions', 'questions', 'publications', 0, 5, 1, 0, 0, 0, NULL, '' FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE name = 'Publications - Questions')";

				$queries[] = "INSERT INTO `#__plugins`(`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) SELECT 'Publications - Citations', 'citations', 'publications', 0, 7, 1, 0, 0, 0, NULL, '' FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE name = 'Publications - Citations')";

				$queries[] = "INSERT INTO `#__plugins`(`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) SELECT 'Publications - Usage', 'usage', 'publications', 0, 8, 1, 0, 0, 0, NULL, 'period=14\n' FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE name = 'Publications - Usage')";

				$queries[] = "INSERT INTO `#__plugins`(`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) SELECT 'Publications - Share', 'share', 'publications', 0, 9, 1, 0, 0, 0, NULL, 'icons_limit=3\nshare_facebook=1\nshare_twitter=1\nshare_google=1\nshare_digg=1\nshare_technorati=1\nshare_delicious=0\nshare_reddit=1\nshare_email=0\nshare_print=0\n\n' FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE name = 'Publications - Share')";

				// Run queries
				foreach ($queries as $query)
				{
					$this->_db->setQuery( $query );
					$this->_db->query();
				}

				// Get Publication component ID
				$query = "SELECT id FROM #__components WHERE name='Publications' ORDER BY ordering ASC LIMIT 1";
				$this->_db->setQuery( $query );
				$cid = $this->_db->loadResult();

				if ($cid)
				{
					$query = "UPDATE `#__components` SET parent = $cid, name = 'List' WHERE name = 'Publication List'";
					$this->_db->setQuery( $query );
					$this->_db->query();

					$query = "UPDATE `#__components` SET parent = $cid, name = 'Licenses' WHERE name = 'Publication Licenses'";	
					$this->_db->setQuery( $query );
					$this->_db->query();

					$query = "UPDATE `#__components` SET parent = $cid WHERE name = 'Master Types'";	
					$this->_db->setQuery( $query );
					$this->_db->query();

					$query = "UPDATE `#__components` SET parent = $cid, name = 'Categories' WHERE name = 'Publication Categories'";	
					$this->_db->setQuery( $query );
					$this->_db->query();
				}
			}
			else
			{
				// The following is for Joomla 1.6+
				$params = '{"enabled":"1","autoapprove":"1","autoapproved_users":"","email":"0","default_category":"dataset","defaultpic":"\/components\/com_publications\/assets\/img\/resource_thumb.gif","toolpic":"\/components\/com_publications\/assets\/img\/tool_thumb.gif","video_thumb":"\/components\/com_publications\/images\/video_thumb.gif","gallery_thumb":"\/components\/com_publications\/images\/gallery_thumb.gif","webpath":"\/site\/publications","aboutdoi":"","doi_shoulder":"","doi_prefix":"","doi_service":"","doi_userpw":"","doi_xmlschema":"","doi_publisher":"","doi_resolve":"http:\/\/dx.doi.org\/","doi_verify":"http:\/\/n2t.net\/ezid\/id\/","supportedtag":"","supportedlink":"","google_id":"","show_authors":"1","show_ranking":"1","show_rating":"1","show_date":"3","show_citation":"1","panels":"content, description, authors, audience, gallery, tags, access, license, notes","suggest_licence":"0","show_tags":"1","show_metadata":"1","show_notes":"1","show_license":"1","show_access":"0","show_gallery":"1","show_audience":"0","audiencelink":"","documentation":"\/kb\/publications","deposit_terms":"\/legal\/termsofdeposit","dbcheck":"0","repository":"0","aip_path":"\/srv\/AIP"}';
				$this->installExtension('com_publications', 'component', 'com_publications', '', 0, $params, 1, 1);
				$this->installExtension('plg_publications_related', 'plugin', 'related', 'publications', 1, '', 1, 0);
				$this->installExtension('plg_publications_recommendations', 'plugin', 'recommendations', 'publications', 2, '', 1, 0);
				$this->installExtension('plg_publications_supportingdocs', 'plugin', 'supportingdocs', 'publications', 3, '', 1, 0);
				$this->installExtension('plg_publications_versions', 'plugin', 'versions', 'publications', 4, '', 1, 0);
				$this->installExtension('plg_publications_reviews', 'plugin', 'reviews', 'publications', 5, '', 1, 0);
				$this->installExtension('plg_publications_questions', 'plugin', 'questions', 'questions', 6, '', 1, 0);
				$this->installExtension('plg_publications_citations', 'plugin', 'citations', 'publications', 7, '', 1, 0);
				$this->installExtension('plg_publications_usage', 'plugin', 'usage', 'publications', 8, '', 1, 0);
				$this->installExtension('plg_publications_share', 'plugin', 'share', 'publications', 9, '', 1, 0);
			}
		}		
	}
}
