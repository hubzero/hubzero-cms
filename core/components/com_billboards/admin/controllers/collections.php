<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\BillBoards\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Billboards\Models\Collection;
use Request;
use Notify;
use Route;
use Lang;
use User;
use App;

/**
 * Primary controller for the Billboards component
 */
class Collections extends AdminController
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
	 * Browse billboards collections (collections are used to display multiple billboards via mod_billboards)
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$rows = Collection::all()
			->paginated()
			->ordered();

		$this->view
			->set('rows', $rows)
			->display();
	}

	/**
	 * Edit a collection
	 *
	 * @param  object $collection
	 * @return void
	 */
	public function editTask($collection=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Hide the menu, force users to save or cancel
		Request::setVar('hidemainmenu', 1);

		if (!isset($collection) || !is_object($collection))
		{
			// Incoming (expecting an array)
			$id = Request::getArray('id', array(0));
			if (!is_array($id))
			{
				$id = array($id);
			}
			$cid = $id[0];

			$collection = Collection::oneOrNew($cid);
		}

		// Display
		$this->view
			->set('row', $collection)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a collection
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

		// Create object
		$collection = Collection::oneOrNew(Request::getInt('id'))->set(array(
			'name' => Request::getString('name')
		));

		if (!$collection->save())
		{
			// Something went wrong...return errors
			foreach ($collection->getErrors() as $error)
			{
				Notify::error($error);
			}

			return $this->editTask($collection);
		}

		// Output messsage and redirect
		Notify::success(Lang::txt('COM_BILLBOARDS_COLLECTION_SUCCESSFULLY_SAVED'));

		$this->cancelTask();
	}

	/**
	 * Delete a billboard collection
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
		$ids = Request::getArray('id', array());
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Loop through the selected collections to delete
		// @TODO: maybe we should warn people if trying to delete a collection with associated billboards?
		$i = 0;
		foreach ($ids as $id)
		{
			$collection = Collection::oneOrFail($id);

			// Delete record
			if (!$collection->destroy())
			{
				Notify::error($collection->getError());
				continue;
			}

			$i++;
		}

		// Output messsage and redirect
		if ($i)
		{
			Notify::success(Lang::txt('COM_BILLBOARDS_COLLECTION_SUCCESSFULLY_DELETED', $i));
		}

		$this->cancelTask();
	}
}
