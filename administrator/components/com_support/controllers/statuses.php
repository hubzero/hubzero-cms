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
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . 'status.php');

/**
 * Support controller class for managing ticket statuses
 */
class SupportControllerStatuses extends \Hubzero\Component\AdminController
{
	/**
	 * Displays a list of records
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = JFactory::getApplication();

		// Get paging variables
		$this->view->filters = array();
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			JFactory::getConfig()->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['open'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.open',
			'open',
			-1,
			'int'
		);
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		// Get sorting variables
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'open'
		));
		$this->view->filters['sort_Dir']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'DESC'
		));

		$obj = new SupportTableStatus($this->database);

		// Record count
		$this->view->total = $obj->find('count', $this->view->filters);

		// Fetch results
		$this->view->rows  = $obj->find('list', $this->view->filters);

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new record
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Display a form for adding/editing a record
	 *
	 * @return	void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming
			$id = JRequest::getVar('id', array(0));
			$id = (is_array($id) ? $id[0] : $id);

			// Initiate database class and load info
			$this->view->row = new SupportModelStatus($id);
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->setLayout('edit')->display();
	}

	/**
	 * Save a record and return to edit form
	 *
	 * @return	void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save changes to a record
	 *
	 * @return	void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Trim and addslashes all posted items
		$fields = JRequest::getVar('fields', array(), 'post');

		// Initiate class and bind posted items to database fields
		$row = new SupportModelStatus($fields);

		// Store new content
		if (!$row->store(true))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Output messsage and redirect
		if ($redirect)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_SUPPORT_STATUS_SUCCESSFULLY_SAVED')
			);
		}

		$this->editTask($row);
	}

	/**
	 * Delete one or more records
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_SUPPORT_ERROR_SELECT_STATUS_TO_DELETE'),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Delete message
			$row = new SupportModelStatus(intval($id));
			$row->delete();
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::sprintf('COM_SUPPORT_STATUS_SUCCESSFULLY_DELETED', count($ids))
		);
	}
}
