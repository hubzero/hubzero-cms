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
ximport('Hubzero_Message_Component');

/**
 * Manage site members
 */
class MembersControllerMessages extends Hubzero_Controller
{
	/**
	 * Display a list of site members
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();

		// Get filters
		$this->view->filters = array();
		$this->view->filters['component'] = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.component', 
			'component', 
			''
		));
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort', 
			'sort', 
			'c.name'
		));
		$this->view->filters['sort_Dir']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir', 
			'sort_Dir', 
			'ASC'
		));

		// Get paging variables
		$this->view->filters['limit']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart', 
			'limitstart', 
			0, 
			'int'
		);

		$obj = new Hubzero_Message_Component($this->database);

		// Get a record count
		$this->view->total = $obj->getCount($this->view->filters, true);

		// Get records
		$this->view->rows = $obj->getRecords($this->view->filters, true);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);
		
		$this->view->components = $obj->getComponents();

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new member
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit a member's information
	 * 
	 * @param      integer $id ID of member to edit
	 * @return     void
	 */
	public function editTask($id=0)
	{
		if (!$id) 
		{
			// Incoming
			$ids = JRequest::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($ids)) 
			{
				$id = (!empty($ids)) ? $ids[0] : 0;
			} 
			else 
			{
				$id = 0;
			}
		}

		// Initiate database class and load info
		$this->view->row = new Hubzero_Message_Component($this->database);
		$this->view->row->load($id);

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Short description for 'apply'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function applyTask()
	{
		$this->saveTask(0);
	}

	/**
	 * Short description for 'save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $redirect Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function saveTask($redirect=1)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Incoming profile edits
		$fields = JRequest::getVar('fields', array(), 'post');

		// Load the profile
		$row = new Hubzero_Message_Component($this->database);
		if (!$row->bind($fields)) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}
		
		// Check content
		if (!$row->check()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Store content
		if (!$row->store()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}
		
		// Redirect
		if ($redirect) 
		{
			// Redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Message Action saved'),
				'message'
			);
		} 
		else 
		{
			$this->view->setLayout('edit');
			$this->editTask($fields['id']);
		}
	}

	/**
	 * Removes a profile entry, associated picture, and redirects to main listing
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('ids', array());

		// Do we have any IDs?
		if (!empty($ids)) 
		{
			ximport('Hubzero_Message_Notify');
			$notify = new Hubzero_Message_Notify($this->database);
			
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$id = intval($id);
				
				$row = new Hubzero_Message_Component($this->database);
				$row->load($id);

				// Remove any associations
				$notify->deleteType($row->action);

				// Remove the record
				$row->delete($id);
			}
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Message Action removed')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
	}
}

