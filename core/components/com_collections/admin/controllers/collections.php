<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-20115 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Collections\Admin\Controllers;

use Components\Collections\Models\Archive;
use Components\Collections\Models\Collection;
use Hubzero\Component\AdminController;
use Request;
use Config;
use Route;
use Lang;
use User;
use App;

/**
 * Controller class for Collections
 */
class Collections extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display a list of all categories
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
			'state'  => -1,
			'access' => -1,
			'object_type' => Request::getState(
				$this->_option . '.' . $this->_controller . '.object_type',
				'object_type',
				''
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				'-1'
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

		$obj = new Archive();

		// Get record count
		$this->view->filters['count'] = true;
		$this->view->total = $obj->collections($this->view->filters);

		// Get records
		$this->view->filters['count'] = false;
		$this->view->rows  = $obj->collections($this->view->filters);

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit a collection
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));

			if (is_array($id))
			{
				$id = (!empty($id) ? $id[0] : 0);
			}

			// Load category
			$row = new Collection($id);
		}

		$this->view->row = $row;

		if (!$this->view->row->exists())
		{
			$this->view->row->set('created_by', User::get('id'));
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
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);

		// Initiate extended database class
		$row = new Collection($fields['id']);
		if (!$row->bind($fields))
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store(true))
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		// Process tags
		//$row->tag(trim(Request::getVar('tags', '')));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_COLLECTIONS_COLLECTION_SAVED')
		);
	}

	/**
	 * Delete one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) > 0)
		{
			// Loop through all the IDs
			foreach ($ids as $id)
			{
				$entry = new Collection(intval($id));
				// Delete the entry
				if (!$entry->delete())
				{
					$this->setError($entry->getError());
				}
			}
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			($this->getError() ? implode('<br />', $this->getErrors()) : Lang::txt('COM_COLLECTIONS_ITEMS_DELETED')),
			($this->getError() ? 'error' : null)
		);
	}

	/**
	 * Set the access level of an entry to 'public'
	 *
	 * @return  void
	 */
	public function accesspublicTask()
	{
		return $this->accessTask(0);
	}

	/**
	 * Set the access level of an entry to 'registered'
	 *
	 * @return  void
	 */
	public function accessregisteredTask()
	{
		return $this->accessTask(1);
	}

	/**
	 * Set the access level of an entry to 'special'
	 *
	 * @return  void
	 */
	public function accessspecialTask()
	{
		return $this->accessTask(2);
	}

	/**
	 * Set the access level of an entry to 'special'
	 *
	 * @return  void
	 */
	public function accessprivateTask()
	{
		return $this->accessTask(4);
	}

	/**
	 * Set the access level of an entry
	 *
	 * @param   integer  $access  Access level to set
	 * @return  void
	 */
	public function accessTask($access=0)
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$id = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_COLLECTIONS_ERROR_SELECT_ITEMS'),
				'error'
			);
			return;
		}

		// Load the entry
		$row = new Collection($id);
		$row->set('access', $access);

		// Check and store the changes
		if (!$row->store())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$row->getError(),
				'error'
			);
			return;
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @param   integer  $state  The state to set entries to
	 * @return  void
	 */
	public function stateTask($state=0)
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$state = $this->getTask() == 'publish' ? 1 : 0;

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for a resource
		if (count($ids) < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_COLLECTIONS_ERROR_SELECT_TO', $this->_task),
				'error'
			);
			return;
		}

		// Loop through all the IDs
		$success = 0;
		foreach ($ids as $id)
		{
			// Load the article
			$row = new Collection(intval($id));
			$row->set('state', $state);

			// Store new content
			if (!$row->store())
			{
				$this->setError($row->getError());
				continue;
			}
			$success++;
		}

		switch ($this->_task)
		{
			case 'publish':
				$message = Lang::txt('COM_COLLECTIONS_ITEMS_PUBLISHED', $success);
			break;
			case 'unpublish':
				$message = Lang::txt('COM_COLLECTIONS_ITEMS_UNPUBLISHED', $success);
			break;
			case 'archive':
				$message = Lang::txt('COM_COLLECTIONS_ITEMS_TRASHED', $success);
			break;
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			$message
		);
	}
}

