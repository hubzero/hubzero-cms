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

namespace Components\Store\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Utility\Sanitize;
use Components\Store\Models\Store;
use Component;
use Request;
use Notify;
use Route;
use Event;
use Lang;
use App;

/**
 * Controller class for store items
 */
class Items extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->banking = Component::params('com_members')->get('bankAccounts');

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');
		$this->registerTask('available', 'availability');
		$this->registerTask('unavailable', 'availability');

		parent::execute();
	}

	/**
	 * Displays a list of entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get paging variables
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'available' => Request::getState(
				$this->_option . '.' . $this->_controller . '.available',
				'limitstart',
				-1,
				'int'
			),
			'published' => Request::getState(
				$this->_option . '.' . $this->_controller . '.published',
				'limitstart',
				-1,
				'int'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$query = Store::all();

		if ($filters['search'])
		{
			$query->whereLike('title', strtolower((string)$filters['search']));
		}

		if ($filters['available'] >= 0)
		{
			$query->whereEquals('available', (int)$filters['available']);
		}

		if ($filters['published'] >= 0)
		{
			$query->whereEquals('published', (int)$filters['published']);
		}

		// Get records
		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Edit a store item
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row = null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the article
			$row = Store::oneOrNew($id);
		}

		if ($row->isNew())
		{
			$row->set('category', 'wear');
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Saves changes to a store item
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

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);

		// Initiate extended database class
		$row = Store::oneOrNew($fields['id'])->set($fields);

		// code cleaner
		$row->set('description', Sanitize::clean($row->get('description')));

		$sizes = Request::getVar('sizes', '', 'post', 'none', 2);
		$sizes = str_replace(' ', '', $sizes);
		$sizes = explode(',', $sizes);
		$sizes_cl = '';
		foreach ($sizes as $s)
		{
			if (trim($s) != '')
			{
				$sizes_cl .= $s;
				$sizes_cl .= ($s == end($sizes)) ? '' : ', ';
			}
		}

		$row->params->set('size', $sizes_cl);

		$row->set('params', $row->params->toString());
		$row->set('published', isset($fields['published']) ? 1 : 0);
		$row->set('available', isset($fields['available']) ? 1 : 0);
		$row->set('featured', isset($fields['featured'])  ? 1 : 0);
		$row->set('type', ($fields['category'] == 'service' ? 2 : 1));

		// Store content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Notify of success
		Notify::success(Lang::txt('COM_STORE_MSG_SAVED'));

		// Redirect to main listing or go back to edit form
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Sets the state of one or more entries
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

		$state = $this->getTask() == 'publish' ? 1 : 0;

		// Incoming
		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for a resource
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_STORE_ALERT_SELECT_ITEM', $this->getTask()));
			return $this->cancelTask();
		}

		// Loop through all the IDs
		$success = 0;
		foreach ($ids as $id)
		{
			// Load the article
			$row = Store::oneOrNew(intval($id));
			$row->set('published', $state);

			// Store new content
			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			switch ($this->getTask())
			{
				case 'publish':
					$message = Lang::txt('COM_STORE_MSG_ITEM_PUBLISHED', $success);
				break;
				case 'unpublish':
					$message = Lang::txt('COM_STORE_MSG_ITEM_UNPUBLISHED', $success);
				break;
			}

			Notify::success($message);
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Change availability of item(s)
	 *
	 * @return  void
	 */
	public function availabilityTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$state = $this->getTask() == 'available' ? 1 : 0;

		// Incoming
		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for a resource
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_STORE_ALERT_SELECT_ITEM', $this->getTask()));
			return $this->cancelTask();
		}

		// Loop through all the IDs
		$success = 0;
		foreach ($ids as $id)
		{
			// Load the article
			$row = Store::oneOrNew(intval($id));
			$row->set('available', $state);

			// Store new content
			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			switch ($this->getTask())
			{
				case 'available':
					$message = Lang::txt('COM_STORE_MSG_ITEM_AVAIL', $success);
				break;
				case 'unavailable':
					$message = Lang::txt('COM_STORE_MSG_ITEM_UNAVAIL', $success);
				break;
			}

			Notify::success($message);
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Delete one or more entries
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$removed = 0;

		foreach ($ids as $id)
		{
			$row = Store::oneOrFail(intval($id));

			// Delete the entry
			if (!$row->destroy())
			{
				Notify::error($row->getError());
				continue;
			}

			// Trigger before delete event
			Event::trigger('onStoreAfterDelete', array($id));

			$removed++;
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_STORE_ITEMSS_DELETED'));
		}

		// Set the redirect
		$this->cancelTask();
	}
}
