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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Manage members password blacklist
 */
class MembersControllerPasswordBlacklist extends \Hubzero\Component\AdminController
{
	/**
	 * Display password blacklist
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit'] = $app->getUserStateFromRequest($this->_option . '.' . $this->_controller . '.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$this->view->filters['start'] = $app->getUserStateFromRequest($this->_option . '.' . $this->_controller . '.limitstart', 'limitstart', 0, 'int');

		// Get password rules object
		$pbObj = new MembersPasswordBlacklist($this->database);

		$this->view->pw_blacklist = $pbObj->getRecords($this->view->filters);

		// Get records and count
		$this->view->total = $pbObj->getCount($this->view->filters);
		$this->view->rows  = $pbObj->getRecords($this->view->filters);

		// Initiate pagination
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination($this->view->total, $this->view->filters['start'], $this->view->filters['limit']);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new blacklisted password
	 *
	 * @return     void
	 */
	public function addTask()
	{
		// Output the HTML
		$this->editTask();
	}

	/**
	 * Edit a blacklisted password
	 *
	 * @param      integer $id ID of word to edit
	 * @return     void
	 */
	public function editTask($id=0)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		if (!$id)
		{
			// Incoming
			$id = JRequest::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}
		}

		// Initiate database class and load info
		$this->view->row = new MembersPasswordBlacklist($this->database);
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
	 * Apply changes to a password blacklist item
	 *
	 * @return     void
	 */
	public function applyTask()
	{
		// Save without redirect
		$this->saveTask(0);
	}

	/**
	 * Save blacklisted password
	 *
	 * @param      integer $redirect - whether or not to redirect after save
	 * @return     boolean Return description (if any) ...
	 */
	public function saveTask($redirect=1)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming password blacklist edits
		$fields = JRequest::getVar('fields', array(), 'post');

		// Load the profile
		$row = new MembersPasswordBlacklist($this->database);

		// Try to save
		if (!$row->save($fields))
		{
			// Redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$row->getError(),
				'error'
			);
			return;
		}

		// Redirect
		if ($redirect)
		{
			// Redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('PASSWORD_BLACKLIST_SAVE_SUCCESS'),
				'message'
			);
		}
		else
		{
			$this->view->setLayout('edit');
			$this->view->task = 'edit';
			$this->editTask($row->id);
		}
	}

	/**
	 * Removes [a] password blacklist item(s)
	 *
	 * @return     void
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
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$id = intval($id);

				$row = new MembersPasswordBlacklist($this->database);

				// Remove the record
				$row->delete($id);
			}
		}
		else // no rows were selected
		{
			// Output message and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('PASSWORD_BLACKLIST_DELETE_NO_ROW_SELECTED'),
				'warning'
			);
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('PASSWORD_BLACKLIST_DELETE_SUCCESS')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}