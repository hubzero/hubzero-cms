<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'organizationtype.php');

/**
 * Manage employer types for registration
 */
class MembersControllerEmployers extends \Hubzero\Component\AdminController
{
	/**
	 * Display all employer types
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		// Get filters
		$this->view->filters = array(
			'search' => urldecode($app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			// Get paging variables
			'limit' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				$config->getValue('config.list_limit'),
				'int'
			),
			'start' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			'sort' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sort_Dir',
				'filter_order_Dir',
				'ASC'
			)
		);
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		$obj = new MembersTableOrganizationType($this->database);

		// Get a record count
		$this->view->total = $obj->find('count', $this->view->filters);

		// Get records
		$this->view->rows  = $obj->find('list', $this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Add a new employer type
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit an employer type
	 *
	 * @param   mixed  $model  MembersTableOrganizationType
	 * @return  void
	 */
	public function editTask($model=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		if (is_object($model))
		{
			$this->view->model = $model;
		}
		else
		{
			// Incoming
			$id = JRequest::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			// Initiate database class and load info
			$this->view->model = new MembersTableOrganizationType($this->database);
			$this->view->model->load($id);
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a record and return to edit form
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save a record
	 *
	 * @param   boolean  $redirect  Redirect after saving?
	 * @return  void
	 */
	public function saveTask($redirect = true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Load the tag object and bind the incoming data to it
		$model = new MembersTableOrganizationType($this->database);

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

		if ($redirect)
		{
			// Output messsage and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_MEMBERS_ORGTYPE_SAVED')
			);
			return;
		}

		$this->editTask($model);
	}

	/**
	 * Remove an employer type
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (!empty($ids))
		{
			$orgtype = new MembersTableOrganizationType($this->database);

			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				// Remove the organization type
				$orgtype->delete(intval($id));
			}
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_MEMBERS_ORGTYPE_REMOVED')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}
