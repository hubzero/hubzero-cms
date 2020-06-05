<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Admin\Controllers;

use Components\Jobs\Tables\JobCategory;
use Hubzero\Component\AdminController;
use Hubzero\Utility\Arr;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use App;

/**
 * Controller class for job categories
 */
class Categories extends AdminController
{
	/**
	 * List categories
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
				'ordernum'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// Instantiate an object
		$jc = new JobCategory($this->database);

		// Get records
		$this->view->rows = $jc->getCats($this->view->filters['sort'], $this->view->filters['sort_Dir'], 1);
		$this->view->total = count($this->view->rows);

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save the ordering of entries
	 *
	 * @return     void
	 */
	public function saveorderTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$order = Request::getArray('order', array(), 'post');
		Arr::toInteger($order);

		// Instantiate an object
		$jc = new JobCategory($this->database);

		if (count($order) > 0)
		{
			foreach ($order as $id => $num)
			{
				$jc->updateOrder($id, $num);
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_JOBS_ORDER_SAVED')
		);
	}

	/**
	 * Create a new category
	 * Displays the edit form
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a category
	 *
	 * @return     void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming (expecting an array)
			$id = Request::getArray('id', array(0));
			$id = (is_array($id)) ? $id[0] : $id;

			// Load the object
			$row = new JobCategory($this->database);
			$row->load($id);
		}

		$this->view->row = $row;

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a category
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Initiate extended database class
		$row = new JobCategory($this->database);
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
	 * Remove one or more categories
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming (expecting an array)
		$ids = Request::getArray('id', array());
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

		$jc = new JobCategory($this->database);

		foreach ($ids as $id)
		{
			// Delete the type
			$jc->delete($id);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_JOBS_ITEMS_REMOVED', count($ids))
		);
	}
}
