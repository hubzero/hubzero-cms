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

namespace Components\Blog\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Blog\Models\Entry;
use Request;
use Config;
use Notify;
use Route;
use User;
use Lang;
use Date;
use App;

/**
 * Blog controller class for entries
 */
class Entries extends AdminController
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
	 * Display a list of blog entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'scope' => Request::getState(
				$this->_option . '.' . $this->_controller . '.scope',
				'scope',
				''
			),
			'scope_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.scope_id',
				'scope_id',
				0,
				'int'
			),
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
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'access',
				0,
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

		$entries = Entry::all();

		if ($filters['search'])
		{
			$entries->whereLike('title', strtolower((string)$filters['search']));
		}

		if ($filters['scope'])
		{
			$entries->whereEquals('scope', $filters['scope']);
		}

		if ($filters['scope_id'])
		{
			$entries->whereEquals('scope_id', (int)$filters['scope_id']);
		}

		if ($filters['state'] >= 0)
		{
			$entries->whereEquals('state', (int)$filters['state']);
		}

		if ($filters['access'])
		{
			$entries->whereEquals('access', (int)$filters['access']);
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
	 * Show a form for editing an entry
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
			// Incoming
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the article
			$row = Entry::oneOrNew($id);
		}

		if ($row->isNew())
		{
			$row->set('created_by', User::get('id'));
			$row->set('created', Date::toSql());
			$row->set('publish_up', Date::toSql());
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
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

		if (isset($fields['publish_up']) && $fields['publish_up'] != '')
		{
			$fields['publish_up']   = Date::of($fields['publish_up'], Config::get('offset'))->toSql();
		}
		if (isset($fields['publish_down']) && $fields['publish_down'] != '')
		{
			$fields['publish_down'] = Date::of($fields['publish_down'], Config::get('offset'))->toSql();
		}

		// Initiate extended database class
		$row = Entry::oneOrNew($fields['id'])->set($fields);

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Process tags
		$row->tag(trim(Request::getVar('tags', '')));

		Notify::success(Lang::txt('COM_BLOG_ENTRY_SAVED'));

		if ($this->_task == 'apply')
		{
			return $this->editTask($row);
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

		if (count($ids) > 0)
		{
			$removed = 0;

			// Loop through all the IDs
			foreach ($ids as $id)
			{
				$entry = Entry::oneOrFail(intval($id));

				// Delete the entry
				if (!$entry->destroy())
				{
					Notify::error($entry->getError());
					continue;
				}

				$removed++;
			}
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_BLOG_ENTRIES_DELETED'));
		}

		// Set the redirect
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

		$state = $this->_task == 'publish' ? 1 : 0;

		// Incoming
		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for a resource
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_BLOG_SELECT_ENTRY_TO', $this->_task));
			return $this->cancelTask();
		}

		// Loop through all the IDs
		$success = 0;
		foreach ($ids as $id)
		{
			// Load the article
			$row = Entry::oneOrNew(intval($id));
			$row->set('state', $state);

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
			switch ($this->_task)
			{
				case 'publish':
					$message = Lang::txt('COM_BLOG_ITEMS_PUBLISHED', $success);
				break;
				case 'unpublish':
					$message = Lang::txt('COM_BLOG_ITEMS_UNPUBLISHED', $success);
				break;
				case 'archive':
					$message = Lang::txt('COM_BLOG_ITEMS_ARCHIVED', $success);
				break;
			}

			Notify::success($message);
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Turn comments on/off
	 *
	 * @return  void
	 */
	public function setcommentsTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);
		$state = Request::getInt('state', 0);

		// Check for a resource
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_BLOG_SELECT_ENTRY_TO_COMMENTS', $this->_task));
			return $this->cancelTask();
		}

		// Loop through all the IDs
		$success = 0;
		foreach ($ids as $id)
		{
			// Load the article
			$row = Entry::oneOrFail(intval($id));
			$row->set('allow_comments', $state);

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
			$message = $state
				? Lang::txt('COM_BLOG_ITEMS_COMMENTS_ENABLED', $success)
				: Lang::txt('COM_BLOG_ITEMS_COMMENTS_DISABLED', $success);

			Notify::success($message);
		}

		// Set the redirect
		$this->cancelTask();
	}
}
