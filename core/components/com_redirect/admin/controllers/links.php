<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Components\Redirect\Admin\Controllers;

use Components\Redirect\Helpers\Redirect as Helper;
use Components\Redirect\Models\Link;
use Hubzero\Component\AdminController;
use Exception;
use Request;
use Route;
use User;
use Lang;
use App;

require_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'redirect.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'link.php');

/**
 * Redirect link list controller class.
 */
class Links extends AdminController
{
	/**
	 * Determine task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Value = 0
		$this->registerTask('unpublish', 'publish');

		// Value = 2
		$this->registerTask('archive', 'publish');

		// Value = -2
		$this->registerTask('trash', 'publish');

		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('save2copy', 'save');

		parent::execute();
	}

	/**
	 * Display
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				-1,
				'int'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'created_date'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'desc'
			)
		);

		$entries = Link::all();

		if ($filters['state'] >= 0)
		{
			$entries->whereEquals('state', (int)$filters['state']);
		}

		if ($filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);

			$entries->whereLike('old_url', $filters['search'], 1)
					->orWhereLike('new_url', $filters['search'], 1)
					->orWhereLike('comment', $filters['search'], 1)
					->orWhereLike('referer', $filters['search'], 1)
					->resetDepth();
		}

		// Get records
		$rows = $entries
			->ordered('filter_order', 'filter_order_Dir')
			->paginated();

		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('enabled', Helper::isEnabled())
			->display();
	}

	/**
	 * Method to add a new record.
	 *
	 * @return  void
	 */
	public function addTask()
	{
		// Access check.
		if (!(User::authorise('core.create', $this->_option)))
		{
			// Set the internal error and also the redirect error.
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
				'error'
			);
			return;
		}

		$this->editTask();
	}

	/**
	 * Display edit form
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		// Access check.
		if (!User::authorise('core.edit', $this->_option))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
				'error'
			);
			return;
		}

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the article
			$row = Link::oneOrNew($id);
		}

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Method to save a record.
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries.
		Request::checkToken();

		// Access check.
		if (!User::authorise('core.edit', $this->_option) && !User::authorise('core.create', $this->_option))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'),
				'error'
			);
			return;
		}

		// Initialise variables.
		$fields = Request::getVar('fields', array(), 'post', 'array');

		// The save2copy task needs to be handled slightly differently.
		if ($this->_task == 'save2copy')
		{
			// Reset the ID and then treat the request as for Apply.
			$fields['id'] = 0;
			$this->_task = 'apply';
		}

		$row = Entry::oneOrNew($fields['id'])->set($fields);

		// Attempt to save the data.
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_REDIRECT_SAVE_SUCCESS'));

		if ($this->_task == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect the user
		App::redirect(
			Route::url('index.php?option=' . $this->_option, false)
		);
	}

	/**
	 * Method to update a record.
	 *
	 * @return  void
	 */
	public function activateTask()
	{
		// Check for request forgeries.
		Request::checkToken();

		// Initialise variables.
		$ids     = Request::getVar('id', array(), '', 'array');
		$newUrl  = Request::getString('new_url');
		$comment = Request::getString('comment');

		if (empty($ids))
		{
			Notify::error(Lang::txt('COM_REDIRECT_NO_ITEM_SELECTED'));
		}

		$updated = 0;

		// Loop through all the IDs
		foreach ($ids as $id)
		{
			$entry = Link::oneOrFail(intval($id));
			$entry->set('new_url', $newUrl);
			$entry->set('comment', $comment);
			$entry->set('published', 1);

			// Remove the items.
			if (!$entry->save())
			{
				Notify::error($entry->getError());
				continue;
			}

			$updated++;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option, false),
			($updated ? Lang::txt('COM_REDIRECT_N_LINKS_UPDATED', $updated) : null)
		);
	}

	/**
	 * Method to publish a list of items
	 *
	 * @return  void
	 */
	public function publishTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Get items to publish from the request.
		$ids = Request::getVar('id', array(), '', 'array');
		$data = array(
			'publish'   => 1,
			'unpublish' => 0,
			'archive'   => 2,
			'trash'     => -2,
			'report'    => -3
		);

		$value = \Hubzero\Utility\Arr::getValue($data, $this->_task, 0, 'int');

		if (empty($ids))
		{
			Notify::error(Lang::txt('COM_REDIRECT_NO_ITEM_SELECTED'));
		}

		$updated = 0;

		// Loop through all the IDs
		foreach ($ids as $id)
		{
			$entry = Link::oneOrFail(intval($id));
			$entry->set('published', $value);

			// Remove the items.
			if (!$entry->save())
			{
				Notify::error($entry->getError());
				continue;
			}

			$updated++;
		}

		if ($value == 1)
		{
			$ntext = 'COM_REDIRECT_N_ITEMS_PUBLISHED';
		}
		elseif ($value == 0)
		{
			$ntext = 'COM_REDIRECT_N_ITEMS_UNPUBLISHED';
		}
		elseif ($value == 2)
		{
			$ntext = 'COM_REDIRECT_N_ITEMS_ARCHIVED';
		}
		else
		{
			$ntext = 'COM_REDIRECT_N_ITEMS_TRASHED';
		}
		Notify::success(Lang::txts($ntext, $updated));

		App::redirect(
			Route::url('index.php?option=' . $this->_option, false)
		);
	}
}
