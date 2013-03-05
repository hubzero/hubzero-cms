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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Groups controller class
 */
class GroupsControllerGroups extends GroupsControllerAbstract
{
	/**
	 * Override Execute Method
	 * 
	 * @return 	void
	 */
	public function execute()
	{
		//get the cname, active tab, and action for plugins
		$this->cn 		= JRequest::getCmd('cn', '');
		$this->active 	= JRequest::getCmd('active', '');
		$this->action 	= JRequest::getCmd('action', '');
		
		//are we serving up a file
		$uri = $_SERVER['REQUEST_URI'];
		if (strstr($uri, 'Image:')) 
		{
			$file = strstr($uri, 'Image:');
		}
		elseif (strstr($uri, 'File:'))
		{
			$file = strstr($uri, 'File:');	
		}
		
		//if we have a file
		if(isset($file))
		{
			return $this->downloadTask( $file ); 
		}
		
		//continue with parent execute method
		parent::execute();
	}
	
	
	/**
	 * Intro Page
	 * 
	 * @return     array
	 */
	public function displayTask()
	{
		// set the neeced layout
		$this->view->setLayout('display');
		
		// build the title
		$this->_buildTitle();

		// build pathway
		$this->_buildPathway();
		
		// Push some styles to the template
		$this->_getStyles('', 'introduction.css', true);
		$this->_getStyles();
		
		// Push some scripts
		$this->_getScripts('assets/js/' . $this->_name);
		
		//vars
		$mytags = '';
		$this->view->mygroups = array(
			'members'    => null,
			'invitees'   => null,
			'applicants' => null
		);
		$this->view->populargroups = array();
		$this->view->interestinggroups = array();
		
		//get the users profile
		$profile = Hubzero_User_Profile::getInstance($this->juser->get("id"));
		
		//if we have a users profile load their groups and groups matching their tags
		if (is_object($profile))
		{
			//get users tags
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'helpers' . DS . 'tags.php');
			$mt = new MembersTags($this->database);
			$mytags = $mt->get_tag_string($profile->get("uidNumber"));

			//get users groups
			$this->view->mygroups['members'] = Hubzero_User_Helper::getGroups($profile->get("uidNumber"), 'members', 1);
			$this->view->mygroups['invitees'] = Hubzero_User_Helper::getGroups($profile->get("uidNumber"), 'invitees', 1);
			$this->view->mygroups['applicants'] = Hubzero_User_Helper::getGroups($profile->get("uidNumber"), 'applicants', 1);
			$this->view->mygroups = array_filter($this->view->mygroups);

			//get groups user may be interested in
			$this->view->interestinggroups = Hubzero_Group_Helper::getGroupsMatchingTagString(
				$mytags, 
				Hubzero_User_Helper::getGroups($profile->get("uidNumber"))
			);
		}

		//get the popular groups
		$this->view->populargroups = Hubzero_Group_Helper::getPopularGroups(3);
		
		//get featured groups
		$this->view->featuredgroups = Hubzero_Group_Helper::getFeaturedGroups($this->config->get('intro_featuredgroups_list', ''));
		
		//set some vars for view
		$this->view->config = $this->config;
		$this->view->title = $this->_title;
		$this->view->juser = $this->juser;
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		
		//display view
		$this->view->display();
	}
	
	
	/**
	 * Browse Groups
	 * 
	 * @return     array
	 */
	public function browseTask()
	{
		// set the neeced layout
		$this->view->setLayout('browse');
		
		// build the title
		$this->_buildTitle();

		// build pathway
		$this->_buildPathway();
		
		// Push some styles to the template
		$this->_getStyles();
		
		// Get site configuration
		$jconfig = JFactory::getConfig();
		
		//build list of filters
		$this->view->filters 			= array();
		$this->view->filters['type']	= array(1, 3);
		$this->view->filters['limit']	= 'all';
		$this->view->filters['fields']	= array('COUNT(*)');
		$this->view->filters['search'] 	= JRequest::getWord('search', '');
		$this->view->filters['sortby'] 	= strtolower(JRequest::getWord('sortby', 'title'));
		$this->view->filters['policy'] 	= strtolower(JRequest::getWord('policy', ''));
		$this->view->filters['index']	= htmlentities(JRequest::getVar('index', ''));
		
		//make sure we have a valid sort filter
		if (!in_array($this->view->filters['sortby'], array('alias', 'title')))
		{
			$this->view->filters['sortby'] = 'title';
		}
		
		//make sure we have a valid policy filter
		if (!in_array($this->view->filters['policy'], array('open', 'restricted', 'invite', 'closed')))
		{
			$this->view->filters['policy'] = '';
		}
		
		// Get a record count
		$this->view->total = Hubzero_Group::find( $this->view->filters );
		
		// Filters for returning results
		$this->view->filters['limit']		= JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$this->view->filters['limit']		= ($this->view->filters['limit']) ? $this->view->filters['limit'] : 'all';
		$this->view->filters['start']		= JRequest::getInt('limitstart', 0);
		$this->view->filters['fields']		= array('cn', 'description', 'published', 'gidNumber', 'type', 'public_desc', 'join_policy');

		// Get a list of all groups
		$this->view->groups = Hubzero_Group::find( $this->view->filters );
		
		$this->view->authorized = $this->_authorize();
		if ($this->view->authorized && $this->view->groups) 
		{
			$this->view->groups = $this->getGroups($this->view->groups);
		}
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination($this->view->total, $this->view->filters['start'], $this->view->filters['limit']);
		
		//set some vars for view
		$this->view->title = $this->_title;
		$this->view->juser = $this->juser;
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		
		//display view
		$this->view->display();
	}
	
	
	/**
	 * View Group
	 * 
	 * @return     array
	 */
	public function viewTask()
	{
		// set the needed layout
		$this->view->setLayout('view');
		
		// validate the incoming cname
		if (!$this->_validCn( $this->cn, true ))
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}
		
		// Load the group object
		$this->view->group = Hubzero_Group::getInstance( $this->cn );
		
		// check to make sure we were able to load group
		if (!is_object($this->view->group) || !$this->view->group->get('gidNumber') || !$this->view->group->get('cn'))
		{
			$this->suggestNonExistingGroupTask();
			return;
		}
		
		// Ensure it's an allowable group type to display
		if (!in_array( $this->view->group->get('type'), array(1,3) )) 
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}
		
		// ensure the group is published
		if($this->view->group->get('published') != 1)
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}
		
		// Ensure the group has been published or has been approved
		if ($this->view->group->get('approved') != 1) 
		{
			//get list of members & managers & invitees
			$managers 	= $this->view->group->get('managers');
			$members 	= $this->view->group->get('members');
			$invitees 	= $this->view->group->get('invitees');
			$members_invitees = array_merge($members, $invitees);
			$managers_members_invitees = array_merge($managers, $members, $invitees);
			
			//if user is not member, manager, or invitee deny access
			if (!in_array($this->juser->get('id'), $managers_members_invitees))
			{
				$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
			}
			
			//if user is NOT manager but member or invitee
			if(!in_array($this->juser->get('id'), $managers) && in_array($this->juser->get('id'), $members_invitees))
			{
				$this->unapprovedGroupTask();
				return;
			}
			
			//set notification and clear after
			$this->setNotification( JText::_('COM_GROUPS_PENDING_APPROVAL_WARNING'), 'warning' );
		}
		
		//determine params parsing class based on joomla version
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}
		
		// Get the group params
		$this->view->gparams = new $paramsClass( $this->view->group->get('params') );
		
		// Check authorization
		$authorized = $this->_authorize();
		
		// Get the active tab (section)
		$this->view->tab = JRequest::getVar('active', 'overview');
		
		//create path
		$path = $this->config->get('uploadpath');
		if ($this->view->tab == 'wiki')
		{
			$path = '';
		}
		
		//build wiki config
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => '',
			'pagename' => $this->view->group->get('cn'),
			'pageid'   => $this->view->group->get('gidNumber'),
			'filepath' => $path,
			'domain'   => $this->view->group->get('cn')
		);
		
		//create wiki parse
		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();

		// Get the group pages if any
		$GPages = new GroupPages( $this->database );
		$this->view->pages = $GPages->getPages( $this->view->group->get('gidNumber'), true );

		if (in_array($this->view->tab, array_keys($this->view->pages)))
		{
			$wikiconfig['pagename'] .= DS . $this->view->tab;
		}

		// Push some vars to the group pages
		$GPages->parser 	= $p;
		$GPages->config 	= $wikiconfig;
		$GPages->group 		= $this->view->group;
		$GPages->authorized = $authorized;
		$GPages->tab 		= $this->view->tab;
		$GPages->pages 		= $this->view->pages;

		// Get the content to display group pages
		$group_overview = $GPages->displayPage();
		
		// Incoming paging for plugins
		$jconfig = JFactory::getConfig();
		$limit = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$start = JRequest::getInt('limitstart', 0);

		// Get plugins
		JPluginHelper::importPlugin('groups');
		$dispatcher =& JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		// then add overview to array
		$this->view->hub_group_plugins = $dispatcher->trigger('onGroupAreas', array());
		array_unshift($this->view->hub_group_plugins, array(
			'name'             => 'overview',
			'title'            => 'Overview',
			'default_access'   => 'anyone',
			'display_menu_tab' => true
		));

		// Get plugin access
		$this->view->group_plugin_access = Hubzero_Group_Helper::getPluginAccess( $this->view->group );

		// If active tab not overview and an not one of available tabs
		if ($this->view->tab != 'overview' && !in_array($this->view->tab, array_keys($this->view->group_plugin_access))) 
		{
			$this->view->tab = 'overview';
		}

		// Limit the records if we're on the overview page
		if ($this->view->tab == 'overview') 
		{
			$limit = 5;
		}
		$limit = ($limit == 0) ? 'all' : $limit;

		// Get the sections
		$this->view->sections = $dispatcher->trigger('onGroup', array(
				$this->view->group,
				$this->_option,
				$authorized,
				$limit,
				$start,
				$this->action,
				$this->view->group_plugin_access,
				array($this->view->tab)
			)
		);
		
		// Push the overview content to the array of sections we're going to output
		array_unshift($this->view->sections, array('html' => $group_overview, 'metadata' => ''));
		
		//is this a super group?
		if($this->view->group->get('type') == 3)
		{
			//use the super layout
			$this->view->setLayout('supergroup');
			
			//use group template file if we have it
			if (is_file(JPATH_SITE . DS . 'templates' . DS . JFactory::getApplication()->getTemplate() . DS . 'group.php'))
			{
				JRequest::setVar('tmpl', 'group');
			}
		}
		
		// build the title
		$this->_buildTitle( $this->view->pages );

		// build pathway
		$this->_buildPathway( $this->view->pages );
		
		// Push some styles to the template
		$this->_getStyles();
		
		//push scripts
		$this->_getScripts('assets/js/' . $this->_name);
		
		//set some vars for view
		$this->view->title = $this->_title;
		$this->view->juser = $this->juser;
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		
		//display view
		$this->view->display();
	}
	
	
	/**
	 *  Show add group
	 * 
	 * @return 		void
	 */
	public function newTask()
	{
		$this->editTask();
	}
	
	
	/**
	 *  Show group edit
	 * 
	 * @return 		void
	 */
	public function editTask()
	{
		// set the neeced layout
		$this->view->setLayout('edit');
		
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to delete a group.');
			return;
		}
		
		//are we creating a new group?
		if($this->_task == 'new')
		{
			// Instantiate an Hubzero_Group object
			$this->view->group = new Hubzero_Group();
			
			// set some group vars for view
			$this->view->group->set('cn', JRequest::getVar('suggested_cn', ''));
			$this->view->group->set('join_policy', $this->config->get('join_policy'));
			$this->view->group->set('discoverability', $this->config->get('discoverability', 0));
			
			$this->view->tags = "";
			
			//set title
			$this->view->title = JText::_('COM_GROUPS_NEW_TITLE');
		}
		else
		{
			//check to make sure we have  cname
			if(!$this->cn)
			{
				$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
			}

			// Load the group page
			$this->view->group = Hubzero_Group::getInstance( $this->cn );

			// Ensure we found the group info
			if (!$this->view->group || !$this->view->group->get('gidNumber')) 
			{
				$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
			}

			// Check authorization
			if ($this->_authorize() != 'manager') 
			{
				$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') );
			}
			
			// Get the group's interests (tags)
			$gt = new GroupsTags( $this->database );
			$this->view->tags = $gt->get_tag_string( $this->view->group->get('gidNumber') );
			
			//set title
			$this->view->title = JText::sprintf('COM_GROUPS_EDIT_TITLE', $this->view->group->get('description'));
		}
		
		//create dir for uploads
		$this->view->lid = time().rand(0,1000);
		if ($this->lid != '')
		{
			$this->view->lid = $this->lid;
		}
		elseif ($this->view->group->get('gidNumber'))
		{
			$this->view->lid = $this->view->group->get('gidNumber');
		}
		
		// are we passing a group from save method
		if ($this->group) 
		{
			$this->view->group = $this->group;
			$this->view->tags  = $this->tags;
		}
		
		// build the title
		$this->_buildTitle();

		// build pathway
		$this->_buildPathway();
		
		// push styles
		$this->_getStyles();
		
		//push scripts
		$this->_getScripts('assets/js/' . $this->_name);
		
		$this->view->task = $this->_task;
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		
		//display view
		$this->view->display();
	}
	
	
	/**
	 *  Save group settings
	 * 
	 * @return 		void
	 */
	public function saveTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to delete a group.');
			return;
		}
		
		// Incoming
		$g_gidNumber    	= JRequest::getInt('gidNumber', 0, 'post');
		$g_cn           	= strtolower(trim(JRequest::getVar('cn', '', 'post')));
		$g_description  	= preg_replace('/\s+/', ' ',trim(JRequest::getVar('description', JText::_('NONE'), 'post')));
		$g_discoverability	= JRequest::getInt('discoverability', 0, 'post');
		$g_public_desc  	= trim(JRequest::getVar('public_desc',  '', 'post', 'none', 2));
		$g_private_desc 	= trim(JRequest::getVar('private_desc', '', 'post', 'none', 2));
		$g_restrict_msg 	= trim(JRequest::getVar('restrict_msg', '', 'post', 'none', 2));
		$g_join_policy  	= JRequest::getInt('join_policy', 0, 'post');
		$tags 				= trim(JRequest::getVar('tags', ''));
		$lid 				= JRequest::getInt('lid', 0, 'post');
		$g_discussion_email_autosubscribe = JRequest::getInt('discussion_email_autosubscribe', 0, 'post');
		
		//Check authorization
		if ($this->_authorize() != 'manager' && $g_gidNumber != 0) 
		{
			$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') );
		}
		
		//are we editing or creating
		if($g_gidNumber)
		{
			$group = Hubzero_Group::getInstance( $g_gidNumber );
			$this->_task = 'edit';
		}
		else
		{
			$this->_task = 'new';
			$group = new Hubzero_Group();
		}
		
		// Check for any missing info
		if (!$g_cn) 
		{
			$this->setNotification(JText::_('COM_GROUPS_SAVE_ERROR_MISSING_INFORMATION') . ': ' . JText::_('COM_GROUPS_DETAILS_FIELD_CN'), 'error');
		}
		if (!$g_description) 
		{
			$this->setNotification(JText::_('COM_GROUPS_SAVE_ERROR_MISSING_INFORMATION') . ': ' . JText::_('COM_GROUPS_DETAILS_FIELD_DESCRIPTION'), 'error');
		}
		
		// Ensure the data passed is valid
		if ($g_cn == 'new' || $g_cn == 'browse') 
		{
			$this->setNotification(JText::_('COM_GROUPS_SAVE_ERROR_INVALID_ID'), 'error');
		}
		if (!$this->_validCn( $g_cn )) 
		{
			$this->setNotification(JText::_('COM_GROUPS_SAVE_ERROR_INVALID_ID'), 'error');
		}
		if ($this->_task == 'new' && Hubzero_Group::exists( $g_cn, true )) 
		{
			$this->setNotification(JText::_('COM_GROUPS_SAVE_ERROR_ID_TAKEN'), 'error');
		}
		
		// Push back into edit mode if any errors
		if ($this->getNotifications()) 
		{
			$group->set('cn', $g_cn);
			$group->set('description', $g_description);
			$group->set('public_desc', $g_public_desc);
			$group->set('private_desc', $g_private_desc);
			$group->set('join_policy', $g_join_policy);
			$group->set('restrict_msg', $g_restrict_msg);
			$group->set('discoverability', $g_discoverability);
			$group->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);
			
			$this->lid = $lid;
			$this->group = $group;
			$this->tags = $tags;
			$this->editTask();
			return;
		}
		
		// Get some needed objects
		$jconfig =& JFactory::getConfig();
		
		// Build the e-mail message
		if ($this->_task == 'new') 
		{
			$subject = JText::sprintf('COM_GROUPS_SAVE_EMAIL_REQUESTED_SUBJECT', $g_cn);
			$type = 'groups_created';
		} 
		else 
		{
			$subject = JText::sprintf('COM_GROUPS_SAVE_EMAIL_UPDATED_SUBJECT', $g_cn);
			$type = 'groups_changed';
		}
		
		// Build the e-mail message
		// Note: this is done *before* pushing the changes to the group so we can show, in the message, what was changed
		$eview = new JView(array('name' => 'emails', 'layout' => 'saved'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $group;
		$eview->isNew = ($this->_task == 'new') ? true : false;
		$eview->g_description = $g_description;
		$eview->g_discoverability = $g_discoverability;
		$eview->g_public_desc = $g_public_desc;
		$eview->g_private_desc = $g_private_desc;
		$eview->g_restrict_msg = $g_restrict_msg;
		$eview->g_join_policy = $g_join_policy;
		$eview->g_cn = $g_cn;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);
		
		if($this->_task == 'new')
		{
			$group->set('cn', $g_cn);
			$group->set('type', 1);
			$group->set('published', 1);
			$group->set('approved', $this->config->get('auto_approve', 1));
			$group->set('created', date("Y-m-d H:i:s"));
			$group->set('created_by', $this->juser->get('id'));
			$group->add('managers', array($this->juser->get('id')));
			$group->add('members', array($this->juser->get('id')));
			$group->create();
		}
		
		//set group vars & Save group
		$group->set('description', $g_description);
		$group->set('public_desc', $g_public_desc);
		$group->set('private_desc', $g_private_desc);
		$group->set('join_policy', $g_join_policy);
		$group->set('restrict_msg', $g_restrict_msg);
		$group->set('discoverability', $g_discoverability);
		$group->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);
		$group->update();
		
		// Process tags
		$gt = new GroupsTags($this->database);
		$gt->tag_object($this->juser->get('id'), $group->get('gidNumber'), $tags, 1, 1);
		
		// Log the group save
		$log = new XGroupLog($this->database);
		$log->gid = $group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->actorid = $this->juser->get('id');

		// Rename the temporary upload directory if it exist
		if ($this->_task == 'new') 
		{
			if ($lid != $group->get('gidNumber')) 
			{
				$config = $this->config;
				$bp = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS);
				if (is_dir($bp . DS . $lid)) 
				{
					rename($bp . DS . $lid, $bp . DS . $group->get('gidNumber'));
				}
			}

			$log->action = 'group_created';

			// Get plugins
			JPluginHelper::importPlugin('groups');
			$dispatcher =& JDispatcher::getInstance();

			// Trigger the functions that delete associated content
			// Should return logs of what was deleted
			$logs = $dispatcher->trigger('onGroupNew', array($group));
			if (count($logs) > 0) 
			{
				$log->comments .= implode('', $logs);
			}
		} 
		else 
		{
			$log->action = 'group_edited';
		}

		if (!$log->store()) 
		{
			$this->setNotification($log->getError(), 'error');
		}

		// Get the administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');
		
		// Get the "from" info
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Get plugins
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		
		//only email managers if updating group
		if($type == 'groups_changed')
		{
			if (!$dispatcher->trigger('onSendMessage', array($type, $subject, $message, $from, $group->get('managers'), $this->_option))) 
			{
				$this->setNotification(JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED'), 'error');
			}
		}
		else
		{
			//only inform site admin if the group wasnt auto-approved
			if(!$this->config->get('auto_approve', 1))
			{
				$to = $emailadmin;
				$subject = $jconfig->getValue('config.sitename') . ' Group Waiting Approval';
				$message  = 'Group "' . $group->get('description') . '" (' . 'https://' . trim($_SERVER['HTTP_HOST'], DS) . DS . 'groups' . DS . $group->get('cn') . ') is waiting for approval by HUB administrator.';
				$message .= "\n\n" . 'Please log into the administrator back-end of HUB to approve group: ' . "\n" . 'https://' . trim($_SERVER['HTTP_HOST'], DS) . DS . 'administrator';
				
				$headers  = "MIME-Version: 1.0\n";
				$headers .= "Content-type: text/plain; charset=utf-8\n";
				$headers .= "From: " . $from['name'] . " <" . $from['email'] . ">\n";
				$headers .= "Reply-To: " . $from['name'] . " <" . $from['email'] . ">\n";
				$headers .= "X-Priority: 3\n";
				$headers .= "X-MSMail-Priority: High\n";
				$headers .= "X-Mailer: " . $from['name'] . "\n";
				
				$args = "-f '" . $from['email'] . "'";
				
				mail($to, $subject, $message, $headers, $args);
			}
		}
		
		// Show success message to user
		if ($this->_task == 'new') 
		{
			$this->setNotification("You have successfully created the \"{$group->get('description')}\" group" , 'passed');
		} 
		else 
		{
			$this->setNotification("You have successfully updated the \"{$group->get('description')}\" group" , 'passed');
		}

		// Redirect back to the group page
		$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $group->get('cn')) );
		return;
	}
	
	
	/**
	 *  Show group customization
	 * 
	 * @return 		void
	 */
	public function customizeTask()
	{
		// set the neeced layout
		$this->view->setLayout('customize');
		
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to customize a group.');
			return;
		}
		
		//check to make sure we have  cname
		if(!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}
		
		// Load the group page
		$this->view->group = Hubzero_Group::getInstance( $this->cn );
		
		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber')) 
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}
		
		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') );
		}
		
		// Path to group assets
		$asset_path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $this->view->group->get('gidNumber');
		
		// If path is a directory then load images
		$this->view->logos = array();
		if (is_dir($asset_path)) 
		{
			// Get all images that are in group asset folder and could be a possible group logo
			$this->view->logos = JFolder::files($asset_path, '.jpg|.jpeg|.png|.gif|.PNG|.JPG|.JPEG|.GIF', false, true);
		}
		
		// Get plugins
		JPluginHelper::importPlugin('groups');
		$dispatcher =& JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		// then add overview to array
		$this->view->hub_group_plugins = $dispatcher->trigger('onGroupAreas', array());
		array_unshift($this->view->hub_group_plugins, array(
			'name'             => 'overview',
			'title'            => 'Overview',
			'default_access'   => 'anyone', 
			'display_menu_tab' => true
		));

		// Get plugin access
		$this->view->group_plugin_access = Hubzero_Group_Helper::getPluginAccess($this->view->group);
		
		// build the title
		$this->_buildTitle();
		
		// build pathway
		$this->_buildPathway();
		
		// Push some styles to the template
		$this->_getStyles();
		
		//push scripts
		$this->_getScripts('assets/js/' . $this->_name);
		
		//set some vars for view
		$this->view->title = 'Customize Group: ' . $this->view->group->get('description');
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		
		//display view
		$this->view->display();
	}
	
	
	/**
	 *  Save customizations
	 * 
	 * @return 		void
	 */
	public function doCustomizeTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to customize a group.');
			return;
		}
		
		//check to make sure we have  cname
		if(!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}
		
		// Load the group page
		$this->view->group = Hubzero_Group::getInstance( $this->cn );
		
		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber')) 
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}
		
		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') );
		}
		
		//get reqquest vars
		$customization = JRequest::getVar('group', '', 'POST', 'none', 2);
		$plugins = JRequest::getVar('group_plugin', '', 'POST');

		// Get the logo
		$logo_parts = explode("/",$customization['logo']);
		$logo = array_pop($logo_parts);

		// Overview type and content
		$overview_type = (!is_numeric($customization['overview_type'])) ? 0 : $customization['overview_type'];
		$overview_content = $customization['overview_content'];

		// Plugin settings
		$plugin_access = '';
		foreach ($plugins as $plugin)
		{
			$plugin_access .= $plugin['name'] . '=' . $plugin['access'] . ',' . "\n";
		}
		
		//update group
		$this->view->group->set('logo', $logo);
		$this->view->group->set('overview_type', $overview_type);
		$this->view->group->set('overview_content', $overview_content);
		$this->view->group->set('plugins', $plugin_access);
		if (!$this->view->group->update()) 
		{
			$this->setNotification(JText::_('An error occurred when trying to save changes to this group.'), 'error');
			$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $this->view->group->get('cn')) );
			return;
		}
		
		// Log the group save
		$log = new XGroupLog($this->database);
		$log->gid = $this->view->group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->actorid = $this->juser->get('id');
		$log->action = 'group_customized';
		if (!$log->store()) 
		{
			$this->setNotification($log->getError(), 'error');
		}
		
		// Push a success message
		$this->setNotification(JText::sprintf('You have successfully customized the %s group.', $this->view->group->get('description')), 'passed');
		
		// Redirect back to the group page
		$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $this->view->group->get('cn')) );
	}
	
	
	/**
	 *  Show confirm delete view
	 * 
	 * @return 		void
	 */
	public function deleteTask()
	{
		// set the neeced layout
		$this->view->setLayout('delete');
		
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to delete a group.');
			return;
		}
		
		//check to make sure we have  cname
		if(!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}
		
		// Load the group page
		$this->view->group = Hubzero_Group::getInstance( $this->cn );
		
		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber')) 
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}
		
		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') );
		}
		
		//determine params class based on joomla version
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the group params
		$gparams = new $paramsClass($this->view->group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->setNotification('Group membership is not managed in the group interface.', 'error');
			$this->setRedirect( JRoute::_('index.php?option=com_groups&cn=' . $this->view->group->get('cn')) );
			return;
		}
		
		// Get plugins
		JPluginHelper::importPlugin('groups');
		$dispatcher =& JDispatcher::getInstance();
		
		//start log
		$this->view->log = JText::sprintf('COM_GROUPS_DELETE_MEMBER_LOG',count($this->view->group->get('members')));
		
		// Trigger the functions that delete associated content
		// Should return logs of what was deleted
		$logs = $dispatcher->trigger('onGroupDeleteCount', array($this->view->group));
		if (count($logs) > 0) 
		{
			$this->view->log .= '<br />' . implode('<br />', $logs);
		}
		
		// build the title
		$this->_buildTitle();
		
		// build pathway
		$this->_buildPathway();
		
		// Push some styles to the template
		$this->_getStyles();
		
		//push scripts
		$this->_getScripts('assets/js/' . $this->_name);
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		
		//set some vars for view
		$this->view->title = 'Delete Group: ' . $this->view->group->get('description');;
		$this->view->juser = $this->juser;
		$this->view->msg = JRequest::getVar('msg', '');
		
		//display view
		$this->view->display();
	}
	
	
	/**
	 *  Permanently delete group
	 * 
	 * @return 		void
	 */
	public function doDeleteTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to delete a group.');
			return;
		}
		
		//check to make sure we have  cname
		if(!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}
		
		// Load the group page
		$this->view->group = Hubzero_Group::getInstance( $this->cn );
		
		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber')) 
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}
		
		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') );
		}
		
		//get request vars
		$confirm_delete = JRequest::getInt('confirmdel', '');
		$message 		= trim(JRequest::getVar('msg', '', 'post'));
		
		//check to make sure we have confirmed
		if(!$confirm_delete)
		{
			$this->setNotification(JText::_('COM_GROUPS_DELETE_MISSING_CONFIRM_MESSAGE'), 'error');
			$this->deleteTask();
			return;
		}
		
		// Start log
		$log  = JText::sprintf('COM_GROUPS_DELETE_MESSAGE_SUBJECT', $this->view->group->get('cn')) . "\n";
		
		$log .= JText::_('COM_GROUPS_GROUP_ID') . ': ' . $this->view->group->get('gidNumber') . "\n";
		$log .= JText::_('COM_GROUPS_GROUP_CNAME') . ': ' . $this->view->group->get('cn') . "\n";
		$log .= JText::_('COM_GROUPS_GROUP_TITLE') . ': ' . $this->view->group->get('description') . "\n";
		$log .= JText::_('COM_GROUPS_GROUP_DISCOVERABILITY') . ': ' . $this->view->group->get('discoverability') . "\n";
		$log .= JText::_('COM_GROUPS_GROUP_PUBLIC_TEXT') . ': ' . stripslashes($this->view->group->get('public_desc'))  . "\n";
		$log .= JText::_('COM_GROUPS_GROUP_PRIVATE_TEXT') . ': ' . stripslashes($this->view->group->get('private_desc'))  . "\n";
		$log .= JText::_('COM_GROUPS_GROUP_RESTRICTED_MESSAGE') . ': ' . stripslashes($this->view->group->get('restrict_msg')) . "\n";
		
		// Get number of group members
		$members  = $this->view->group->get('members');
		$managers = $this->view->group->get('managers');
		
		// Log ids of group members
		if ($members) 
		{
			$log .= JText::_('COM_GROUP_MEMBERS') . ': ';
			foreach ($members as $gu)
			{
				$log .= $gu . ' ';
			}
			$log .= '' . "\n";
		}
		$log .= JText::_('COM_GROUP_MANAGERS') . ': ';
		foreach ($managers as $gm)
		{
			$log .= $gm . ' ';
		}
		$log .= '' . "\n";
		
		// Get plugins
		JPluginHelper::importPlugin('groups');
		$dispatcher =& JDispatcher::getInstance();

		// Trigger the functions that delete associated content
		// Should return logs of what was deleted
		$logs = $dispatcher->trigger('onGroupDelete', array($this->view->group));
		if (count($logs) > 0) 
		{
			$log .= implode('',$logs);
		}
		
		// Build the file path
		$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $this->view->group->get('gidNumber');
		if (is_dir($path)) 
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFolder::delete( $path )) 
			{
				$this->setNotification(JText::_('UNABLE_TO_DELETE_DIRECTORY'), 'error');
			}
		}
		
		//clone the deleted group
		$deletedgroup = clone($this->view->group);
		
		// Delete group
		if (!$this->view->group->delete()) 
		{
			$this->setNotification($this->view->group->error, 'error');
			$this->deleteTask();
			return;
		}

		//get site config for mailing
		$jconfig =& JFactory::getConfig();

		// Build the "from" info for e-mails
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// E-mail subject
		$subject = JText::sprintf('COM_GROUPS_DELETE_MESSAGE_SUBJECT', $deletedgroup->get('cn'));

		// Build the e-mail message
		$eview = new JView(array('name' => 'emails','layout' => 'deleted'));
		$eview->option 		= $this->_option;
		$eview->sitename 	= $jconfig->getValue('config.sitename');
		$eview->juser 		= $this->juser;
		$eview->gcn 		= $deletedgroup->get('cn');
		$eview->msg 		= $message;
		$eview->group 		= $deletedgroup;
		$message 			= $eview->loadTemplate();
		$message 			= str_replace("\n", "\r\n", $message);

		// Send the message
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('groups_deleted', $subject, $message, $from, $members, $this->_option))) 
		{
			$this->setNotification(JText::_('COM_GROUPS_DELETE_MESSAGE_SEND_FAILURE'), 'error');
		}

		// Log the deletion
		$xlog = new XGroupLog($this->database);
		$xlog->gid 			= $deletedgroup->get('gidNumber');
		$xlog->uid 			= $this->juser->get('id');
		$xlog->timestamp 	= date('Y-m-d H:i:s', time());
		$xlog->action 		= 'group_deleted';
		$xlog->comments 	= $log;
		$xlog->actorid 		= $this->juser->get('id');
		if (!$xlog->store()) 
		{
			$this->setNotification($xlog->getError(), 'error');
		}
		
		// Redirect back to the groups page
		$this->setNotification("You successfully deleted the \"{$deletedgroup->get('description')}\" group", 'passed');
		$this->setRedirect( JRoute::_('index.php?option=' . $this->_option) );
		return;
	}
	
	
	/**
	 * View to Suggest User to Create Group
	 * 
	 * @return     array
	 */
	public function suggestNonExistingGroupTask()
	{
		// set the neeced layout
		$this->view->setLayout('suggest');
		
		//set notification
		$this->setNotification(
			JText::_('This group does not seem to exist. Would you like to <a href="' . JRoute::_('index.php?option=' . $this->_option . '&task=new' . (is_numeric($this->cn) ? '' : '&suggested_cn=' . $this->cn)) . '">create it</a>?'),
			'warning'
		);
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		
		//set some vars for view
		$this->view->title = "Groups";
		
		//display view
		$this->view->display();
	}
	
	
	/**
	 * Group is Unapproved
	 * 
	 * @return     array
	 */
	public function unapprovedGroupTask()
	{
		// set the neeced layout
		$this->view->setLayout('unapproved');
		
		// push styles
		$this->_getStyles();
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		
		//set some vars for view
		$this->view->title = "Group: Unapproved";
		$this->view->juser = $this->juser;
		
		//display view
		$this->view->display();
	}
	
	
	/**
	 * Return data for the autocompleter
	 * 
	 * @return     string JSON
	 */
	public function autocompleteTask()
	{
		$filters = array();
		$filters['limit']  = 20;
		$filters['start']  = 0;
		$filters['search'] = trim(JRequest::getString('value', ''));
		
		$query = "SELECT t.gidNumber, t.cn, t.description 
					FROM #__xgroups AS t 
					WHERE (t.type=1 OR t.type=2) AND (LOWER(t.cn) LIKE '%" . $filters['search'] . "%' OR LOWER(t.description) LIKE '%" . $filters['search'] . "%')
					ORDER BY t.description ASC";

		$this->database->setQuery($query);
		$rows = $this->database->loadObjectList();

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0) 
		{
			foreach ($rows as $row)
			{
				$name = str_replace("\n", '', stripslashes(trim($row->description)));
				$name = str_replace("\r", '', $name);
				
				//$json[] = '{"id":"'.$row->cn.'","name":"'.htmlentities($name,ENT_COMPAT,'UTF-8').'"}';
				$item = array(
					'id'   => $row->cn,
					'name' => $name
				);
				$json[] = $item;
			}
		}

		echo json_encode($json); //'[' . implode(',',$json) . ']';
	}
	
	
	/**
	 * Get a list of members
	 * 
	 * @return     void
	 */
	public function memberslistTask()
	{
		// Fetch results
		$filters = array();
		$filters['cn'] = trim(JRequest::getString('group', ''));

		if ($filters['cn']) 
		{
			$query = "SELECT u.username, u.name 
						FROM #__users AS u, #__xgroups_members AS m, #__xgroups AS g
						WHERE g.cn='" . $filters['cn'] . "' AND g.gidNumber=m.gidNumber AND m.uidNumber=u.id
						ORDER BY u.name ASC";
		} 
		else 
		{
			$query = "SELECT a.username, a.name"
				. "\n FROM #__users AS a"
				. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
				. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id"	// map aro to group
				. "\n INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id"
				. "\n WHERE a.block = '0' AND g.id=25"
				. "\n ORDER BY a.name";
		}

		$this->database->setQuery($query);
		$rows = $this->database->loadObjectList();

		// Output search results in JSON format
		$json = array();
		if ($filters['cn'] == '') 
		{
			$json[] = '{"username":"","name":"No User"}';
		}
		if (count($rows) > 0) 
		{
			foreach ($rows as $row)
			{
				$json[] = '{"username":"' . $row->username . '","name":"' . htmlentities(stripslashes($row->name), ENT_COMPAT, 'UTF-8') . '"}';
			}
		}

		echo '{"members":[' . implode(',', $json) . ']}';
	}
	
	
	/**
	 * Get a group's availability
	 * 
	 * @param      object $group Hubzero_Group
	 * @return     string
	 */
	public function groupavailabilityTask( $group = NULL )
	{
		//get the group
		$group = (!is_null($group)) ? $group : JRequest::getVar('group', '');
		$group = strtolower(trim($group));

		if ($group == '')
		{
			return;
		}

		// Ensure the data passed is valid
		if (($group == 'new' || $group == 'browse') || (!$this->_validCn($group)) || (Hubzero_Group::exists($group, true))) 
		{
			$availability = false;
		}
		else
		{
			$availability = true;
		}

		if (JRequest::getVar('no_html', 0) == 1)
		{
			echo json_encode(array('available' => $availability));
            return;
		}
		else
		{
			return $availability;
		}
	}
	
	
	/**
	 * Download a file
	 * 
	 * @param      string $filename File name
	 * @return     void
	 */
	public function downloadTask( $filename = "" )
	{
		//get the group
		$group = Hubzero_Group::getInstance( $this->cn );

		//authorize
		$authorized = $this->_authorize();

		//get the file name
		if (substr(strtolower($filename), 0, 5) == 'image') 
		{
			$file = urldecode(substr($filename, 6));
		} 
		elseif (substr(strtolower($filename), 0, 4) == 'file') 
		{
			$file = urldecode(substr($filename, 5));
		}
		else
		{
			return;
		}
		
		//if were on the wiki we need to output files a specific way
		if ($this->active == 'wiki') 
		{
			//get access level for wiki
			$access = Hubzero_Group_Helper::getPluginAccess($group, 'wiki');
			
			//check to make sure user has access to wiki section
			if (($access == 'members' && !in_array($this->juser->get('id'), $group->get('members'))) 
			 || ($access == 'registered' && $this->juser->get('guest') == 1)) 
			{
				$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') . ' ' . $file);
			}
			
			//load wiki page from db
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');
			$page = new WikiPage($this->database);
			
			$pagename = JRequest::getVar('pagename');
			$scope = JRequest::getVar('scope', $group->get('cn') . DS . 'wiki');
			if ($scope)
			{
				$parts = explode('/', $scope);
				if (count($parts) > 2)
				{
					$pagename = array_pop($parts);
					if (strtolower($filename) == strtolower($pagename))
					{
						$pagename = array_pop($parts);
					}
					$scope = implode('/', $parts);
				}
			}
			$page->load($pagename, $scope);
			
			//check specific wiki page access
			if ($page->get('access') == 1 && !in_array($this->juser->get('id'), $group->get('members')) && $authorized != 'admin') 
			{
				$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') . ' ' . $file);
				return;
			}
			
			//get the config and build base path
			$wiki_config = JComponentHelper::getParams('com_wiki');
			$base_path = $wiki_config->get('filepath') . DS . $page->get('id');
		} 
		elseif ($this->active == 'blog')
		{
			//get access setting of group blog
			$access = Hubzero_Group_Helper::getPluginAccess($group, 'blog');
			
			//make sure user has access to blog
			if (($access == 'members' && !in_array($this->juser->get('id'), $group->get('members'))) 
			 || ($access == 'registered' && $this->juser->get('guest') == 1)) 
			{
				$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') . ' ' . $file);
			}
			
			//make sure we have a group id of the proper length
			$groupID = Hubzero_Group_Helper::niceidformat($group->get('gidNumber'));
			
			//buld path to blog folder
			$base_path = $this->config->get('uploadpath') . DS . $groupID . DS . 'blog';
			if (!file_exists(JPATH_ROOT . DS . $base_path . DS . $file)) 
			{
				$base_path = $this->config->get('uploadpath') . DS . $group->get('gidNumber') . DS . 'blog';
			}
		}
		else 
		{
			//get access level for overview or other group pages
			$access = Hubzero_Group_Helper::getPluginAccess($group, 'overview');
			
			//check to make sure we can access it
			if (($access == 'members' && !in_array($this->juser->get('id'), $group->get('members'))) 
			 || ($access == 'registered' && $this->juser->get('guest') == 1)) 
			{
				$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') . ' ' . $file);
			}
			
			// Build the path
			$base_path = $this->config->get('uploadpath');
			$base_path .= DS . $group->get('gidNumber');
		}
		
		// Final path of file
		$file_path = $base_path . DS . $file;
		
		// Ensure the file exist
		if (!file_exists(JPATH_ROOT . DS . $file_path)) 
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_FILE_NOT_FOUND') . ' ' . $file);
		}
		
		// Get some needed libraries
		ximport('Hubzero_Content_Server');
		
		// Serve up the file
		$xserver = new Hubzero_Content_Server();
		$xserver->filename(JPATH_ROOT . DS . $file_path);
		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support
		if (!$xserver->serve()) 
		{
			JError::raiseError(404, JText::_('COM_GROUPS_SERVER_ERROR'));
		} 
		else 
		{
			exit;
		}
		return;
	}
	
	
	/**
	 * Check if a group alias is valid
	 * 
	 * @param 		integer 	$cname 			Group alias
	 * @param 		boolean		$allowDashes 	Allow dashes in cn
	 * @return 		boolean		True if valid, false if not
	 */
    private function _validCn( $cn, $allowDashes = false )
	{
		$regex = '/^[0-9a-zA-Z]+[_0-9a-zA-Z]*$/i';
		if ($allowDashes)
		{
			$regex = '/^[0-9a-zA-Z]+[-_0-9a-zA-Z]*$/i';
		}
		
		if (preg_match($regex, $cn))
		{
			if (is_numeric($cn) && intval($cn) == $cn && $cn >= 0) 
			{
				return false;
			} 
			else 
			{
				return true;
			}
		} 
		else 
		{
			return false;
		}
	}
}
