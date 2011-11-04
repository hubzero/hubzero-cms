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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Manage organizations for registration
 */
class RegisterControllerOrganizations extends Hubzero_Controller
{
	/**
	 * Display all organizations
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Get filters
		$this->view->filters = array();
		$this->view->filters['search'] = urldecode($app->getUserStateFromRequest(
			$this->_option . '.organizations.search', 
			'search', 
			''
			));
		$this->view->filters['show']   = '';

		// Get paging variables
		$this->view->filters['limit']  = $app->getUserStateFromRequest(
			$this->_option . '.organizations.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']  = $app->getUserStateFromRequest(
			$this->_option . '.organizations.limitstart', 
			'limitstart', 
			0, 
			'int'
		);

		$obj = new RegisterOrganization($this->database);

		// Get a record count
		$this->view->total = $obj->getCount($this->view->filters);

		// Get records
		$this->view->rows = $obj->getRecords($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Add a new organization
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit an organization
	 * 
	 * @return     void
	 */
	public function editTask($model=null)
	{
		$this->view->setLayout('edit');
		
		if (is_object($model))
		{
			$this->view->model = $model;
		}
		else 
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

			// Initiate database class and load info
			$this->view->model = new RegisterOrganization($this->database);
			$this->view->model->load($id);
		}

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save an organization
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Load the tag object and bind the incoming data to it
		$model = new RegisterOrganization($this->database);
		
		if (!$model->bind($_POST)) 
		{
			JError::raiseError(500, $model->getError());
			return;
		}

		// Check content
		if (!$model->check()) 
		{
			$this->setError($model->getError());
			$this->editTask($model);
			return;
		}

		// Store new content
		if (!$model->store()) 
		{
			$this->setError($model->getError());
			$this->editTask($model);
			return;
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('REGISTER_ORG_SAVED')
		);
	}

	/**
	 * Remove an organization
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array(), 'post');

		// Get the single ID we're working with
		if (!is_array($ids)) 
		{
			$ids = array();
		}

		// Do we have any IDs?
		if (!empty($ids)) 
		{
			$model = new RegisterOrganization($this->database);

			// Loop through each ID and delete the necessary items
			foreach ($ids as $id) 
			{
				// Remove the organization type
				$model->delete(intval($id));
			}
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('REGISTER_ORG_REMOVED')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
	}
}
