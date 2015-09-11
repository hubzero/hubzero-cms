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

namespace Components\Tools\Admin\Controllers;

use Components\Tools\Helpers\Utils;
use Hubzero\Component\AdminController;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use App;

include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'hosttype.php');

/**
 * Tools controller for host types
 */
class Hosttypes extends AdminController
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
	 * Display a list of host types
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
			// Sorting
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'value'
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
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		// Get the middleware database
		$mwdb = Utils::getMWDBO();

		$model = new \Components\Tools\Tables\Hosttype($mwdb);

		$this->view->total = $model->getCount($this->view->filters);

		$this->view->rows = $model->getRecords($this->view->filters);

		// Form the query and show the table.
		//$mwdb->setQuery("SELECT * FROM hosttype ORDER BY VALUE");
		//$this->view->rows = $mwdb->loadObjectList();
		if ($this->view->rows)
		{
			foreach ($this->view->rows as $key => $row)
			{
				$this->view->rows[$key]->refs = $this->_refs($row->value);
			}
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Display results
		$this->view->display();
	}

	/**
	 * Edit a record
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row = null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$item = Request::getVar('item', '', 'get');

			$mwdb = Utils::getMWDBO();

			$row = new \Components\Tools\Tables\Hosttype($mwdb);
			$row->load($item);
		}

		$this->view->row = $row;

		if ($this->view->row->value > 0)
		{
			$this->view->bit = log($this->view->row->value)/log(2);
		}
		else
		{
			$this->view->bit = '';
		}

		$this->view->refs = $this->_refs($this->view->row->value);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Display results
		$this->view
			->set('status', (isset($item) && $item != '') ? 'exists' : 'new')
			->setLayout('edit')
			->display();
	}

	/**
	 * Get a count of references
	 *
	 * @param   mixed    $value
	 * @return  integer
	 */
	private function _refs($value)
	{
		$refs = 0;

		// Get the middleware database
		$mwdb = Utils::getMWDBO();
		$mwdb->setQuery("SELECT count(*) AS count FROM host WHERE provisions & " . $mwdb->Quote($value) . " != 0");
		$elts = $mwdb->loadObjectList();
		if ($elts)
		{
			$elt  = $elts[0];
			$refs = $elt->count;
		}

		return $refs;
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

		// Get the middleware database
		$mwdb = Utils::getMWDBO();

		$fields = Request::getVar('fields', array(), 'post');

		$row = new \Components\Tools\Tables\Hosttype($mwdb);
		if (!$row->bind($fields))
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$insert = false;
		if ($fields['status'] == 'new')
		{
			$insert = true;
		}

		if (!$fields['value'])
		{
			$rows = $row->getRecords();

			$value = 1;
			for ($i=0; $i<count($rows); $i++)
			{
				if ($value == $rows[$i]->value)
				{
					$value = $value * 2;
				}
				// Double check that the hosttype doesn't already exist
				if ($row->name == $rows[$i]->name)
				{
					$insert = false;
				}
			}

			$row->value = $value;
		}

		// Check content
		if (!$row->check())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Store new content
		if ($fields['status'] == 'new')
		{
			$result = $row->store($insert);
		}
		else
		{
			$result = $row->update($fields['id']);
		}

		if (!$result)
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_TOOLS_ITEM_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Delete a hostname record
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());

		$mwdb = Utils::getMWDBO();

		if (count($ids) > 0)
		{
			$row = new \Components\Tools\Tables\Hosttype($mwdb);

			// Loop through each ID
			foreach ($ids as $id)
			{
				if (!$row->delete($id))
				{
					throw new \Exception($row->getError(), 500);
				}
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_TOOLS_ITEM_DELETED'),
			'message'
		);
	}
}
