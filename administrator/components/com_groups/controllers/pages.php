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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Groups controller class for managing group pages
 */
class GroupsControllerPages extends Hubzero_Controller
{
	/**
	 * Manage group pages
	 *
	 * @return void
	 */
	public function displayTask()
	{
		// Incoming
		$gid = JRequest::getVar('gid', '');

		// Ensure we have a group ID
		if (!$gid)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=manage',
				JText::_('COM_GROUPS_MISSING_ID'),
				'error'
			);
			return;
		}

		// Load the group page
		$this->view->group = new Hubzero_Group();
		$this->view->group->read($gid);

		//$this->gid = $gid;
		//$this->group = $group;

		$action = JRequest::getVar('action','');

		$this->action = $action;
		$this->authorized = 'admin';

		// Do we need to perform any actions?
		$out = '';
		if ($action)
		{
			$action = strtolower(trim($action));
			$action = str_replace(' ', '', $action);

			// Perform the action
			if (in_array($action, $this->_taskMap))
			{
				$action .= 'Task';
				$this->$action();
			}

			// Did the action return anything? (HTML)
			if ($this->output != '')
			{
				$out = $this->output;
			}
		}

		//get the group pages
		$gp = new GroupPages($this->database);
		$this->view->pages = $gp->getPages($this->view->group->get('gidNumber'), false);

		// Output HTML
		/*if ($out == '')
		{
			$this->view->group      = $group;
			//$this->view->authorized = 'admin';
			$this->view->pages      = $pages;
			// Set any errors
			if ($this->getError())
			{
				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}
			}

			// Output the HTML
			$this->view->display();
		}
		else
		{
			echo $out;
		}*/

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a group page
	 *
	 * @return void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a group page
	 *
	 * @return void
	 */
	public function editTask($page = null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Incoming
		$gid = JRequest::getVar('gid', '');
		$p   = JRequest::getVar('page', '');

		// Check to make sure we have an id if we're editing
		if (!$p && $this->task == 'edit')
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $gid,
				JText::_('Missing page ID'),
				'error'
			);
			return;
		}

		// load group page data
		if (is_object($page))
		{
			$this->view->page = $page;
		}
		else 
		{
			$this->view->page = new GroupPages($this->database);
			$this->view->page->load($p);
		}

		// Load the group page
		$this->view->group = new Hubzero_Group();
		$this->view->group->read($gid);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save a group page
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// load the request vars
		$page = JRequest::getVar('page', array());

		// instatiate group page object for saving
		$db =& JFactory::getDBO();
		$Gpage = new GroupPages($db);

		// Load the group page
		$gid = JRequest::getVar('gid','');
		$group = new Hubzero_Group();
		$group->read($gid);

		// new page
		if (!$page['id'])
		{
			$high = $Gpage->getHighestPageOrder($group->get('gidNumber'));
			$page['porder'] = ($high + 1);
		}

		// check to see if user supplied url
		if (isset($page['url']) && $page['url'] != '')
		{
			$page['url'] = strtolower(str_replace(' ', '_', trim($page['url'])));
		}
		else
		{
			$page['url'] = strtolower(str_replace(' ', '_', trim($page['title'])));
		}

		// remove unwanted chars
		$invalid_chrs = array("?","!",">","<",",",".",";",":","`","~","@","#","$","%","^","&","*","(",")","-","=","+","/","\/","|","{","}","[","]");
		$page['url'] = str_replace("'", "", $page['url']);
		$page['url'] = str_replace('"', '', $page['url']);
		$page['url'] = str_replace($invalid_chrs, '', $page['url']);

		// save page
		if (!$Gpage->save($page))
		{
			$this->addComponentMessage(JText::_('Error occurred'), 'error');
			$this->editTask($Gpage);
			return;
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $gid
		);
	}

	/**
	 * Cancel a group page task
	 *
	 * @return void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . JRequest::getVar('gid', '')
		);
	}
}
