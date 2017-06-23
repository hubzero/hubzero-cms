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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Publications\Tables\License;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use App;

/**
 * Manage publication licenses
 */
class Licenses extends AdminController
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
		$this->registerTask('save2new', 'save');

		parent::execute();
	}

	/**
	 * List resource types
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'limit' => Request::getState(
				$this->_option . '.licenses.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.licenses.limitstart',
				'limitstart',
				0,
				'int'
			),
			'search' => Request::getState(
				$this->_option . '.licenses.search',
				'search',
				''
			),
			'sort' => Request::getState(
				$this->_option . '.licenses.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.licenses.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// Instantiate an object
		$rt = new License($this->database);

		// Get a record count
		$total = $rt->getCount($filters);

		// Get records
		$rows = $rt->getRecords($filters);

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('total', $total)
			->set('rows', $rows)
			->display();
	}

	/**
	 * Edit a type
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.create', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming (expecting an array)
			$id = Request::getVar('id', array(0));
			$id = is_array($id) ? $id[0] : $id;

			// Load the object
			$row = new License($this->database);
			$row->load($id);
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save record
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.create', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$fields = Request::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		$url = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $fields['id'], false);

		// Initiate extended database class
		$row = new License($this->database);

		if (!$row->bind($fields))
		{
			Notify::error($row->getError());
			App::redirect($url);
		}

		$row->customizable = Request::getInt('customizable', 0, 'post');
		$row->agreement    = Request::getInt('agreement', 0, 'post');
		$row->active       = Request::getInt('active', 0, 'post');
		$row->icon         = $row->icon ? $row->icon : '/core/components/com_publications/site/assets/img/logos/license.gif';

		if (!$row->id)
		{
			$row->ordering = $row->getNextOrder();
		}

		// Check content
		if (!$row->check())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Store new content
		if (!$row->store())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_PUBLICATIONS_SUCCESS_LICENSE_SAVED'));

		// Redirect to edit view?
		if ($this->getTask() == 'apply')
		{
			App::redirect(
				$url
			);
		}

		$this->cancelTask();
	}

	/**
	 * Reorder up
	 *
	 * @return  void
	 */
	public function orderupTask()
	{
		$this->reorderTask(-1);
	}

	/**
	 * Reorder down
	 *
	 * @return  void
	 */
	public function orderdownTask()
	{
		$this->reorderTask(1);
	}

	/**
	 * Reorders licenses
	 * Redirects to license listing
	 *
	 * @param   integer  $dir
	 * @return  void
	 */
	public function reorderTask($dir = 0)
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getVar('id', array(0), '', 'array');

		// Load row
		$row = new License($this->database);
		$row->load((int) $id[0]);

		// Update order
		$row->changeOrder($dir);

		$this->cancelTask();
	}

	/**
	 * Makes one license default
	 * Redirects to license listing
	 *
	 * @return  void
	 */
	public function makedefaultTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$id = Request::getVar('id', array(0), '', 'array');

		if (count($id) > 1)
		{
			Notify::error(Lang::txt('COM_PUBLICATIONS_LICENSE_SELECT_ONE'));
			return $this->cancelTask();
		}

		$id = intval($id[0]);

		// Initialize
		$row = new License($this->database);

		// Load row
		$row->load($id);

		// Make default
		$row->main = 1;

		// Save
		if (!$row->store())
		{
			Notify::error($row->getError());
			return $this->cancelTask();
		}

		// Fix up all other licenses
		$row->undefault($id);

		Notify::success(Lang::txt('COM_PUBLICATIONS_SUCCESS_LICENSE_MADE_DEFAULT'));

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Change license status
	 * Redirects to license listing
	 *
	 * @return  void
	 */
	public function changestatusTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('id', array(0), '', 'array');

		// Initialize
		$row = new License($this->database);

		$success = 0;
		foreach ($ids as $id)
		{
			$id = intval($id);
			if (!$id)
			{
				continue;
			}

			// Load row
			$row->load($id);
			$row->active = $row->active == 1 ? 0 : 1;

			// Save
			if (!$row->store())
			{
				Notify::error($row->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			Notify::success(Lang::txt('COM_PUBLICATIONS_SUCCESS_LICENSE_PUBLISHED'));
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Removes one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		$success = 0;

		// Loop through each ID and delete the necessary items
		foreach ($ids as $id)
		{
			// Remove the profile
			$row = new License($this->database);
			$row->load($id);

			if (!$row->delete())
			{
				Notify::error($row->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			Notify::success(Lang::txt('COM_PUBLICATIONS_SUCCESS_LICENSE_REMOVED'));
		}

		// Output messsage and redirect
		$this->cancelTask();
	}
}
