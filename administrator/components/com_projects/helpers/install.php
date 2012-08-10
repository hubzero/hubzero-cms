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
	 * Install project tables
	 * 
	 * @return     void
	 */	
	public function runInstall( ) 
	{
		$queries = array();
		
		$queries[] = "INSERT INTO `jos_components` (`id`,`name`,`link`,`menuid`,`parent`,`admin_menu_link`,`admin_menu_alt`,`option`,`ordering`,`admin_menu_img`,`iscore`,`params`,`enabled`) VALUES ('','Projects','option=com_projects','0','0','option=com_projects','Projects','com_projects','0','../components/com_hub/images/hubzero-component.png','0','grantinfo=1\nconfirm_step=1\nedit_settings=1\nrestricted_data=2\napprove_restricted=0\nprivacylink=/legal/privacy\nHIPAAlink=/legal/privacy\nFERPAlink=/legal/privacy\ncreatorgroup=\nadmingroup=projectsadmin\nsdata_group=\nginfo_group=\nmin_name_length=5\nmax_name_length=25\nreserved_names=clone, temp, test, view, edit, setup, start, deleteimg, intro, features, verify, register, autocomplete, showcount, edit, suspend, reinstate, review, analytics, reports, about, feedback, share, authorize\nwebpath=/srv/projects\ngitpath=/usr/bin/git\ngitclone=/site/projects/clone/.git\noffroot=1\nmaxUpload=10000000\ndefaultQuota=.5\npremiumQuota=1\napproachingQuota=90\nimagepath=/site/projects\ndefaultpic=/components/com_projects/assets/img/project.png\nimg_maxAllowed=40000000\nimg_file_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nmessaging=1\nprivacy=1\nlimit=25\nsidebox_limit=3\ngroup_prefix=pr-\nuse_alias=1\ndocumentation=/kb/projects\npubQuota=1\npremiumPubQuota=20\n\n','1')";
		
		// Make entries for Projects plugins
		$queries[] = "INSERT INTO `jos_plugins` (`id`,`name`,`element`,`folder`,`access`,`ordering`,`published`,`iscore`,`client_id`,`checked_out`,`checked_out_time`,`params`) VALUES ('','Projects - Notes','notes','projects','0','8','1','0','0','0','0000-00-00 00:00:00','')";
		
		$queries[] = "INSERT INTO `jos_plugins` (`id`,`name`,`element`,`folder`,`access`,`ordering`,`published`,`iscore`,`client_id`,`checked_out`,`checked_out_time`,`params`) VALUES ('','Projects - Todo','todo','projects','0','7','1','0','0','0','0000-00-00 00:00:00','')";
		
		$queries[] = "INSERT INTO `jos_plugins` (`id`,`name`,`element`,`folder`,`access`,`ordering`,`published`,`iscore`,`client_id`,`checked_out`,`checked_out_time`,`params`) VALUES ('','Projects - Blog','blog','projects','0','1','1','0','0','0','0000-00-00 00:00:00','')";
		
		$queries[] = "INSERT INTO `jos_plugins` (`id`,`name`,`element`,`folder`,`access`,`ordering`,`published`,`iscore`,`client_id`,`checked_out`,`checked_out_time`,`params`) VALUES ('','Projects - Files','files','projects','0','3','1','0','0','0','0000-00-00 00:00:00','display_limit=50\nmaxUpload=104857600\nmaxDownload=1048576\ntempPath=/site/projects/temp\n\n')";
		
		$queries[] = "INSERT INTO `jos_plugins` (`id`,`name`,`element`,`folder`,`access`,`ordering`,`published`,`iscore`,`client_id`,`checked_out`,`checked_out_time`,`params`) VALUES ('','Projects - Team','team','projects','0','2','1','0','0','0','0000-00-00 00:00:00','')";
		
		// Make entries for Groups/Members plugins, My Projects module
		$queries[] = "INSERT INTO `jos_plugins` (`id`,`name`,`element`,`folder`,`access`,`ordering`,`published`,`iscore`,`client_id`,`checked_out`,`checked_out_time`,`params`) VALUES ('','Members - Projects','projects','members','0','17','1','0','0','0','0000-00-00 00:00:00','')";
		$queries[] = "INSERT INTO `jos_plugins` (`id`,`name`,`element`,`folder`,`access`,`ordering`,`published`,`iscore`,`client_id`,`checked_out`,`checked_out_time`,`params`) VALUES ('','Groups - Projects','projects','groups','0','9','1','0','0','0','0000-00-00 00:00:00','')";
		$queries[] = "INSERT INTO `jos_modules` (`id`,`title`,`content`,`ordering`,`position`,`checked_out`,`checked_out_time`,`published`,`module`,`numnews`,`access`,`showtitle`,`params`,`iscore`,`client_id`,`control`) VALUES ('','My Projects','','0','myhub','0','0000-00-00 00:00:00','1','mod_myprojects','0','0','1','moduleclass=md-projects\nlimit=5\n\n','0','0','')";

		// Make entries to enable HUB messaging
		$queries[] = "INSERT INTO `jos_xmessage_component` (`id`,`component`,`action`,`title`) VALUES ('','com_projects','projects_member_added','You were added or invited to a project')";
		
		$queries[] = "INSERT INTO `jos_xmessage_component` (`id`,`component`,`action`,`title`) VALUES ('','com_projects','projects_new_project_admin','Receive notifications about project(s) you monitor as an admin or reviewer')";
		
		$queries[] = "INSERT INTO `jos_xmessage_component` (`id`,`component`,`action`,`title`) VALUES ('','com_projects','projects_admin_message','Receive administrative messages about your project(s)')";

		// Create jos_project_activity
		$queries[] = "CREATE TABLE `jos_project_activity` (
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

		// Create jos_project_comments

		$queries[] = "CREATE TABLE `jos_project_comments` (
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

		// Create jos_project_microblog
		$queries[] = "CREATE TABLE `jos_project_microblog` (
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

		// Create jos_project_owners
		$queries[] = "CREATE TABLE `jos_project_owners` (
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

		// Create table jos_project_todo
		$queries[] = "CREATE TABLE `jos_project_todo` (
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

		// Create jos_project_types
		$queries[] = "CREATE TABLE `jos_project_types` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `type` varchar(150) NOT NULL DEFAULT '',
		  `description` varchar(255) NOT NULL DEFAULT '',
		  `params` text,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1";

		$queries[] = "INSERT INTO `jos_project_types` (`id`,`type`,`description`,`params`) VALUES ('1','General','Individual or collaborative projects of general nature','apps_dev=0\npublications_public=1\nteam_public=1\nallow_invite=0')";
		$queries[] = "INSERT INTO `jos_project_types` (`id`,`type`,`description`,`params`) VALUES ('3','Content publication','Projects created with the purpose to publish data as a resource or a collection of related resources','apps_dev=0\npublications_public=1\nteam_public=1\nallow_invite=0')";
		$queries[] = "INSERT INTO `jos_project_types` (`id`,`type`,`description`,`params`) VALUES ('2','Application development','Projects created with the purpose to develop and publish a simulation tool or a code library','apps_dev=1\npublications_public=1\nteam_public=1\nallow_invite=0')";

		// Create jos_projects
		$queries[] = "CREATE TABLE `jos_projects` (
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
