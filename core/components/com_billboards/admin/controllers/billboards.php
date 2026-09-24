<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
use Cache;

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
			$cid = Request::getArray('cid', array(0));
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
		$data = Request::getArray('billboard', array(), 'post');
		$data['id'] = isset($data['id']) ? (int) $data['id'] : 0;

		// The form never posts these. background_img is set from the upload
		// below and the stored name is what the old file is removed by, so it
		// must not be client-chosen; published belongs to stateTask
		// (core.edit.state); the checkout columns are bookkeeping.
		unset($data['background_img'], $data['checked_out'], $data['checked_out_time']);
		if (!User::authorise('core.edit.state', $this->_option))
		{
			unset($data['published']);
		}

		// Create object
		$billboard = Billboard::oneOrNew($data['id']);
		$previous_img = $billboard->get('background_img');
		$billboard->set($data);

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
		$billboard_image = Request::getArray('billboard-image', false, 'files');

		// If so, proceed with saving the image
		if (isset($billboard_image['name']) && $billboard_image['name'])
		{
			// Never trust the client-supplied filename: strip any path and require a
			// real image extension so it cannot traverse the upload dir or drop a
			// .php/.phtml file. Note svg stays in the list because billboards use
			// it as a vector image, and an SVG can carry script that runs in this
			// origin when viewed directly -- acceptable only because uploading one
			// already requires an administrator.
			$billboard_image['name'] = basename(str_replace('\\', '/', (string) $billboard_image['name']));
			$imgExt = strtolower(pathinfo($billboard_image['name'], PATHINFO_EXTENSION));
			if (!in_array($imgExt, array('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'), true))
			{
				Notify::error(Lang::txt('COM_BILLBOARDS_ERROR_FILE_MOVE_FAILED'));
				return $this->editTask($billboard);
			}

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
				// Remove the previous image, unless the replacement was uploaded
				// under the same name and is that file.
				if ($old = basename((string) $previous_img))
				{
					if ($old !== $billboard_image['name'] && file_exists($uploadDirectory . $old))
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
		$cid   = Request::getArray('cid', array(), 'post');
		$order = Request::getArray('order', array(), 'post');

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
		$ids = Request::getArray('cid', array());
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
		/*$fields = Request::getArray('billboard', array(), 'post');

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
		$ids = Request::getArray('cid', array());
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
