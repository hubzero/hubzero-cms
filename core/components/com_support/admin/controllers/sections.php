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
use Components\Support\Tables;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use App;

/**
 * Support controller class for managing ticket sections
 */
class Sections extends AdminController
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
				$this->_option . '.sections.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.sections.limitstart',
				'limitstart',
				0,
				'int'
			)
		);

		$model = new Tables\Section($this->database);

		// Record count
		$total = $model->getCount($filters);

		// Fetch results
		$rows  = $model->getRecords($filters);

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
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($edit))
		{
			// Incoming
			$id = Request::getInt('id', 0);

			// Initiate database class and load info
			$row = new SupportSection($this->database);
			$row->load($id);

			// Set action
			if (!$row->id)
			{
				$row->section = '';
			}
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			Notify::error($error);
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
		$sec = Request::getVar('sec', array(), 'post');
		$sec = array_map('trim', $sec);

		// Initiate class and bind posted items to database fields
		$row = new Tables\Section($this->database);
		if (!$row->bind($sec))
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Code cleaner for xhtml transitional compliance
		$row->section = trim($row->section);

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('SECTION_SUCCESSFULLY_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Output messsage and redirect
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
		$ids = Request::getVar('id', array(0));
		if (!is_array($ids))
		{
			$ids = array(0);
		}

		// Check for an ID
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('SUPPORT_ERROR_SELECT_SECTION_TO_DELETE'));
			return $this->cancelTask();
		}

		$i = 0;
		foreach ($ids as $id)
		{
			// Delete entry
			$cat = new Tables\Section($this->database);

			if (!$cat->delete(intval($id)))
			{
				Notify::error($cat->getError());
				continue;
			}

			$i++;
		}

		// Output messsage and redirect
		if ($i)
		{
			Notify::success(Lang::txt('SECTION_SUCCESSFULLY_DELETED', $i));
		}

		$this->cancelTask();
	}
}
