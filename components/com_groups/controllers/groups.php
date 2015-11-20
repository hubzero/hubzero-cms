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
		$this->cn 		= JRequest::getVar('cn', '');
		$this->active 	= JRequest::getVar('active', '');
		$this->action 	= JRequest::getVar('action', '');

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
		if (isset($file))
		{
			return $this->downloadTask( $file );
		}

		// check in for user
		GroupsHelperPages::checkinForUser();

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
		$profile = \Hubzero\User\Profile::getInstance($this->juser->get("id"));

		//if we have a users profile load their groups and groups matching their tags
		if (is_object($profile))
		{
			//get users tags
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'models' . DS . 'tags.php');
			$mt = new MembersModelTags($profile->get("uidNumber"));
			$mytags = $mt->render('string');

			//get users groups
			$this->view->mygroups['members'] = \Hubzero\User\Helper::getGroups($profile->get("uidNumber"), 'members', 1);
			$this->view->mygroups['invitees'] = \Hubzero\User\Helper::getGroups($profile->get("uidNumber"), 'invitees', 1);
			$this->view->mygroups['applicants'] = \Hubzero\User\Helper::getGroups($profile->get("uidNumber"), 'applicants', 1);
			$this->view->mygroups = array_filter($this->view->mygroups);

			//get groups user may be interested in
			$this->view->interestinggroups = \Hubzero\User\Group\Helper::getGroupsMatchingTagString(
				$mytags,
				\Hubzero\User\Helper::getGroups($profile->get("uidNumber"))
			);
		}

		//get the popular groups
		$this->view->populargroups = \Hubzero\User\Group\Helper::getPopularGroups(3);

		//get featured groups
		$this->view->featuredgroups = \Hubzero\User\Group\Helper::getFeaturedGroups($this->config->get('intro_featuredgroups_list', ''));

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

		// Get site configuration
		$jconfig = JFactory::getConfig();

		//build list of filters
		$this->view->filters 			  = array();
		$this->view->filters['type']	  = array(1, 3);
		$this->view->filters['published'] = 1;
		$this->view->filters['limit']	  = 'all';
		$this->view->filters['fields']	  = array('COUNT(*)');
		$this->view->filters['search'] 	  = JRequest::getVar('search', '');
		$this->view->filters['sortby'] 	  = strtolower(JRequest::getWord('sortby', 'title'));
		$this->view->filters['policy'] 	  = strtolower(JRequest::getWord('policy', ''));
		$this->view->filters['index']	  = htmlentities(JRequest::getVar('index', ''));

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
		$this->view->total = \Hubzero\User\Group::find( $this->view->filters );

		// Filters for returning results
		$this->view->filters['limit']		= JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$this->view->filters['limit']		= ($this->view->filters['limit']) ? $this->view->filters['limit'] : 'all';
		$this->view->filters['start']		= JRequest::getInt('limitstart', 0);
		$this->view->filters['fields']		= array('cn', 'description', 'published', 'gidNumber', 'type', 'public_desc', 'join_policy');

		// Get a list of all groups
		$this->view->groups = \Hubzero\User\Group::find( $this->view->filters );
		$this->view->authorized = $this->_authorize();


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
		$this->view->group = \Hubzero\User\Group::getInstance( $this->cn );

		// check to make sure we were able to load group
		if (!is_object($this->view->group)|| !$this->view->group->get('gidNumber') || !$this->view->group->get('cn'))
		{
			$this->suggestNonExistingGroupTask();
			return;
		}

		// Ensure it's an allowable group type to display
		if (!in_array($this->view->group->get('type'), array(1,3)))
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}

		// ensure the group is published
		if ($this->view->group->get('published') != 1)
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
			if (!in_array($this->juser->get('id'), $managers) && in_array($this->juser->get('id'), $members_invitees))
			{
				$this->unapprovedGroupTask();
				return;
			}

			//set notification and clear after
			$this->setNotification( JText::_('COM_GROUPS_PENDING_APPROVAL_WARNING'), 'warning' );
		}

		// Get the group params
		$this->view->gparams = new JParameter( $this->view->group->get('params') );

		// Check authorization
		$this->view->authorized = GroupsHelperView::authorize( $this->view->group );

		// get active tab
		$this->view->tab     = GroupsHelperView::getTab( $this->view->group );
		$this->view->trueTab = strtolower(JRequest::getVar('active', 'overview'));

		// get group pages if any
		$pageArchive = GroupsModelPageArchive::getInstance();
		$pages = $pageArchive->pages('list', array(
			'gidNumber' => $this->view->group->get('gidNumber'),
			'state'     => array(0,1),
			'orderby'   => 'lft ASC'
		));

		// custom error handling for super groups
		GroupsHelperView::attachCustomErrorHandler($this->view->group);

		// add the overview content
		$overviewContent = '';
		$activePage      = null;
		if ($this->view->tab == 'overview')
		{
			// add home page to pages list
			$pages = GroupsHelperPages::addHomePage($this->view->group, $pages);

			// fetch the active page
			$activePage = GroupsHelperPages::getActivePage($this->view->group, $pages);

			// are we on the login
			if ($this->view->trueTab == 'login')
			{
				$overviewContent = GroupsHelperView::superGroupLogin($this->view->group);
			}

			// check to see if we have super group component or php page
			if ($overviewContent == null
				&& $this->config->get('super_components', 0))
			{
				$overviewContent = GroupsHelperView::superGroupComponents($this->view->group, $this->view->trueTab);
			}

			// do we have group php pages
			if ($overviewContent == null)
			{
				$overviewContent = GroupsHelperView::superGroupPhpPages($this->view->group);
			}

			//set overview content
			if ($overviewContent == null)
			{
				$overviewContent = GroupsHelperPages::displayPage( $this->view->group, $activePage );
			}
		}

		// build the title
		$this->_buildTitle( $pages );

		// build pathway
		$this->_buildPathway( $pages );

		//set some vars for view
		$this->view->title         = $this->_title;
		$this->view->juser         = $this->juser;
		$this->view->content       = GroupsHelperView::displaySectionsContent( $this->view->group, $overviewContent );
		$this->view->activePage    = $activePage;
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();

		//is this a super group?
		if ($this->view->group->isSuperGroup())
		{
			//use group template file if we have it
			JRequest::setVar('tmpl', 'group');

			// must call here cause otherwise doesnt load template
			$this->view->css()->js();

			// load super group template
			// parse & render
			$superGroupTemplate = new GroupsHelperTemplate();
			$superGroupTemplate->set('group', $this->view->group)
				               ->set('tab', $this->view->trueTab)
				               ->set('content', $this->view->content)
				               ->set('page', $this->view->activePage)
				               ->parse()
				               ->render();

			// echo content & stop execution
			return $superGroupTemplate->output(true);
		}

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
		if (!$this->juser->authorise('core.create', $this->_option))
		{
			return $this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				JText::_('COM_GROUPS_ERROR_NOT_AUTH'),
				'warning'
			);
		}

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
			$this->loginTask(JText::_('COM_GROUPS_CREATE_MUST_BE_LOGGED_IN'));
			return;
		}

		if (!$this->juser->authorise('core.edit', $this->_option))
		{
			return $this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				JText::_('COM_GROUPS_ERROR_NOT_AUTH'),
				'warning'
			);
		}

		//are we creating a new group?
		if ($this->_task == 'new')
		{
			// Instantiate an \Hubzero\User\Group object
			$this->view->group = new \Hubzero\User\Group();

			// set some group vars for view
			$this->view->group->set('cn', JRequest::getVar('suggested_cn', ''));
			$this->view->group->set('join_policy', $this->config->get('join_policy'));
			$this->view->group->set('discoverability', $this->config->get('discoverability', 0));
			$this->view->group->set('discussion_email_autosubscribe', null);

			$this->view->tags = "";

			//set title
			$this->view->title = JText::_('COM_GROUPS_NEW_TITLE');
		}
		else
		{
			//check to make sure we have  cname
			if (!$this->cn)
			{
				$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
			}

			// Load the group page
			$this->view->group = \Hubzero\User\Group::getInstance( $this->cn );

			// Ensure we found the group info
			if (!$this->view->group || !$this->view->group->get('gidNumber'))
			{
				$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
			}

			// Check authorization
			if ($this->_authorize() != 'manager' && !$this->_authorizedForTask('group.edit'))
			{
				$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') );
			}

			// Get the group's interests (tags)
			$gt = new GroupsModelTags($this->view->group->get('gidNumber'));
			$this->view->tags = $gt->render('string');

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

		// Path to group assets
		$asset_path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $this->view->lid . DS . 'uploads';

		// If path is a directory then load images
		$this->view->logos = array();
		if (is_dir($asset_path))
		{
			// Get all images that are in group asset folder and could be a possible group logo
			$this->view->logos = JFolder::files($asset_path, '.jpg|.jpeg|.png|.gif|.PNG|.JPG|.JPEG|.GIF', false, true);
		}

		// Get plugins
		JPluginHelper::importPlugin('groups');
		$dispatcher = JDispatcher::getInstance();

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
		$this->view->group_plugin_access = \Hubzero\User\Group\Helper::getPluginAccess($this->view->group);

		// build the title
		$this->_buildTitle();

		// build pathway
		$this->_buildPathway();

		$this->view->task = $this->_task;
		$this->view->config = $this->config;

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
			$this->loginTask(JText::_('COM_GROUPS_CREATE_MUST_BE_LOGGED_IN'));
			return;
		}

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$g_gidNumber = JRequest::getInt('gidNumber', 0, 'post');
		$c_gidNumber = JRequest::getVar('gidNumber', 0, 'post');

		if ((string) $g_gidNumber !== (string) $c_gidNumber)
		{
			throw new Exception(JText::_('COM_GROUPS_ERROR_NO_ID'), 404);
		}

		if ((!$g_gidNumber && !$this->juser->authorise('core.create', $this->_option))
		 || ($g_gidNumber && !$this->juser->authorise('core.edit', $this->_option)))
		{
			return $this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option),
				JText::_('COM_GROUPS_ERROR_NOT_AUTH'),
				'warning'
			);
		}

		$g_cn              = trim(JRequest::getVar('cn', '', 'post'));
		$g_description     = preg_replace('/\s+/', ' ',trim(JRequest::getVar('description', JText::_('NONE'), 'post')));
		$g_discoverability = JRequest::getInt('discoverability', 0, 'post');
		$g_public_desc     = \Hubzero\Utility\Sanitize::stripScripts(trim(JRequest::getVar('public_desc',  '', 'post', 'none', 2)));
		$g_private_desc    = \Hubzero\Utility\Sanitize::stripScripts(trim(JRequest::getVar('private_desc', '', 'post', 'none', 2)));
		$g_restrict_msg    = \Hubzero\Utility\Sanitize::stripScripts(trim(JRequest::getVar('restrict_msg', '', 'post', 'none', 2)));
		$g_join_policy     = JRequest::getInt('join_policy', 0, 'post');
		$tags              = trim(JRequest::getVar('tags', ''));
		$lid               = JRequest::getInt('lid', 0, 'post');
		$customization     = JRequest::getVar('group', '', 'POST', 'none', 2);
		$plugins           = JRequest::getVar('group_plugin', '', 'POST');
		$params            = JRequest::getVar('params', array(), 'POST');

		$g_discussion_email_autosubscribe = JRequest::getInt('discussion_email_autosubscribe', 0, 'post');

		//Check authorization
		if ($this->_authorize() != 'manager' && $g_gidNumber != 0 && !$this->_authorizedForTask('group.edit'))
		{
			$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') );
		}

		//are we editing or creating
		if ($g_gidNumber)
		{
			$group = \Hubzero\User\Group::getInstance($g_gidNumber);
			$this->_task = 'edit';
			$before = \Hubzero\User\Group::getInstance($g_gidNumber);
		}
		else
		{
			$this->_task = 'new';
			$group  = new \Hubzero\User\Group();
			$before = new \Hubzero\User\Group();
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
		if ($this->_task == 'new' && \Hubzero\User\Group::exists( $g_cn, true ))
		{
			$this->setNotification(JText::_('COM_GROUPS_SAVE_ERROR_ID_TAKEN'), 'error');
		}

		// Get the logo
		$logo = '';
		if (isset($customization['logo']))
		{
			$logo_parts = explode("/",$customization['logo']);
			$logo = array_pop($logo_parts);
		}

		// Plugin settings
		$plugin_access = '';
		foreach ($plugins as $plugin)
		{
			$plugin_access .= $plugin['name'] . '=' . $plugin['access'] . ',' . "\n";
		}

		// Run content through validation and spam filters
		if (trim($g_public_desc))
		{
			JPluginHelper::importPlugin('content');
			$results = JDispatcher::getInstance()->trigger('onContentBeforeSave', array(
				'com_groups.group.description',
				&$g_public_desc,
				($this->_task == 'new')
			));
			foreach ($results as $result)
			{
				if ($result === false)
				{
					$this->setNotification(JText::_('COM_GROUPS_SAVE_ERROR_FAILED_VALIDATION'), 'error');
					break;
				}
			}
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
			$group->set('logo', $logo);
			$group->set('plugins', $plugin_access);

			$this->lid = $lid;
			$this->group = $group;
			$this->tags = $tags;
			$this->editTask();
			return;
		}

		// Get some needed objects
		$jconfig = JFactory::getConfig();

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

		if ($this->_task == 'new')
		{
			$group->set('cn', $g_cn);
			$group->set('type', 1);
			$group->set('published', 1);
			$group->set('approved', $this->config->get('auto_approve', 1));
			$group->set('created', JFactory::getDate());
			$group->set('created_by', $this->juser->get('id'));
			$group->add('managers', array($this->juser->get('id')));
			$group->add('members', array($this->juser->get('id')));
			$group->create();
		}

		// merge incoming settings with existing params
		$params = new JRegistry($params);
		$gParams = new JRegistry($group->get('params'));
		$gParams->merge($params);

		//set group vars & Save group
		$group->set('description', $g_description);
		$group->set('public_desc', $g_public_desc);
		$group->set('private_desc', $g_private_desc);
		$group->set('join_policy', $g_join_policy);
		$group->set('restrict_msg', $g_restrict_msg);
		$group->set('discoverability', $g_discoverability);
		$group->set('logo', $logo);
		$group->set('plugins', $plugin_access);
		$group->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);
		$group->set('params', $gParams->toString());
		$group->update();

		// Process tags
		$gt = new GroupsModelTags($group->get('gidNumber'));
		$gt->setTags($tags, $this->juser->get('id'));

		// Rename the temporary upload directory if it exist
		$log_comments = '';

		// Get plugins
		JPluginHelper::importPlugin('groups');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onGroupAfterSave', array($before, $group));

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

			$log_action = 'group_created';

			// Trigger the functions that delete associated content
			// Should return logs of what was deleted
			$logs = $dispatcher->trigger('onGroupNew', array($group));
			if (count($logs) > 0)
			{
				$log_comments .= implode('', $logs);
			}
		}
		else
		{
			$log_action   = 'group_edited';
		}

		// log invites
		GroupsModelLog::log(array(
			'gidNumber' => $group->get('gidNumber'),
			'action'    => $log_action,
			'comments'  => $log_comments
		));

		// Build the e-mail message
		// Note: this is done *before* pushing the changes to the group so we can show, in the message, what was changed
		$eview = new \Hubzero\Component\View(array(
			'name' => 'emails',
			'layout' => 'saved'));
		$eview->option = $this->_option;
		$eview->juser  = $this->juser;
		$eview->group  = $group;

		// HTML email
		$message['multipart'] = $eview->loadTemplate();
		$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

		// Plaintext email
		$eview->setLayout('saved_plain');
		$message['plaintext'] = $eview->loadTemplate();
		$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

		// Get the administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// Get the "from" info
		$from = array(
			'name'  => $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name)),
			'email' => $jconfig->getValue('config.mailfrom')
		);

		//only email managers if updating group
		if ($type == 'groups_changed')
		{
			// build array of managers
			$managers = $group->get('managers');

			// Get the xmessage plugin
			JPluginHelper::importPlugin('xmessage');
			$dispatcher = JDispatcher::getInstance();

			if (!$dispatcher->trigger('onSendMessage', array($type, $subject, $message, $from, $managers, $this->_option)))
			{
				$this->setNotification(JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED'), 'error');
			}
		}

		//only inform site admin if the group wasnt auto-approved
		if (!$this->config->get('auto_approve', 1) && $group->get('approved') == 0)
		{
			// create approval subject
			$subject = JText::sprintf('COM_GROUPS_SAVE_WAITING_APPROVAL', $jconfig->getValue('config.sitename'));

			// build approval message
			$link  = 'https://' . trim($_SERVER['HTTP_HOST'], DS) . DS . 'groups' . DS . $group->get('cn');
			$link2 = 'https://' . trim($_SERVER['HTTP_HOST'], DS) . DS . 'administrator';
			$html  = JText::sprintf('COM_GROUPS_SAVE_WAITING_APPROVAL_DESC', $group->get('description'), $link, $link2);

			// create new message
			$message = new \Hubzero\Mail\Message();

			// build message object and send
			$message->setSubject($subject)
					->addFrom($from['email'], $from['name'])
					->setTo($emailadmin)
					->addHeader('X-Mailer', 'PHP/' . phpversion())
					->addHeader('X-Component', 'com_groups')
					->addHeader('X-Component-Object', 'group_pending_approval')
					->addHeader('X-Component-ObjectId', $group->get('gidNumber'))
					->addPart($html, 'text/html')
					->send();
		}

		// create home page
		if ($this->_task == 'new')
		{
			// create page
			$page = new GroupsModelPage(array(
				'gidNumber' => $group->get('gidNumber'),
				'parent'    => 0,
				'lft'       => 1,
				'rgt'       => 2,
				'depth'     => 0,
				'alias'     => 'overview',
				'title'     => 'Overview',
				'state'     => 1,
				'privacy'   => 'default',
				'home'      => 1
			));
			$page->store(false);

			// create page version
			$version = new GroupsModelPageVersion(array(
				'pageid'     => $page->get('id'),
				'version'    => 1,
				'content'    => "<!-- {FORMAT:HTML} -->\n<p>[[Group.DefaultHomePage()]]</p>",
				'created'    => JFactory::getDate(),
				'created_by' => JFactory::getUser()->get('id'),
				'approved'   => 1
			));
			$version->store(false);
		}

		// Show success message to user
		if ($this->_task == 'new')
		{
			$this->setNotification(JText::sprintf('COM_GROUPS_CREATED_SUCCESS', $group->get('description')), 'passed');
		}
		else
		{
			$this->setNotification(JText::sprintf('COM_GROUPS_UPDATED_SUCCESS', $group->get('description')), 'passed');
		}

		// Redirect back to the group page
		$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $group->get('cn')) );
		return;
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
			$this->loginTask(JText::_('COM_GROUPS_DELETE_MUST_BE_LOGGED_IN'));
			return;
		}

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = \Hubzero\User\Group::getInstance( $this->cn );

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

		// Get the group params
		$gparams = new JRegistry($this->view->group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0)
		{
			$this->setNotification(JText::_('COM_GROUPS_MEMBERSHIP_MANAGED_ELSEWHERE'), 'error');
			$this->setRedirect( JRoute::_('index.php?option=com_groups&cn=' . $this->view->group->get('cn')) );
			return;
		}

		// Get plugins
		JPluginHelper::importPlugin('groups');
		$dispatcher = JDispatcher::getInstance();

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

		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();

		//set some vars for view
		$this->view->title = JText::_('COM_GROUPS_DELETE_GROUP') . ': ' . $this->view->group->get('description');;
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
			$this->loginTask(JText::_('COM_GROUPS_DELETE_MUST_BE_LOGGED_IN'));
			return;
		}

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = \Hubzero\User\Group::getInstance( $this->cn );

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
		if (!$confirm_delete)
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
		$dispatcher = JDispatcher::getInstance();

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
		$jconfig = JFactory::getConfig();

		// Build the "from" info for e-mails
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// E-mail subject
		$subject = JText::sprintf('COM_GROUPS_DELETE_MESSAGE_SUBJECT', $deletedgroup->get('cn'));

		// Build the e-mail message
		$eview = new \Hubzero\Component\View(array('name' => 'emails','layout' => 'deleted'));
		$eview->option 		= $this->_option;
		$eview->sitename 	= $jconfig->getValue('config.sitename');
		$eview->juser 		= $this->juser;
		$eview->gcn 		= $deletedgroup->get('cn');
		$eview->msg 		= $message;
		$eview->group 		= $deletedgroup;
		$html 			    = $eview->loadTemplate();
		$html  			    = str_replace("\n", "\r\n", $html);

		// build array of email recipients
		$groupMembers = array();
		foreach ($members as $member)
		{
			$profile = \Hubzero\User\Profile::getInstance( $member );
			if ($profile)
			{
				$groupMembers[$profile->get('email')] = $profile->get('name');
			}
		}

		// create new message
		$message = new \Hubzero\Mail\Message();

		// build message object and send
		$message->setSubject($subject)
				->addFrom($from['email'], $from['name'])
				->setTo($groupMembers)
				->addHeader('X-Mailer', 'PHP/' . phpversion())
				->addHeader('X-Component', 'com_groups')
				->addHeader('X-Component-Object', 'group_deleted')
				->addHeader('X-Component-ObjectId', $deletedgroup->get('gidNumber'))
				->addPart($html, 'text/plain')
				->send();

		// log deleted group
		GroupsModelLog::log(array(
			'gidNumber' => $deletedgroup->get('gidNumber'),
			'action'    => 'group_deleted',
			'comments'  => $log
		));

		// Redirect back to the groups page
		$this->setNotification(JText::sprintf('COM_GROUPS_DELETE_SUCCESS', $deletedgroup->get('description')), 'passed');
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
		// throw 404 error
		header("HTTP/1.0 404 Not Found");

		// set the neeced layout
		$this->view->setLayout('suggest');

		//set notification
		$this->setNotification(
			JText::sprintf('COM_GROUPS_CREATE_SUGGEST', JRoute::_('index.php?option=' . $this->_option . '&task=new' . (is_numeric($this->cn) ? '' : '&suggested_cn=' . $this->cn))),
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
					WHERE (t.type=1 OR t.type=3) AND (LOWER(t.cn) LIKE '%" . $filters['search'] . "%' OR LOWER(t.description) LIKE '%" . $filters['search'] . "%')
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
	 * @param      object $group \Hubzero\User\Group
	 * @return     string
	 */
	public function groupavailabilityTask( $group = NULL )
	{
		//get the group
		$group = (!is_null($group)) ? $group : JRequest::getVar('group', '');
		$group = trim($group);

		if ($group == '')
		{
			return;
		}

		// Ensure the data passed is valid
		if (($group == 'new' || $group == 'browse') || (!$this->_validCn($group)) || (\Hubzero\User\Group::exists($group, true)))
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
		$group = \Hubzero\User\Group::getInstance( $this->cn );

		// make sure we have a group
		if (!is_object($group))
		{
			return;
		}

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

		// clean up file, strip double "uploads" & trim directory sep
		$file = str_replace('uploads', '', $file);
		$file = ltrim($file, DS);

		// get extension
		$extension = pathinfo($file, PATHINFO_EXTENSION);

		//if were on the wiki we need to output files a specific way
		if ($this->active == 'wiki')
		{
			//get access level for wiki
			$access = \Hubzero\User\Group\Helper::getPluginAccess($group, 'wiki');

			//check to make sure user has access to wiki section
			if (($access == 'members' && !in_array($this->juser->get('id'), $group->get('members')))
			 || ($access == 'registered' && $this->juser->get('guest') == 1))
			{
				$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') . ' ' . $file);
			}

			//load wiki page from db
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');
			$page = new WikiTablePage($this->database);

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
			$access = \Hubzero\User\Group\Helper::getPluginAccess($group, 'blog');

			//make sure user has access to blog
			if (($access == 'members' && !in_array($this->juser->get('id'), $group->get('members')))
			 || ($access == 'registered' && $this->juser->get('guest') == 1))
			{
				$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') . ' ' . $file);
			}

			//make sure we have a group id of the proper length
			$groupID = \Hubzero\User\Group\Helper::niceidformat($group->get('gidNumber'));

			//buld path to blog folder
			$base_path = $this->config->get('uploadpath') . DS . $groupID . DS . 'blog';
			if (!file_exists(JPATH_ROOT . DS . $base_path . DS . $file))
			{
				$base_path = $this->config->get('uploadpath') . DS . $group->get('gidNumber') . DS . 'uploads' . DS . 'blog';
			}
		}
		else
		{
			//get access level for overview or other group pages
			$access = \Hubzero\User\Group\Helper::getPluginAccess($group, 'overview');

			//check to make sure we can access it
			if (($access == 'members' && !in_array($this->juser->get('id'), $group->get('members')))
			 || ($access == 'registered' && $this->juser->get('guest') == 1))
			{
				$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') . ' ' . $file);
			}

			// Build the path
			$base_path = $this->config->get('uploadpath');
			$base_path .= DS . $group->get('gidNumber') . DS . 'uploads';
		}

		// trim base path
		$base_path = ltrim($base_path, DS);

		// only can serve files from within /site/groups/{group_id}/uploads/
		$pathCheck = JPATH_ROOT . DS . $base_path;

		// Final path of file
		$file_path     = $base_path . DS . $file;
		$alt_file_path = null;

		// if super group offer alt path outside uploads
		if ($group->isSuperGroup())
		{
			$alt_file_path = str_replace('/uploads', '', $base_path) . DS . $file;

			// if super group can serve files anywhere inside /site/groups/{group_id}
			$altPathCheck  = JPATH_ROOT . DS . ltrim($alt_file_path);
		}

		// Ensure the file exist
		if (!file_exists(JPATH_ROOT . DS . $file_path))
		{
			if ($alt_file_path == null || !file_exists(JPATH_ROOT . DS . $alt_file_path))
			{
				$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_FILE_NOT_FOUND') . ' ' . $file);
				return;
			}
			else
			{
				$file_path = $alt_file_path;
				$pathCheck = $altPathCheck;
			}
		}

		// get full path, expanding ../
		if ($realPath = realpath(JPATH_ROOT . DS . $file_path))
		{
			// make sure requested file is within acceptable dir
			if (strpos($realPath, $pathCheck) === false)
			{
				$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_FILE_NOT_FOUND') . ' ' . $file);
				return;
			}
		}

		// new content server
		$contentServer = new \Hubzero\Content\Server();
		$contentServer->filename(JPATH_ROOT . DS . $file_path);
		$contentServer->disposition('attachment');
		$contentServer->acceptranges(false);

		// do we need to manually set mime type
		if ($extension == 'css')
		{
			$contentServer->setContentType('text/css');
		}

		// Serve up the file
		if (!$contentServer->serve())
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
		$regex = '/^[0-9a-z]+[_0-9a-z]*$/u';
		if ($allowDashes)
		{
			$regex = '/^[0-9a-z]+[-_0-9a-z]*$/u';
		}

		if (\Hubzero\Utility\Validate::reserved('group', $cn))
		{
			return false;
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
