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
use Components\Billboards\Models\Billboard;
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
class BillBoards extends AdminController
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
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Browse the list of billboards
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$rows = Billboard::all()
			->paginated('limitstart')
			->ordered()
			->rows();

		$this->view
			->set('rows', $rows)
			->display();
	}

	/**
	 * Edit a billboard
	 *
	 * @param   object  $billboard
	 * @return  void
	 */
	public function editTask($billboard=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Hide the menu, force users to save or cancel
		Request::setVar('hidemainmenu', 1);

		if (!isset($billboard) || !is_object($billboard))
		{
			// Incoming - expecting an array
			$cid = Request::getVar('cid', array(0));
			if (!is_array($cid))
			{
				$cid = array($cid);
			}
			$uid = $cid[0];

			$billboard = Billboard::oneOrNew($uid);
		}

		// Fail if not checked out by current user
		if ($billboard->isCheckedOut())
		{
			Notify::warning(Lang::txt('COM_BILLBOARDS_ERROR_CHECKED_OUT'));
			return $this->cancelTask();
		}

		// Are we editing an existing entry?
		/*if ($billboard->id)
		{
			// Yes, we should check it out first
			$billboard->checkout(User::get('id'));
		}*/

		// Output the HTML
		$this->view
			->set('row', $billboard)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a billboard
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

		// Incoming, make sure to allow HTML to pass through
		$data = Request::getVar('billboard', array(), 'post', 'array', JREQUEST_ALLOWHTML);

		// Create object
		$billboard = Billboard::oneOrNew($data['id'])->set($data);

		// Check to make sure collection exists
		$collection = Collection::oneOrNew($billboard->collection_id);
		if ($collection->isNew())
		{
			$collection->set('name', 'Default Collection')->save();
			$billboard->set('collection_id', $collection->id);
		}

		if (!$billboard->save())
		{
			// Something went wrong...return errors
			foreach ($billboard->getErrors() as $error)
			{
				Notify::error($error);
			}

			return $this->editTask($billboard);
		}

		// See if we have an image coming in as well
		$billboard_image = Request::getVar('billboard-image', false, 'files', 'array');

		// If so, proceed with saving the image
		if (isset($billboard_image['name']) && $billboard_image['name'])
		{
			// Build the upload path if it doesn't exist
			$image_location  = $this->config->get('image_location', 'app' . DS . 'site' . DS . 'media' . DS . 'images' . DS . 'billboards');
			$uploadDirectory = PATH_ROOT . DS . trim($image_location, DS) . DS;

			// Make sure upload directory exists and is writable
			if (!is_dir($uploadDirectory))
			{
				if (!\Filesystem::makeDirectory($uploadDirectory))
				{
					Notify::error(Lang::txt('COM_BILLBOARDS_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
					return $this->editTask($billboard);
				}
			}

			// Scan for viruses
			if (!\Filesystem::isSafe($billboard_image['tmp_name']))
			{
				Notify::error(Lang::txt('COM_BILLBOARDS_ERROR_FAILED_VIRUS_SCAN'));
				return $this->editTask($billboard);
			}

			if (!move_uploaded_file($billboard_image['tmp_name'], $uploadDirectory . $billboard_image['name']))
			{
				Notify::error(Lang::txt('COM_BILLBOARDS_ERROR_FILE_MOVE_FAILED'));
				return $this->editTask($billboard);
			}
			else
			{
				if ($old = $billboard->get('background_img'))
				{
					if (file_exists($uploadDirectory . $old))
					{
						\Filesystem::delete($uploadDirectory . $old);
					}
				}
				// Move successful, save the image url to the billboard entry
				$billboard->set('background_img', $billboard_image['name']);
				if (!$billboard->save())
				{
					Notify::error($billboard->getError());
					return $this->editTask($billboard);
				}
			}
		}

		// Check in the billboard now that we've saved it
		$billboard->checkin();

		// Redirect
		Notify::success(Lang::txt('COM_BILLBOARDS_BILLBOARD_SUCCESSFULLY_SAVED'));

		$this->cancelTask();
	}

	/**
	 * Save the new order
	 *
	 * @return void
	 */
	public function saveorderTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Initialize variables
		$cid   = Request::getVar('cid', array(), 'post', 'array');
		$order = Request::getVar('order', array(), 'post', 'array');

		// Make sure we have something to work with
		if (empty($cid))
		{
			App::abort(500, Lang::txt('BILLBOARDS_ORDER_PLEASE_SELECT_ITEMS'));
		}

		// Update ordering values
		for ($i = 0; $i < count($cid); $i++)
		{
			$billboard = Billboard::oneOrFail($cid[$i]);

			if ($billboard->ordering != $order[$i])
			{
				$billboard->set('ordering', $order[$i]);

				if (!$billboard->save())
				{
					App::abort(500, $billboard->getError());
				}
			}
		}

		// Clear the component's cache
		Cache::clean('com_billboards');

		// Redirect
		Notify::success(Lang::txt('COM_BILLBOARDS_ORDER_SUCCESSFULLY_UPDATED'));

		$this->cancelTask();
	}

	/**
	 * Delete a billboard
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

		// Incoming (expecting an array)
		$ids = Request::getVar('cid', array());
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		$i = 0;

		// Make sure we have IDs to work with
		if (count($ids) > 0)
		{
			// Loop through the array of ID's and delete
			foreach ($ids as $id)
			{
				$billboard = Billboard::oneOrFail($id);

				// Delete record
				if (!$billboard->destroy())
				{
					Notify::error(Lang::txt('COM_BILLBOARDS_ERROR_CANT_DELETE'));
					continue;
				}

				$i++;
			}
		}

		// Redirect
		if ($i)
		{
			Notify::success(Lang::txt('COM_BILLBOARDS_BILLBOARD_SUCCESSFULLY_DELETED', $i));
		}

		$this->cancelTask();
	}

	/**
	 * Cancels out of the billboard edit view, makes sure to
	 * check the billboard back in for other people to edit
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		// Incoming - we need an id so that we can check it back in
		/*$fields = Request::getVar('billboard', array(), 'post');

		// Check the billboard back in
		if (isset($fields['id']) && $fields['id'])
		{
			$billboard = Billboard::oneOrNew($fields['id']);
			$billboard->checkin();
		}*/

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Toggle a billboard between published and unpublished.
	 * We're looking for an array of ID's to publish/unpublish
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming (we're expecting an array)
		$ids = Request::getVar('cid', array());
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		$publish = $this->getTask() == 'publish' ? 1 : 0;

		// Loop through the IDs
		foreach ($ids as $id)
		{
			// Load the billboard
			$row = Billboard::oneOrFail($id);

			// Only alter items not checked out or checked out by 'me'
			if (!$row->isCheckedOut())
			{
				$row->set('published', $publish);

				if (!$row->save())
				{
					App::abort(500, $row->getError());
				}

				// Check it back in
				$row->checkin();
			}
			else
			{
				Notify::warning(Lang::txt('COM_BILLBOARDS_ERROR_CHECKED_OUT'));
			}
		}

		// Redirect
		$this->cancelTask();
	}
}
