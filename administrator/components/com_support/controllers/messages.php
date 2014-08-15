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

/**
 * Support controller class for message templates
 */
class SupportControllerMessages extends \Hubzero\Component\AdminController
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
		$config = JFactory::getConfig();

		// Get paging variables
		$this->view->filter = array();
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.messages.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.messages.limitstart',
			'limitstart',
			0,
			'int'
		);

		$model = new SupportMessage($this->database);

		// Record count
		$this->view->total = $model->getCount($this->view->filters);

		// Fetch results
		$this->view->rows  = $model->getRecords($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError()) {
			$this->view->setError($this->getError());
		}

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
			if (is_array($id))
			{
				$id = (!empty($id) ? $id[0] : 0);
			}

			// Initiate database class and load info
			$this->view->row = new SupportMessage($this->database);
			$this->view->row->load($id);
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
	 * Save changes to a record and return to edit form
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
		$msg = JRequest::getVar('msg', array(), 'post');
		$msg = array_map('trim', $msg);

		// Initiate class and bind posted items to database fields
		$row = new SupportMessage($this->database);
		if (!$row->bind($msg))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Code cleaner for xhtml transitional compliance
		$row->title   = trim($row->title);
		$row->message = trim($row->message);

		// Check content
		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($redirect)
		{
			// Output messsage and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_SUPPORT_MESSAGE_SUCCESSFULLY_SAVED')
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
				JText::_('COM_SUPPORT_ERROR_SELECT_MESSAGE_TO_DELETE'),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Delete message
			$msg = new SupportMessage($this->database);
			$msg->delete(intval($id));
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::sprintf('COM_SUPPORT_MESSAGE_SUCCESSFULLY_DELETED', count($ids))
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}
