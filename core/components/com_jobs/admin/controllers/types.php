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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Jobs\Admin\Controllers;

use Components\Jobs\Tables\JobType;
use Hubzero\Component\AdminController;
use Exception;
use Request;
use Notify;
use Config;
use Route;
use Lang;
use App;

/**
 * Controller class for job types
 */
class Types extends AdminController
{
	/**
	 * Display a list of types
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->filters = array(
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
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// Instantiate an object
		$jt = new JobType($this->database);

		// Get records
		$this->view->rows = $jt->getTypes($this->view->filters['sort'], $this->view->filters['sort_Dir']);
		$this->view->total = count($this->view->rows);

		// Load default types if none found
		if (!$this->view->total)
		{
			$default = array(
				array(
					'id' => 0,
					'category' => ucfirst(Lang::txt('COM_JOBS_CATEGORY_FULLTIME'))
				),
				array(
					'id' => 0,
					'category' => ucfirst(Lang::txt('COM_JOBS_CATEGORY_PARTTIME'))
				),
				array(
					'id' => 0,
					'category' => ucfirst(Lang::txt('COM_JOBS_CATEGORY_CONTRACT'))
				),
				array(
					'id' => 0,
					'category' => ucfirst(Lang::txt('COM_JOBS_CATEGORY_INTERNSHIP'))
				),
				array(
					'id' => 0,
					'category' => ucfirst(Lang::txt('COM_JOBS_CATEGORY_TEMPORARY'))
				)
			);

			foreach ($default as $d)
			{
				if (!$jt->bind($d))
				{
					throw new Exception($jt->getError());
				}
				if (!$jt->store())
				{
					throw new Exception($jt->getError());
				}
			}

			// Get new records
			$this->view->rows = $jt->getTypes();
			$this->view->total = count($this->view->rows);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new type
	 * Displays the edit form
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
	 * @return     void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming (expecting an array)
			$id = Request::getVar('id', array(0));
			$id = (is_array($id)) ? $id[0] : $id;

			// Load the object
			$row = new JobType($this->database);
			$row->load($id);
		}

		$this->view->row = $row;

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save type
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Initiate extended database class
		$row = new JobType($this->database);
		if (!$row->bind($_POST))
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

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_JOBS_ITEM_SAVED')
		);
	}

	/**
	 * Remove type
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming (expecting an array)
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Ensure we have an ID to work with
		if (empty($ids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_JOBS_ERROR_NO_ITEM_SELECTED'),
				'error'
			);
			return;
		}

		$jt = new JobType($this->database);

		foreach ($ids as $id)
		{
			// Delete the type
			$jt->delete(intval($id));
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_JOBS_ITEMS_REMOVED', count($ids))
		);
	}
}

