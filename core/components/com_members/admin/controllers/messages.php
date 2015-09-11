<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Message;
use Notify;
use Request;
use Config;
use Route;
use Lang;
use App;

/**
 * Manage messaging settings
 */
class Messages extends AdminController
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

		$obj = new Message\Component($this->database);

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
			$row = new Message\Component($this->database);
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
		Request::checkToken();

		// Incoming profile edits
		$fields = Request::getVar('fields', array(), 'post');

		// Load the profile
		$row = new Message\Component($this->database);
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
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (!empty($ids))
		{
			$notify = new Message\Notify($this->database);

			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$id = intval($id);

				$row = new Message\Component($this->database);
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

