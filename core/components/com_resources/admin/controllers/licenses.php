<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Models\License;
use Hubzero\Component\AdminController;
use Request;
use Notify;
use Route;
use Lang;
use User;
use App;

/**
 * Manage resource types
 */
class Licenses extends AdminController
{
	/**
	 * Executes a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');

		parent::execute();
	}

	/**
	 * List entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'ordering'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// Instantiate an object
		$entries = License::all();

		if ($filters['search'])
		{
			$entries->whereLike('title', strtolower((string)$filters['search']));
		}

		// Get records
		$rows = $entries
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Edit a record
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming (expecting an array)
			$id = Request::getArray('id', array(0));
			if (is_array($id))
			{
				$id = (!empty($id) ? $id[0] : 0);
			}

			// Load the object
			$row = License::oneOrNew($id);
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
	 * Save a record
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$fields = Request::getArray('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = License::oneOrNew($fields['id'])->set($fields);

		// Store new content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_RESOURCES_ITEM_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Remove one or more entries
	 *
	 * @return  void  Redirects back to main listing
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming (expecting an array)
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Ensure we have an ID to work with
		if (empty($ids))
		{
			// Redirect with error message
			Notify::warning(Lang::txt('COM_RESOURCES_NO_ITEM_SELECTED'));
			return $this->cancelTask();
		}

		$i = 0;

		foreach ($ids as $id)
		{
			$row = License::oneOrFail($id);

			if (!$row->destroy())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_RESOURCES_ITEMS_REMOVED', $i));
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Reorders a resource child
	 * Redirects to parent resource's children listing
	 *
	 * @return  void
	 */
	public function reorderTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$dir = $this->getTask() == 'orderup' ? -1 : 1;

		// Incoming
		$id = Request::getArray('id', array(0), '');

		// Load row
		$row = License::oneOrFail((int) $id[0]);

		// Update order
		if (!$row->move($dir))
		{
			Notify::error($row->getError());
		}

		$this->cancelTask();
	}
}
