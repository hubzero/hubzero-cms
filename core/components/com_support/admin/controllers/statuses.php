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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Support\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Support\Models\Status;
use Components\Support\Tables;
use Request;
use Config;
use Route;
use Lang;
use App;

include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'status.php');

/**
 * Support controller class for managing ticket statuses
 */
class Statuses extends AdminController
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

		parent::execute();
	}

	/**
	 * Displays a list of records
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get paging variables
		$this->view->filters = array(
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
			),
			'open' => Request::getState(
				$this->_option . '.' . $this->_controller . '.open',
				'open',
				-1,
				'int'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'open'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			)
		);

		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		$obj = new Tables\Status($this->database);

		// Record count
		$this->view->total = $obj->find('count', $this->view->filters);

		// Fetch results
		$this->view->rows  = $obj->find('list', $this->view->filters);

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Display a form for adding/editing a record
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			$id = (is_array($id) ? $id[0] : $id);

			// Initiate database class and load info
			$row = new Status($id);
		}

		$this->view->row = $row;

		// Set any errors
		if ($this->getError())
		{
			\Notify::error($this->getError());
		}

		// Output the HTML
		$this->view->setLayout('edit')->display();
	}

	/**
	 * Save changes to a record
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Trim and addslashes all posted items
		$fields = Request::getVar('fields', array(), 'post');

		// Initiate class and bind posted items to database fields
		$row = new Status($fields);

		// Store new content
		if (!$row->store(true))
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		// Output messsage and redirect
		if ($this->_task == 'apply')
		{
			return $this->editTask($row);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_SUPPORT_STATUS_SUCCESSFULLY_SAVED')
		);
	}

	/**
	 * Delete one or more records
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

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_SUPPORT_ERROR_SELECT_STATUS_TO_DELETE'),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Delete message
			$row = new Status(intval($id));
			$row->delete();
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_SUPPORT_STATUS_SUCCESSFULLY_DELETED', count($ids))
		);
	}
}
