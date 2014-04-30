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
class GroupsControllerPages extends GroupsControllerAbstract
{
	/**
	 * Override Execute Method
	 * 
	 * @return 	void
	 */
	public function execute()
	{
		//get the cname, active tab, and action for plugins
		$this->cn     = JRequest::getVar('cn', '');
		$this->active = JRequest::getVar('active', '');
		$this->action = JRequest::getVar('action', '');
		
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
		$this->group = \Hubzero\User\Group::getInstance( $this->cn );
		
		// Ensure we found the group info
		if (!$this->group || !$this->group->get('gidNumber')) 
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}
		
		// Check authorization
		if ($this->_authorize() != 'manager' && !$this->_authorizedForTask('group.pages'))
		{
			$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') );
		}
		
		//continue with parent execute method
		parent::execute();
	}
	
	
	/**
	 * Display Group Pages
	 * 
	 * @return 	void
	 */
	public function displayTask()
	{
		// check in for user
		GroupsHelperPages::checkinForUser();
		
		// get group pages
		$pageArchive = new GroupsModelPageArchive();
		$this->view->pages = $pageArchive->pages('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'state'     => array(0,1),
			'orderby'   => 'ordering'
		));
		
		// get page categories
		$categoryArchive = new GroupsModelPageCategoryArchive();
		$this->view->categories = $categoryArchive->categories('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'orderby'   => 'title'
		));
		
		// get modules archive
		$moduleArchive = new GroupsModelModuleArchive();
		$this->view->modules = $moduleArchive->modules('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'state'     => array(0,1),
			'orderby'   => 'position ASC, ordering ASC'
		));
		
		// get request vars
		$this->view->search = JRequest::getWord('search', '');
		$this->view->filter = JRequest::getInt('filer', 0);
		
		//build pathway
		$this->_buildPathway();
		
		//build title
		$this->_buildTitle();
		
		//push styles
		$this->_getStyles();
		
		//push scripts
		$this->_getScripts('assets/js/' . $this->_name);
		
		// add fancy select for page categories
		\Hubzero\Document\Assets::addSystemScript('jquery.fancyselect');
		\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect.css');
		
		//set view vars
		$this->view->title  = 'Manage Group Pages: ' . $this->group->get('description');
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$this->view->group         = $this->group;
		$this->view->config        = $this->config;
		
		//display
		$this->view->display();
	}
	
	
	/**
	 * Add Group Page
	 * 
	 * @return 	void
	 */
	public function addTask()
	{
		$this->editTask();
	}
	
	
	/**
	 * Edit Group Page
	 * 
	 * @return 	void
	 */
	public function editTask()
	{
		//set to edit layout
		$this->view->setLayout('edit');
		
		//get request vars
		$pageid = JRequest::getInt('pageid', 0,'get');
		
		// load page object
		$this->view->page    = new GroupsModelPage( $pageid );
		$this->view->version = $this->view->page->version();
		
		//are we adding or editing
		$new = ($this->view->page->get('id') === null && $pageid == null) ? true : false;
		
		// make sure page exists
		if (!$this->view->page->exists() && !$new)
		{
			JError::raiseError(404, 'Page not found.');
		}
		
		// make sure page belongs to group - if editing
		if (!$this->view->page->belongsToGroup($this->group) && !$new)
		{
			JError::raiseError(403, 'You are not authorized to edit this page.');
		}
		
		// are we passing in a page from someplace else
		if ($this->page) 
		{
			$this->view->page = $this->page;
		}
		if ($this->version) 
		{
			$this->view->version = $this->version;
		}
		
		// checkout page
		GroupsHelperPages::checkout($this->view->page->get('id'));
		
		// get a list of all pages for page ordering
		$pageArchive = GroupsModelPageArchive::getInstance();
		$this->view->order = $pageArchive->pages('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'state'     => array(0,1),
			'orderby'   => 'ordering'
		));
		
		// get page categories
		$categoryArchive = new GroupsModelPageCategoryArchive();
		$this->view->categories = $categoryArchive->categories('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'orderby'   => 'title'
		));
		
		// get stylesheets for editor
		$this->view->stylesheets = GroupsHelperView::getPageCss($this->group);
		
		// get page templates
		$this->view->pageTemplates = GroupsHelperView::getPageTemplates($this->group);
		
		// build the title
		$this->_buildTitle();

		// build pathway
		$this->_buildPathway();
		
		//push styles
		$this->_getStyles();
		
		//push scripts
		$this->_getScripts('assets/js/' . $this->_name);
		
		// add fancy select for select boxes
		\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect.css');
		\Hubzero\Document\Assets::addSystemScript('jquery.fancyselect');
		
		// add color picker
		\Hubzero\Document\Assets::addSystemScript('jquery.colpick');
		\Hubzero\Document\Assets::addSystemStylesheet('jquery.colpick.css');
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$this->view->group         = $this->group;
		
		//display layout
		$this->view->display();
	}
	
	/**
	 * Save Group page
	 * 
	 * @return 	void
	 */
	public function saveTask()
	{
		// Get the page vars being posted
		$page    = JRequest::getVar('page', array(), 'post');
		$version = JRequest::getVar('pageversion', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		
		// are we updating or creating a new page
		$task = ($page['id']) ? 'update' : 'create';
		
		// load page and version objects
		$this->page    = new GroupsModelPage( $page['id'] );
		$this->version = new GroupsModelPageVersion();
		
		// ordering change
		$ordering = null;
		if (isset($page['ordering']) && $page['ordering'] != $this->page->get('ordering'))
		{
			$ordering = $page['ordering'];
			unset($page['ordering']);
		}
		
		// if this is new page, get next order possible for position
		if (!isset($page['id']) || $page['id'] == '')
		{
			$ordering = null;
			$page['ordering'] = $this->page->getNextOrder($this->group->get('gidNumber'));
		}
		
		// bind new page properties
		if (!$this->page->bind($page))
		{
			$this->setNotification($this->page->getError(), 'error');
			$this->editTask();
			return;
		}
		
		// bind new page version properties
		if (!$this->version->bind($version))
		{
			$this->setNotification($this->version->getError(), 'error');
			$this->editTask();
			return;
		}
		
		// make sure page belongs to group
		if ($task == 'update' && !$this->page->belongsToGroup($this->group))
		{
			JError::raiseError(403, 'You are not authorized to modify this page.');
		}
		
		// set page vars
		$this->page->set('gidNumber', $this->group->get('gidNumber'));
		$this->page->set('alias', $this->page->uniqueAlias());
		
		// make sure we can create both the page and version
		if (!$this->page->check() || !$this->version->check())
		{
			$error = ($this->page->getError()) ? $this->page->getError() : $this->version->getError();
			$this->setNotification($error, 'error');
			$this->editTask();
			return;
		}
		
		// save page settings
		if (!$this->page->store(true))
		{
			$this->setNotification($this->page->getError(), 'error');
			$this->editTask();
			return;
		}
		
		// do we need to reorder
		if ($ordering !== null)
		{
			$move = (int) $ordering - (int) $this->page->get('ordering');
			$this->page->move($move, $this->group->get('gidNumber'));
		}
		
		// get currrent version #
		$currentVersionNumber = ($this->page->version()) ? $this->page->version()->get('version') : 0;
		
		// did the module content change?
		$contentChanged = false;
		$oldContent = ($this->page->version()) ? trim($this->page->version()->get('content')) : '';
		$newContent = (isset($version['content'])) ? trim($version['content']) : '';
		$newContent = GroupsModelPageVersion::purify($newContent, $this->group->isSuperGroup());
		
		// is the new and old content different?
		if ($oldContent != $newContent)
		{
			$contentChanged = true;
		}
		
		// set page version vars
		$this->version->set('pageid', $this->page->get('id'));
		$this->version->set('version', $currentVersionNumber + 1);
		$this->version->set('created', JFactory::getDate()->toSql());
		$this->version->set('created_by', $this->juser->get('id'));
		$this->version->set('approved', 1);
		$this->version->set('approved_on', JFactory::getDate()->toSql());
		$this->version->set('approved_by', $this->juser->get('id'));
		
		// if we have php or script tags we must get page approved by admin
		// check the $newContent var since its already been purified 
		// and has has php/script tags removed if not super group
		if (strpos($newContent, '<?') !== false ||
			strpos($newContent, '<?php') !== false ||
			strpos($newContent, '<script') !== false)
		{
			$this->version->set('approved', 0);
			$this->version->set('approved_on', NULL);
			$this->version->set('approved_by', NULL);
		}
		
		// only create a new version and send approve notif if content has changed
		if ($contentChanged)
		{
			// check version again (because were not on store() method)
			if (!$this->version->check())
			{
				$this->setNotification($this->version->getError(), 'error');
				$this->editTask();
				return;
			}

			// save version settings
			if (!$this->version->store(false, $this->group->isSuperGroup()))
			{
				$this->setNotification($this->version->getError(), 'error');
				$this->editTask();
				return;
			}
		
			// send to approvers
			if ($this->version->get('approved', 0) == 0)
			{
				GroupsHelperPages::sendApproveNotification('page', $this->page);
			}
		}
		
		
		// check page back in
		GroupsHelperPages::checkin($this->page->get('id'));
		
		// Push success message and redirect
		$this->setNotification("You have successfully {$task}d the page.", 'passed');
		$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $this->group->get('cn') . '&controller=pages') );
		if ($return = JRequest::getVar('return', '','post'))
		{
			$this->setRedirect(base64_decode($return));
		}
	}
	
	/**
	 * Display page versions page
	 * 
	 * @return 	void
	 */
	public function versionsTask()
	{
		//set to edit layout
		$this->view->setLayout('versions');
		
		//get request vars
		$pageid = JRequest::getInt('pageid', 0,'get');
		
		// load page object
		$this->view->page = new GroupsModelPage( $pageid );
		
		// make sure page exists
		if (!$this->view->page->exists())
		{
			JError::raiseError(404, 'Page not found.');
		}
		
		// make sure page belongs to group - if editing
		if (!$this->view->page->belongsToGroup($this->group))
		{
			JError::raiseError(403, 'You are not authorized to edit this page.');
		}
		
		// build the title
		$this->_buildTitle();

		// build pathway
		$this->_buildPathway();
		
		//push styles
		$this->_getStyles();
		
		//push scripts
		$this->_getScripts('assets/js/' . $this->_name);
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$this->view->group         = $this->group;
		
		//display layout
		$this->view->display();
	}
	
	/**
	 * Publish Group Page
	 * 
	 * @return 	void
	 */
	public function publishTask()
	{
		$this->setStateTask( 1, 'published' );
	}
	
	
	/**
	 * Unpublish Group Page
	 * 
	 * @return 	void
	 */
	public function unpublishTask()
	{
		$this->setStateTask( 0, 'unpubished' );
	}
	
	
	/**
	 * Delete Group Page
	 * 
	 * @return 	void
	 */
	public function deleteTask()
	{
		$this->setStateTask( 2, 'deleted' );
	}
	
	
	/**
	 * Set page state
	 * 
	 * @return 	void
	 */
	public function setStateTask( $state = 1, $status = 'published' )
	{
		//get request vars
		$pageid = JRequest::getInt('pageid', 0, 'get');
		
		// load page model
		$page = new GroupsModelPage( $pageid );
		
		// make sure its out page
		if (!$page->belongsToGroup($this->group))
		{
			JError::raiseError(403, 'You are not authorized to modify this page.');
		}
		
		// make sure state is a valid state
		if (!in_array($state, array(0, 1, 2)))
		{
			$state = 1;
		}
		
		// set the page state
		$page->set('state', $state);
		
		// save
		if (!$page->store(false))
		{
			$this->setNotification($page->getError(), 'error');
			$this->displayTask();
			return;
		}
		
		//inform user & redirect
		$this->setNotification('The group page was successfully ' . $status . '.', 'passed');
		$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $this->group->get('cn') . '&controller=pages') );
		if ($return = JRequest::getVar('return', '','get'))
		{
			$this->setRedirect(base64_decode($return));
		}
	}
	
	
	/**
	 * Reorder Pages Task
	 * 
	 * @return 	void
	 */
	public function reorderTask()
	{
		//get the request vars
		$pagesOrder = JRequest::getVar('order', array(), 'post');
		
		// update each page accordingly
		foreach ($pagesOrder as $order => $page)
		{
			$order = (int) $order + 1;
			$sql = "UPDATE `#__xgroups_pages` SET `ordering`=".$this->database->quote($order)." WHERE `id`=".$this->database->quote($page);
			$this->database->setQuery( $sql );
			$this->database->query();
		}
		
		//we successfully redirected
		echo json_encode(array('reordered'=>true));
	}
	
	
	/**
	 * Set Group Home Page
	 * 
	 * @return 	void
	 */
	public function setHomeTask()
	{
		// get request vars
		$pageid = JRequest::getInt('pageid', 0, 'get');
		
		// load page model
		$page = new GroupsModelPage( $pageid );
		
		// make sure its out page
		if (!$page->belongsToGroup($this->group))
		{
			JError::raiseError(403, 'You are not authorized to modify this page.');
		}
		
		// make sure we have an approved version
		$view->version = $page->approvedVersion();
		if ($view->version === null)
		{
			$this->setNotification('Unable to set "' . $page->get('title') . '" as the home page. The page must have at least one approved version to set as home.', 'error');
			$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $this->group->get('cn') . '&controller=pages') );
			return;
		}
		
		// remove any current home page
		$pageArchive = GroupsModelPageArchive::getInstance();
		$pageArchive->reset('home', 0, array(
			'gidNumber' => $this->group->get('gidNumber')
		));
		
		// toggle home state
		$home = 1;
		if ($page->get('home') == 1)
		{
			$home = 0;
		}
		$page->set('home', $home);
		
		// store new group home page
		if (!$page->store())
		{
			$this->setNotification( $page->getError(), 'error' );
			return $this->displayTask();
		}
		
		// inform user
		$this->setNotification("The page " . $page->get('title') . " was successfully set as the home page.", 'passed');
		
		// redirect
		$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $this->group->get('cn') . '&controller=pages') );
		if ($return = JRequest::getVar('return', '','get'))
		{
			$this->setRedirect(base64_decode($return));
		}
	}
	
	/**
	 * Preview Group Page
	 * 
	 * @return 	void
	 */
	public function previewTask()
	{
		// get reqest vars
		$pageid  = JRequest::getInt('pageid', 0, 'get');
		$version = JRequest::getInt('version', 0, 'get');
		
		// page object
		$page = new GroupsModelPage( $pageid );
		
		// render preview
		echo GroupsHelperPages::generatePreview($page, $version);
		exit();
	}
	
	
	/**
	 * Output raw content 
	 * 
	 * @param     $escape    Escape outputted content
	 * @return    string     HTML content
	 */
	public function rawTask( $escape = true )
	{
		// get reqest vars
		$pageid  = JRequest::getInt('pageid', 0, 'get');
		$version = JRequest::getInt('version', 1, 'get');
		
		// page object
		$page = new GroupsModelPage( $pageid );
		
		// make sure page belongs to this group
		if (!$page->belongsToGroup($this->group))
		{
			JError::raiseError(403, 'You are not authorized to view this page.');
		}
		
		// load page version
		$pageVersion = $page->version($version);
		
		// do we have a page version
		if ($pageVersion === null)
		{
			JError::raiseError(404, 'Page Version Not Found');
		}
		
		// output page version
		if ($escape)
		{
			echo '<pre>' . $this->view->escape($pageVersion->get('content')) . '</pre>';
		}
		else
		{
			echo $pageVersion->get('content');
		}
		exit();
	}
}