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
 * Projects install helper class
 */
class ProjectsInstall extends JObject {
	
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
	 * Install project logs
	 * 
	 * @return     void
	 */	
	public function installLogs( ) 
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__project_logs` (
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
		  KEY `projectid` (`projectid`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8";
		
		$this->runQuery($query);
	}
	
	/**
	 * Install project stats
	 * 
	 * @return     void
	 */	
	public function installStats( ) 
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__project_stats` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `month` int(2) DEFAULT NULL,
		  `year` int(2) DEFAULT NULL,
		  `week` int(2) DEFAULT NULL,
		  `processed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `stats` text,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
		
		$this->runQuery($query);
	}
	
	/**
	 * Install public stamps
	 * 
	 * @return     void
	 */	
	public function installPubStamps( ) 
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__project_public_stamps` (
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
		  UNIQUE KEY `stamp` (`stamp`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
		
		$this->runQuery($query);
	}
	
	/**
	 * Install remote connections
	 * 
	 * @return     void
	 */	
	public function installRemotes( ) 
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__project_remote_files` (
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
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
		
		$this->runQuery($query);
	}
	
	/**
	 * Install project plugin
	 * 
	 * @return     void
	 */	
	public function installPlugin( $name = '', $active = 0, $ordering = 8) 
	{
		// [!] zooley - Added condition for J1.5 - J1.6 compatibility
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			// The following is for Joomla 1.5-
			$query = "INSERT INTO `#__plugins`(`name`, `element`, `folder`, `access`, 
				`ordering`, `published`, `iscore`, `client_id`, `checked_out`, 
				`checked_out_time`, `params`) SELECT 'Projects - " . ucfirst($name) . "', 
				'" . strtolower($name) . "', 'projects', 0, $ordering, $active, 0, 0, 0, NULL, '' 
				FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE name = 'Projects - " . ucfirst($name) . "')";
		}
		else
		{
			// The following is for Joomla 1.6+
			$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)
					SELECT 'Projects - " . ucfirst($name) . "', 'plugin', '" . strtolower($name) . "', 'projects', 0, $active, 1, 0, null, null, null, null, 0, '0000-00-00 00:00:00', $ordering, 0
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__extensions` WHERE name = 'Projects - " . ucfirst($name) . "');";
		}

		$this->runQuery($query);
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
		FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__extensions` WHERE name = '$name')";
		
		$this->runQuery($query);
	}
	
	/**
	 * Install project tables
	 * 
	 * @return     void
	 */	
	public function runInstall( ) 
	{
		$queries = array();
		
		// [!] zooley - The following tables are Joomla 1.5-
		// New queries needed for Joomla 1.6+ #__extensions table
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$queries[] = "INSERT INTO `#__components` (`id`,`name`,`link`,`menuid`,`parent`,`admin_menu_link`,`admin_menu_alt`,`option`,`ordering`,`admin_menu_img`,`iscore`,`params`,`enabled`) VALUES ('','Projects','option=com_projects','0','0','option=com_projects','Projects','com_projects','0','../components/com_hub/images/hubzero-component.png','0','component_on=0\ngrantinfo=1\nconfirm_step=1\nedit_settings=1\nrestricted_data=2\napprove_restricted=0\nprivacylink=/legal/privacy\nHIPAAlink=/legal/privacy\nFERPAlink=/legal/privacy\ncreatorgroup=\nadmingroup=projectsadmin\nsdata_group=\nginfo_group=\nmin_name_length=5\nmax_name_length=25\nreserved_names=clone, temp, test, view, edit, setup, start, deleteimg, intro, features, verify, register, autocomplete, showcount, edit, suspend, reinstate, review, analytics, reports, about, feedback, share, authorize\nwebpath=/srv/projects\ngitpath=/usr/bin/git\noffroot=1\nmaxUpload=10000000\ndefaultQuota=.5\npremiumQuota=1\napproachingQuota=90\nimagepath=/site/projects\ndefaultpic=/components/com_projects/assets/img/project.png\nimg_maxAllowed=40000000\nimg_file_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nmessaging=1\nprivacy=1\nlimit=25\nsidebox_limit=3\ngroup_prefix=pr-\nuse_alias=1\ndocumentation=/kb/projects\npubQuota=1\npremiumPubQuota=20\n\n','1')";
		
			// Make entries for Projects plugins
			$queries[] = "INSERT INTO `#__plugins` (`id`,`name`,`element`,`folder`,`access`,`ordering`,`published`,`iscore`,`client_id`,`checked_out`,`checked_out_time`,`params`) VALUES ('','Projects - Notes','notes','projects','0','8','1','0','0','0','0000-00-00 00:00:00','')";
		
			$queries[] = "INSERT INTO `#__plugins` (`id`,`name`,`element`,`folder`,`access`,`ordering`,`published`,`iscore`,`client_id`,`checked_out`,`checked_out_time`,`params`) VALUES ('','Projects - Todo','todo','projects','0','7','1','0','0','0','0000-00-00 00:00:00','')";
		
			$queries[] = "INSERT INTO `#__plugins` (`id`,`name`,`element`,`folder`,`access`,`ordering`,`published`,`iscore`,`client_id`,`checked_out`,`checked_out_time`,`params`) VALUES ('','Projects - Blog','blog','projects','0','1','1','0','0','0','0000-00-00 00:00:00','')";
		
			$queries[] = "INSERT INTO `#__plugins` (`id`,`name`,`element`,`folder`,`access`,`ordering`,`published`,`iscore`,`client_id`,`checked_out`,`checked_out_time`,`params`) VALUES ('','Projects - Files','files','projects','0','3','1','0','0','0','0000-00-00 00:00:00','maxUpload=104857600\nmaxDownload=1048576\n\n')";
		
			$queries[] = "INSERT INTO `#__plugins` (`id`,`name`,`element`,`folder`,`access`,`ordering`,`published`,`iscore`,`client_id`,`checked_out`,`checked_out_time`,`params`) VALUES ('','Projects - Team','team','projects','0','2','1','0','0','0','0000-00-00 00:00:00','')";
		
			// Make entries for Groups/Members plugins, My Projects module
			$queries[] = "INSERT INTO `#__plugins` (`id`,`name`,`element`,`folder`,`access`,`ordering`,`published`,`iscore`,`client_id`,`checked_out`,`checked_out_time`,`params`) VALUES ('','Members - Projects','projects','members','0','17','0','0','0','0','0000-00-00 00:00:00','')";
			$queries[] = "INSERT INTO `#__plugins` (`id`,`name`,`element`,`folder`,`access`,`ordering`,`published`,`iscore`,`client_id`,`checked_out`,`checked_out_time`,`params`) VALUES ('','Groups - Projects','projects','groups','0','9','0','0','0','0','0000-00-00 00:00:00','')";
			$queries[] = "INSERT INTO `#__modules` (`id`,`title`,`content`,`ordering`,`position`,`checked_out`,`checked_out_time`,`published`,`module`,`numnews`,`access`,`showtitle`,`params`,`iscore`,`client_id`,`control`) VALUES ('','My Projects','','0','myhub','0','0000-00-00 00:00:00','0','mod_myprojects','0','0','0','moduleclass=md-projects\nlimit=5\n\n','0','0','')";
		}
		else
		{
			// The following is for Joomla 1.6+
			$params = '{"component_on":"0","grantinfo":"0","confirm_step":"0","edit_settings":"1","restricted_data":"0","restricted_upfront":"0","approve_restricted":"0","privacylink":"\/legal\/privacy","HIPAAlink":"\/legal\/privacy","FERPAlink":"\/legal\/privacy","creatorgroup":"","admingroup":"projectsadmin","sdata_group":"hipaa_reviewers","ginfo_group":"sps_reviewers","min_name_length":"6","max_name_length":"25","reserved_names":"clone, temp, test","webpath":"\/srv\/projects","offroot":"1","gitpath":"\/usr\/bin\/git","gitclone":"\/site\/projects\/clone\/.git","maxUpload":"104857600","defaultQuota":"1","premiumQuota":"1","approachingQuota":"90","pubQuota":"1","premiumPubQuota":"1","imagepath":"\/site\/projects","defaultpic":"\/components\/com_projects\/assets\/img\/project.png","img_maxAllowed":"5242880","img_file_ext":"jpg,jpeg,jpe,bmp,tif,tiff,png,gif","logging":"0","messaging":"1","privacy":"1","limit":"25","sidebox_limit":"3","group_prefix":"pr-","use_alias":"1","documentation":"\/projects\/features","dbcheck":"1"}';
			
			$this->installExtension('com_projects', 'component', 'com_projects', '', 0, $params, 1, 1);
			
			$this->installExtension('plg_projects_blog', 'plugin', 'blog', 'projects', 1, '', 1, 0);
			$this->installExtension('plg_projects_team', 'plugin', 'team', 'projects', 2, '', 1, 0);
			
			$params = '{"maxUpload":"104857600","maxDownload":"1048576","reservedNames":"google , dropbox, shared, temp","connectedProjects":"","enable_google":"0","google_clientId":"","google_clientSecret":"","google_appKey":"","google_folder":"Google","sync_lock":"0","auto_sync":"1","latex":"1","texpath":"\/usr\/bin\/","gspath":"\/usr\/bin\/"}';
			
			$this->installExtension('plg_projects_files','plugin', 'files', 'projects', 3, $params, 1, 0);
			
			$this->installExtension('plg_projects_todo','plugin', 'todo', 'projects', 7, '', 1, 0);
			$this->installExtension('plg_projects_notes','plugin', 'notes', 'projects', 8, '', 1, 0);
			
			// Make entries for Groups/Members plugins, My Projects module
			$this->installExtension('plg_members_projects','plugin', 'projects', 'members', 17, '', 1, 0);
			$this->installExtension('plg_groups_projects','plugin', 'projects', 'groups', 17, '', 1, 0);
			$this->installExtension('mod_myprojects','module', 'mod_myprojects', '', 0, '', 1, 0);
		}

		// Make entries to enable HUB messaging
		$queries[] = "INSERT INTO `#__xmessage_component` (`id`,`component`,`action`,`title`) VALUES ('','com_projects','projects_member_added','You were added or invited to a project')";
		
		$queries[] = "INSERT INTO `#__xmessage_component` (`id`,`component`,`action`,`title`) VALUES ('','com_projects','projects_new_project_admin','Receive notifications about project(s) you monitor as an admin or reviewer')";
		
		$queries[] = "INSERT INTO `#__xmessage_component` (`id`,`component`,`action`,`title`) VALUES ('','com_projects','projects_admin_message','Receive administrative messages about your project(s)')";

		// Create #__project_activity
		$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_activity` (
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
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";

		// Create #__project_comments

		$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_comments` (
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
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";

		// Create #__project_microblog
		$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_microblog` (
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
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";

		// Create #__project_owners
		$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_owners` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `projectid` int(11) NOT NULL DEFAULT '0',
		  `userid` int(11) NOT NULL DEFAULT '0',
		  `groupid` int(11) DEFAULT '0',
		  `invited_name` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
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
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";

		// Create table #__project_todo
		$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_todo` (
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
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";

		// Create #__project_types
		$queries[] = "CREATE TABLE IF NOT EXISTS `#__project_types` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `type` varchar(150) NOT NULL DEFAULT '',
		  `description` varchar(255) NOT NULL DEFAULT '',
		  `params` text,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1";

		$queries[] = "INSERT INTO `#__project_types` (`id`,`type`,`description`,`params`) 
						SELECT '1','General','Individual or collaborative projects of general nature','apps_dev=0\npublications_public=1\nteam_public=1\nallow_invite=0'
						FROM DUAL WHERE NOT EXISTS (SELECT `type` FROM `#__project_types` WHERE `type` = 'General')";
		$queries[] = "INSERT INTO `#__project_types` (`id`,`type`,`description`,`params`) 
						SELECT '3','Content publication','Projects created with the purpose to publish data as a resource or a collection of related resources','apps_dev=0\npublications_public=1\nteam_public=1\nallow_invite=0'
						FROM DUAL WHERE NOT EXISTS (SELECT `type` FROM `#__project_types` WHERE `type` = 'General')";
		$queries[] = "INSERT INTO `#__project_types` (`id`,`type`,`description`,`params`) 
						SELECT '2','Application development','Projects created with the purpose to develop and publish a simulation tool or a code library','apps_dev=1\npublications_public=1\nteam_public=1\nallow_invite=0'
						FROM DUAL WHERE NOT EXISTS (SELECT `type` FROM `#__project_types` WHERE `type` = 'General')";

		// Create #__projects
		$queries[] = "CREATE TABLE IF NOT EXISTS `#__projects` (
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
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
		
		// Run queries
		foreach ($queries as $query)
		{
			$this->_db->setQuery( $query );
			$this->_db->query();
		}		
	}
}
