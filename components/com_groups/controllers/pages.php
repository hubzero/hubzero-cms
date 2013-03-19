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

//import hubzero controller lib
ximport('Hubzero_Controller');

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
		$this->cn 		= JRequest::getCmd('cn', '');
		$this->active 	= JRequest::getCmd('active', '');
		$this->action 	= JRequest::getCmd('action', '');
		
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
		$this->view->setLayout('display');
		
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
		
		// Import the wiki parser
		$this->view->wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => '',
			'pagename' => $this->view->group->get('cn'),
			'pageid'   => $this->view->group->get('gidNumber'),
			'filepath' => $this->config->get('uploadpath'),
			'domain'   => $this->view->group->get('cn')
		);

		ximport('Hubzero_Wiki_Parser');
		$this->view->parser =& Hubzero_Wiki_Parser::getInstance();
		
		// Instantiate group page and module object
		$GPage = new GroupPages($this->database);
		
		// Get the group pages
		$pages = $GPage->getPages($this->view->group->get('gidNumber'));

		// Seperate active/inactive pages
		$this->view->active_pages = array();
		$this->view->inactive_pages = array();

		foreach ($pages as $page)
		{
			if ($page['active'] == 1) 
			{
				array_push($this->view->active_pages, $page);
			} 
			else 
			{
				array_push($this->view->inactive_pages, $page);
			}
		}
		
		// Get the highest page order
		$this->view->high_order_pages = $GPage->getHighestPageOrder($this->view->group->get('gidNumber'));
		
		//build pathway
		$this->_buildPathway();
		
		//build title
		$this->_buildTitle();
		
		//push styles
		$this->_getStyles();
		
		//push scripts
		$this->_getScripts('assets/js/' . $this->_name);
		
		//set view vars
		$this->view->title  = 'Manage Custom Content: ' . $this->view->group->get('description');
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		
		//display
		$this->view->display();
	}
	
	
	/**
	 * Add Group Page
	 * 
	 * @return 	void
	 */
	public function addPageTask()
	{
		$this->editPageTask();
	}
	
	
	/**
	 * Edit Group Page
	 * 
	 * @return 	void
	 */
	public function editPageTask()
	{
		//set to edit layout
		$this->view->setLayout('edit');
		
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
		
		//get request vars
		$this->view->page = JRequest::getVar('page','','get');
		if ($this->view->page) 
		{
			$GPage = new GroupPages($this->database);
			$GPage->load($this->view->page);

			$this->view->page 				= array();
			$this->view->page['id'] 		= $GPage->id;
			$this->view->page['gid']		= $GPage->gid;
			$this->view->page['url']		= $GPage->url;
			$this->view->page['title'] 		= $GPage->title;
			$this->view->page['content']	= $GPage->content;
			$this->view->page['porder']		= $GPage->porder;
			$this->view->page['active'] 	= $GPage->active;
			$this->view->page['privacy']	= $GPage->privacy;
		}
		
		//are we passing in a page from someplage else
		if ($this->page) 
		{
			$this->view->page = $this->page;
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
		
		//display layout
		$this->view->display();
	}
	
	
	/**
	 * Save Group page
	 * 
	 * @return 	void
	 */
	public function savePageTask()
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
		
		// Get the page vars being posted
		$page = JRequest::getVar('page',array(),'post','none',2);
		
		// Check if the page title is set
		if (trim($page['title']) == '') 
		{
			$this->setNotification('You must enter a page title.','error');
			$this->page = $page;
			$this->editPageTask();
			return;
		}

		// Check if the page title is set
		if (trim($page['content']) == '') 
		{
			$this->setNotification('You must enter page content.','error');
			$this->page = $page;
			$this->editPageTask();
			return;
		}
		
		// Default task
		$task = 'update';

		// Instantiate group page object
		$GPage = new GroupPages($this->database);

		// If new page we must create extra vars
		if (!$page['id']) 
		{
			$high = $GPage->getHighestPageOrder($this->view->group->get('gidNumber'));

			$page['gid'] = $this->view->group->get('gidNumber');
			$page['active'] = 1;
			$page['porder'] = ($high + 1);

			$task = 'create';
		}

		// Get the group pages
		$pages = $GPage->getPages( $this->view->group->get('gidNumber') );

		//check to see if user supplied url
		if ($task == 'create')
		{
			if (isset($page['url']) && $page['url'] != '')
			{
				$page['url'] = strtolower(str_replace(' ', '_', trim($page['url'])));
			}
			else
			{
				$page['url'] = strtolower(str_replace(' ', '_', trim($page['title'])));
			}
		}
		
		//remove unwanted chars
		$invalid_chrs = array("?","!",">","<",",",".",";",":","`","~","@","#","$","%","^","&","*","(",")","-","=","+","/","\/","|","{","}","[","]");
		$page['url'] = str_replace("'", '', $page['url']);
		$page['url'] = str_replace('"', '', $page['url']);
		$page['url'] = str_replace($invalid_chrs, '', $page['url']);

		// Get unique page name
		$page['url'] = $this->_uniqueGroupPageURL($page['url'],$pages, $this->view->group, $page['id']);

		// Save the page
		if (!$GPage->save($page)) 
		{
			$this->setNotification("An error occurred while trying to {$task} the page.", 'error');
		}

		// Push success message and redirect
		$this->setNotification("You have successfully {$task}d the page.", 'passed');
		$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $this->view->group->get('cn') . '&task=pages') );
	}
	
	
	/**
	 * Activate Group Page
	 * 
	 * @return 	void
	 */
	public function activatePageTask()
	{
		$this->togglePageStateTask( true );
	}
	
	
	/**
	 * De-Activate Group Page
	 * 
	 * @return 	void
	 */
	public function deactivatePageTask()
	{
		$this->togglePageStateTask( false );
	}
	
	
	/**
	 * Toggle Page Active State
	 * 
	 * @return 	void
	 */
	public function togglePageStateTask( $activatePage = true )
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
		
		//get request vars
		$page = JRequest::getInt('page', '');
		
		//load group page object
		$GPage = new GroupPages($this->database);
		$GPage->load( $page );
		
		//do we want to activate or de-activate page
		if ($activatePage)
		{
			$GPage->active = 1;
			$status = 'activate';
		}
		else
		{
			$GPage->active = 0;
			$status = 'deactivate';
		}
		
		//save page
		$GPage->save( $GPage );
		
		//inform user
		$this->setNotification('The group page was successfully ' . $status . 'd.', 'passed');
		
		//redirect
		$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $this->view->group->get('cn') . '&task=pages') );
	}
	
	
	/**
	 * Reorder Page Up
	 * 
	 * @return 	void
	 */
	public function upPageTask()
	{
		$this->reorderPageTask('up');
	}
	
	
	/**
	 * Reorder Page Down
	 * 
	 * @return 	void
	 */
	public function downPageTask()
	{
		$this->reorderPageTask('down');
	}
	
	
	/**
	 * Reorder Page up & down
	 * 
	 * @return 	void
	 */
	public function reorderPageTask( $direction = 'up' )
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
		
		//get request vars
		$page = JRequest::getInt('page', '');
		
		//load group page object
		$GPage = new GroupPages($this->database);
		$GPage->load( $page );
		
		//set lowest, highest, and current orders
		$lowestOrder = 1;
		$highestOrder = $GPage->getHighestPageOrder($this->view->group->get('gidNumber'));
		$currentOrder = $GPage->porder;
		
		//move page up or down
		if ($direction == 'down')
		{
			$newOrder = $currentOrder + 1;
			if ($newOrder > $highestOrder)
			{
				$newOrder = $highestOrder;
			}
		}
		else
		{
			$newOrder = $currentOrder - 1;
			if($newOrder < $lowestOrder)
			{
				$newOrder = $lowestOrder;
			}
		}
		
		// Check to see if another object holds the order we are trying to move to
		$sql = "SELECT *  FROM #__xgroups_pages WHERE porder='" . $newOrder . "' AND gid='" . $this->view->group->get('gidNumber') . "'";
		$this->database->setQuery($sql);
		$new = $this->database->loadResult();
		
		if (!$new)
		{
			$sql = "UPDATE #__xgroups_pages SET porder='" . $newOrder . "' WHERE id='" . $page . "'";
			$this->database->setQuery($sql);
		}
		else
		{
			// Otherwise basically switch the two objects orders
			$sql = "UPDATE #__xgroups_pages SET porder='" . $newOrder . "' WHERE id='" . $page . "'";
			$this->database->setQuery($sql);
			$this->database->query();

			$sql = "UPDATE #__xgroups_pages SET porder='" . $currentOrder . "' WHERE id='" . $new . "'";
			$this->database->setQuery($sql);
			$this->database->query();
		}
		
		//inform user
		$this->setNotification('The group pages were successfully reordered.', 'passed');
		
		// Redirect back to manage pages area
		$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $this->view->group->get('cn') . '&task=pages') );
	}
	
	
	/**
	 * Generate a unique page URL
	 * 
	 * @param      string $current_url Current URL
	 * @param      array  $group_pages List of group pages
	 * @param      object $group       Hubzero_Group
	 * @return     string
	 */
	private function _uniqueGroupPageURL($current_url, $group_pages, $group, $current_id = null)
	{
		//remove the current page so we dont check it
		foreach ($group_pages as $k => $v)
		{
			if ($current_id != null && $current_id == $v['id'])
			{
				unset($group_pages[$k]);
			}
		}
		
		// Get the page urls
		$page_urls = array_keys($group_pages);
		
		// Get plugin names
		$plugin_names = array_keys(Hubzero_Group_Helper::getPluginAccess($group));
		
		//are we trying to name a group page the same as a group plugin name
		if (in_array($current_url, $plugin_names)) 
		{
			$current_url = $current_url . '_page';
			return $this->_uniqueGroupPageURL($current_url, $group_pages, $group);
		}
		
		// Check if current url is already taken
		// otherwise return current url
		if (in_array($current_url, $page_urls)) 
		{
			// Split up the current url
			$url_parts = explode('_', $current_url);
			
			// Get the last part of the split url
			$num = end($url_parts);
			
			// If last part is numeric we need to remove that part from array and increment number then append back on end of url
			// else append a number to the end of the url
			if (is_numeric($num)) 
			{
				$num++;
				$oldNum = array_pop($url_parts);
				$url  = implode('_', $url_parts);
				$url .= "_{$num}";
			} 
			else 
			{
				$count = 1;
				$url  = implode('_', $url_parts);
				$url .= "_{$count}";
			}
			
			// Run the function again to see if we now have a unique url
			return $this->_uniqueGroupPageURL($url, $group_pages, $group);
		} 
		else 
		{
			return $current_url;
		}
	}
}