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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
				'*'
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

		if ($filters['state'] != '*')
		{
			$entries->whereEquals('published', (int)$filters['state']);
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
			Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
			return $this->cancelTask();
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
			Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			return $this->cancelTask();
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

		$this->view
			->set('row', $row)
			->setLayout('edit')
			->setError($this->getErrors())
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
			Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			return $this->cancelTask();
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
		$this->cancelTask();
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

		// Access check.
		if (!User::authorise('core.edit', $this->_option))
		{
			Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			return $this->cancelTask();
		}

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

		if ($updated)
		{
			Notify::success(Lang::txt('COM_REDIRECT_N_LINKS_UPDATED', $updated));
		}

		$this->cancelTask();
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

		// Access check.
		if (!User::authorise('core.edit', $this->_option))
		{
			Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			return $this->cancelTask();
		}

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

		$this->cancelTask();
	}

	/**
	 * Remove one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Access check.
		if (!User::authorise('core.delete', $this->_option))
		{
			Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
			return $this->cancelTask();
		}

		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$ids = Request::getVar('id', array(), '', 'array');

		if (empty($ids))
		{
			Notify::error(Lang::txt('COM_REDIRECT_NO_ITEM_SELECTED'));
		}

		$i = 0;
		foreach ($ids as $id)
		{
			$entry = Link::oneOrFail(intval($id));

			if (!$entry->destroy())
			{
				Notify::error($entry->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			Notify::success(Lang::txts('COM_REDIRECT_N_ITEMS_DELETED', $i));
		}

		$this->cancelTask();
	}
}
