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
 * Manage messaging settings
 */
class MembersControllerMessages extends \Hubzero\Component\AdminController
{
	/**
	 * Display a list of messaging settings
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
			'component' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.component',
				'component',
				''
			)),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'c.name'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			// Get paging variables
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			)
		);

		$obj = new \Hubzero\Message\Component($this->database);

		// Get a record count
		$this->view->total = $obj->getCount($this->view->filters, true);

		// Get records
		$this->view->rows = $obj->getRecords($this->view->filters, true);

		$this->view->components = $obj->getComponents();

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new record
	 *
	 * @return     void
	 */
	public function addTask()
	{
		// Output the HTML
		$this->editTask();
	}

	/**
	 * Edit a record
	 *
	 * @param      object $row Database row
	 * @return     void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			// Initiate database class and load info
			$row = new \Hubzero\Message\Component($this->database);
			$row->load($id);
		}

		$this->view->row = $row;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			Notify::error($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry and display edit form
	 *
	 * @return     void
	 */
	public function applyTask()
	{
		$this->saveTask();
	}

	/**
	 * Save an entry and redirect to main listing
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming profile edits
		$fields = Request::getVar('fields', array(), 'post');

		// Load the profile
		$row = new \Hubzero\Message\Component($this->database);
		if (!$row->bind($fields))
		{
			$this->setError($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store content
		if (!$row->store())
		{
			$this->setError($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		Notify::success(Lang::txt('Message Action saved'));

		// Redirect
		if ($this->_task == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Delete a record
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (!empty($ids))
		{
			$notify = new \Hubzero\Message\Notify($this->database);

			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$id = intval($id);

				$row = new \Hubzero\Message\Component($this->database);
				$row->load($id);

				// Remove any associations
				$notify->deleteType($row->action);

				// Remove the record
				$row->delete($id);
			}
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('Message Action removed')
		);
	}
}

