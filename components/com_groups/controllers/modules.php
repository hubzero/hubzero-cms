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
class GroupsControllerModules extends GroupsControllerAbstract
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
	 * Display Page Modules
	 *
	 * @return void
	 */
	public function displayTask()
	{
		$this->setRedirect( JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages#modules'));
	}
	
	/**
	 * Add Module
	 *
	 * @return void
	 */
	public function addTask()
	{
		$this->editTask();
	}
	
	/**
	 * Edit Module
	 *
	 * @return void
	 */
	public function editTask()
	{
		//set to edit layout
		$this->view->setLayout('edit');
		
		// get request vars
		$moduleid = JRequest::getInt('moduleid', 0);
		
		// get the category object
		$this->view->module = new GroupsModelModule( $moduleid );
		
		// are we passing a module object
		if ($this->module)
		{
			$this->view->module = $this->module;
		}
		
		// get a list of all pages for creating module menu
		$pageArchive = GroupsModelPageArchive::getInstance();
		$this->view->pages = $pageArchive->pages('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'state'     => array(0,1),
			'orderby'   => 'ordering'
		));
		
		// get a list of all pages for creating module menu
		$moduleArchive = GroupsModelModuleArchive::getInstance();
		$this->view->order = $moduleArchive->modules('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'position'  => $this->view->module->get('position'),
			'state'     => array(0,1),
			'orderby'   => 'ordering'
		));
		
		// get stylesheets for editor
		$this->view->stylesheets = GroupsHelperView::getPageCss($this->group);
		
		// build the title
		$this->_buildTitle();

		// build pathway
		$this->_buildPathway();
		
		//push styles
		$this->_getStyles();
		
		//push scripts
		$this->_getScripts('assets/js/' . $this->_name);
		
		// add fancy select for page categories
		\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect.css');
		\Hubzero\Document\Assets::addSystemScript('jquery.fancyselect');
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$this->view->group = $this->group;
		
		//display layout
		$this->view->display();
	}
	
	/**
	 * Save Module
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// get request vars
		$module = JRequest::getVar('module', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		$menu   = JRequest::getVar('menu', array(), 'post');

		// set gid number
		$module['gidNumber'] = $this->group->get('gidNumber');
		
		// clean title & position
		$module['title']    = preg_replace("/[^-_ a-zA-Z0-9]+/", "", $module['title']);
		$module['position'] = preg_replace("/[^-_a-zA-Z0-9]+/", "", $module['position']);
		
		// get the category object
		$this->module = new GroupsModelModule( $module['id'] );
		
		// ordering change
		$ordering = null;
		if (isset($module['ordering']) && $module['ordering'] != $this->module->get('ordering'))
		{
			$ordering = $module['ordering'];
			unset($module['ordering']);
		}
		
		// if this is new module or were changing position, 
		// get next order possible for position
		if (!isset($module['id']) || ($module['id'] == '')
			|| ($module['position'] != $this->module->get('position')))
		{
			$ordering = null;
			$module['ordering'] = $this->module->getNextOrder($module['position']);
		}
		
		// did the module content change?
		$contentChanged = false;
		$oldContent = trim($this->module->get('content'));
		$newContent = (isset($module['content'])) ? trim($module['content']) : '';
		$newContent = GroupsModelModule::purify($newContent, $this->group->isSuperGroup());
		
		// is the new and old content different?
		if ($oldContent != $newContent)
		{
			$contentChanged = true;
		}
		
		// bind request vars to module model
		if (!$this->module->bind( $module ))
		{
			$this->setNotification($this->module->getError(), 'error');
			return $this->editTask();
		}
		
		// module is approved unless contains php or scripts (checked below)
		$this->module->set('approved', 1);
		
		// if we have php or script tags we must get module approved by admin
		if (strpos($this->module->get('content'), '<?') !== false ||
			strpos($this->module->get('content'), '<?php') !== false ||
			strpos($this->module->get('content'), '<script') !== false)
		{
			// only change approve status if content changed
			if ($contentChanged)
			{
				$this->module->set('approved', 0);
				$this->module->set('approved_on', NULL);
				$this->module->set('approved_by', NULL);
				$this->module->set('checked_errors', 0);
				$this->module->set('scanned', 0);
			}
		}
		
		// set created if new module
		if (!$this->module->get('id'))
		{
			$this->module->set('created', JFactory::getDate()->toSql());
			$this->module->set('created_by', JFactory::getUser()->get('id'));
		}
		
		// set modified 
		$this->module->set('modified', JFactory::getDate()->toSql());
		$this->module->set('modified_by', JFactory::getUser()->get('id'));


		// check module again (because were not on store() method)
		if (!$this->module->check())
		{
			$this->setNotification($this->module->getError(), 'error');
			$this->editTask();
			return;
		}
		
		// save version settings
		// dont run check on module store, skips onContentBeforeSave in Html format hadler
		if (!$this->module->store(false, $this->group->isSuperGroup()))
		{
			$this->setNotification($this->module->getError(), 'error');
			$this->editTask();
			return;
		}

		// create module menu
		if (!$this->module->buildMenu($menu))
		{
			$this->setNotification($this->module->getError(), 'error');
			$this->editTask();
			return;
		}
		
		// do we need to reorder
		if ($ordering !== null)
		{
			$move = (int) $ordering - (int) $this->module->get('ordering');
			$this->module->move($move, $this->module->get('position'));
		}
		
		// send to approvers if unapproved
		if ($this->module->get('approved', 0) == 0)
		{
			GroupsHelperPages::sendApproveNotification( 'module', $this->module );
		}
		
		// Push success message and redirect
		$this->setNotification("You have successfully updated the module.", 'passed');
		$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $this->group->get('cn') . '&controller=pages#modules') );
		if ($return = JRequest::getVar('return', '','post'))
		{
			$this->setRedirect(base64_decode($return));
		}
	}
	
	
	/**
	 * Publish Group Module
	 * 
	 * @return 	void
	 */
	public function publishTask()
	{
		$this->setStateTask( 1, 'published' );
	}
	
	
	/**
	 * Unpublish Group Module
	 * 
	 * @return 	void
	 */
	public function unpublishTask()
	{
		$this->setStateTask( 0, 'unpubished' );
	}
	
	
	/**
	 * Delete Module
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
		$moduleid = JRequest::getInt('moduleid', 0, 'get');
		
		// load page model
		$module = new GroupsModelModule( $moduleid );
		
		// make sure its out page
		if (!$module->belongsToGroup($this->group))
		{
			JError::raiseError(403, 'You are not authorized to modify this module.');
		}
		
		// make sure state is a valid state
		if (!in_array($state, array(0, 1, 2)))
		{
			$state = 1;
		}
		
		// set the page state
		$module->set('state', $state);
		
		// save
		if (!$module->store(false))
		{
			$this->setNotification($module->getError(), 'error');
			$this->displayTask();
			return;
		}
		
		//inform user & redirect
		$this->setNotification('The group module was successfully ' . $status . '.', 'passed');
		$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $this->group->get('cn') . '&controller=modules') );
		if ($return = JRequest::getVar('return', '','get'))
		{
			$this->setRedirect(base64_decode($return));
		}
	}
}