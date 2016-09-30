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

namespace Components\Support\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Support\Models\Status;
use Components\Support\Tables;
use Request;
use Config;
use Notify;
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
	 * @return  void
	 */
	public function displayTask()
	{
		// Get paging variables
		$filters = array(
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

		$filters['start'] = ($filters['limit'] != 0 ? (floor($filters['start'] / $filters['limit']) * $filters['limit']) : 0);

		$obj = new Tables\Status($this->database);

		// Record count
		$total = $obj->find('count', $filters);

		// Fetch results
		$rows  = $obj->find('list', $filters);

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('total', $total)
			->set('rows', $rows)
			->display();
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

		// Set any errors
		if ($this->getError())
		{
			Notify::error($this->getError());
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
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
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_SUPPORT_STATUS_SUCCESSFULLY_SAVED'));

		// Output messsage and redirect
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
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
			Notify::warning(Lang::txt('COM_SUPPORT_ERROR_SELECT_STATUS_TO_DELETE'));
			return $this->cancelTask();
		}

		$i = 0;
		foreach ($ids as $id)
		{
			// Delete entry
			$row = new Status(intval($id));

			if (!$row->delete())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		// Output messsage and redirect
		if ($i)
		{
			Notify::success(Lang::txt('COM_SUPPORT_STATUS_SUCCESSFULLY_DELETED', $i));
		}

		$this->cancelTask();
	}
}
