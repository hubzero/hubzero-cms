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
use Components\Publications\Tables;
use Request;
use Config;
use Route;
use Lang;
use App;

/**
 * Manage publication licenses
 */
class Licenses extends AdminController
{
	/**
	 * List resource types
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
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
		$rt = new \Components\Publications\Tables\License($this->database);

		// Get a record count
		$this->view->total = $rt->getCount($this->view->filters);

		// Get records
		$this->view->rows = $rt->getRecords($this->view->filters);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Add a new type
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a type
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming (expecting an array)
			$id = Request::getVar('id', array(0));
			$id = is_array($id) ? $id[0] : $id;

			// Load the object
			$row = new \Components\Publications\Tables\License($this->database);
			$row->loadLicense($id);
		}

		$this->view->row = $row;

		// Set any errors
		if ($this->getError())
		{
			\Notify::error($this->getError());
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save record and fall through to edit view
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask(true);
	}

	/**
	 * Save record
	 *
	 * @param   boolean  $redirect
	 * @return  void
	 */
	public function saveTask($redirect = false)
	{
		// Check for request forgeries
		Request::checkToken();

		$fields = Request::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		$url = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $fields['id'], false);

		// Initiate extended database class
		$row = new \Components\Publications\Tables\License($this->database);
		if (!$row->bind($fields))
		{
			App::redirect($url, $row->getError(), 'error');
			return;
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
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		// Redirect to edit view?
		if ($redirect)
		{
			App::redirect(
				$url,
				Lang::txt('COM_PUBLICATIONS_SUCCESS_LICENSE_SAVED')
			);
		}
		else
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_SUCCESS_LICENSE_SAVED')
			);
		}
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
		$row = new \Components\Publications\Tables\License($this->database);
		$row->loadLicense((int) $id[0]);

		// Update order
		$row->changeOrder($dir);

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
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

		// Incoming
		$id = Request::getVar('id', array(0), '', 'array');

		if (count($id) > 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_LICENSE_SELECT_ONE'),
				'error'
			);
			return;
		}

		// Initialize
		$row = new \Components\Publications\Tables\License($this->database);

		$id = intval($id[0]);

		// Load row
		$row->loadLicense($id);

		// Make default
		$row->main = 1;

		// Save
		if (!$row->store())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$row->getError(),
				'error'
			);
			return;
		}

		// Fix up all other licenses
		$row->undefault($id);

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_PUBLICATIONS_SUCCESS_LICENSE_MADE_DEFAULT')
		);
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

		// Incoming
		$ids = Request::getVar('id', array(0), '', 'array');

		// Initialize
		$row = new \Components\Publications\Tables\License($this->database);

		foreach ($ids as $id)
		{
			$id = intval($id);
			if (!$id)
			{
				continue;
			}

			// Load row
			$row->loadLicense($id);
			$row->active = $row->active == 1 ? 0 : 1;

			// Save
			if (!$row->store())
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					$row->getError(),
					'error'
				);
				return;
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_PUBLICATIONS_SUCCESS_LICENSE_PUBLISHED')
		);
	}
}
