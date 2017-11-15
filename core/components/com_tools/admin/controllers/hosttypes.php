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
use Components\Tools\Tables\Hosttype;
use Hubzero\Component\AdminController;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'hosttype.php';

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
		$filters = array(
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
		$filters['limit'] = ($filters['limit'] == 'all') ? 0 : $filters['limit'];
		$filters['start'] = ($filters['limit'] != 0 ? (floor($filters['start'] / $filters['limit']) * $filters['limit']) : 0);

		// Get the middleware database
		$mwdb = Utils::getMWDBO();

		$model = new Hosttype($mwdb);

		$total = $model->getCount($filters);

		$rows = $model->getRecords($filters);

		if ($rows)
		{
			foreach ($rows as $key => $row)
			{
				$rows[$key]->refs = $this->_refs($row->value);
			}
		}

		// Display results
		$this->view
			->set('filters', $filters)
			->set('total', $total)
			->set('rows', $rows)
			->display();
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

			$row = new Hosttype($mwdb);
			$row->load($item);
		}

		$bit = '';
		if ($row->value > 0)
		{
			$bit = log($row->value)/log(2);
		}

		$refs = $this->_refs($row->value);

		// Display results
		$this->view
			->set('row', $row)
			->set('bit', $bit)
			->set('refs', $refs)
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

		$row = new Hosttype($mwdb);

		if (!$row->bind($fields))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
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

		$this->cancelTask();
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

		$removed = 0;

		if (count($ids) > 0)
		{
			$mwdb = Utils::getMWDBO();

			$row = new Hosttype($mwdb);

			// Loop through each ID
			foreach ($ids as $id)
			{
				if (!$row->delete($id))
				{
					Notify::error($row->getError());
					continue;
				}

				$removed++;
			}
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_TOOLS_ITEM_DELETED'));
		}

		$this->cancelTask();
	}
}
