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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
			$id = Request::getVar('id', array(0));
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
			'name' => Request::getVar('name')
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
		$ids = Request::getVar('id', array());
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