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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Admin\Controllers;

use Components\Events\Models\Orm\Page;
use Hubzero\Component\AdminController;
use Request;
use Notify;
use Route;
use Event;
use User;
use Lang;
use Date;
use App;

require_once dirname(dirname(__DIR__)) . '/models/orm/event.php';

/**
 * Events controller for pages
 */
class Pages extends AdminController
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
	 * Display a list of pages for an event
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$filters = array(
			'event_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.event_id',
				'event_id',
				0,
				'int'
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
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

		$event = \Components\Events\Models\Orm\Event::oneOrFail($filters['event_id']);

		// Get records
		$query = $event->pages();

		if ($filters['search'])
		{
			$query->whereLike('title', strtolower((string)$filters['search']));
		}

		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('event', $event)
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
			$id = Request::getArray('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the article
			$page = Page::oneOrNew($id);
		}

		if ($page->isNew())
		{
			$page->set('event_id', Request::getInt('event_id'));
		}

		$event = $page->event;

		// Output the HTML
		$this->view
			->set('page', $page)
			->set('event', $event)
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

		$fields = Request::getArray('fields', array(), 'post');
		$fields['event_id'] = Request::getInt('event_id');

		// Bind incoming data to object
		$row = Page::oneOrNew($fields['id'])->set($fields);

		// Trigger before save event
		$isNew  = $row->isNew();
		$result = Event::trigger('onEventsBeforeSavePage', array(&$row, $isNew));

		if (in_array(false, $result, true))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Trigger after save event
		Event::trigger('onEventsAfterSavePage', array(&$row, $isNew));

		Notify::success(Lang::txt('COM_EVENTS_PAGE_SAVED'));

		// Redirect
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Remove one or more entries for an event
	 *
	 * @return     void
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
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$removed = 0;

		foreach ($ids as $id)
		{
			$entry = Page::oneOrFail(intval($id));

			// Delete the entry
			if (!$entry->destroy())
			{
				Notify::error($entry->getError());
				continue;
			}

			// Trigger before delete event
			Event::trigger('onEventsAfterDeletePage', array($id));

			$removed++;
		}

		if ($removed)
		{
			Notify::success(Lang::txt('EVENTS_PAGES_REMOVED'));
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Move an item up one in the ordering
	 *
	 * @return  void
	 */
	public function orderupTask()
	{
		$this->reorderTask('up');
	}

	/**
	 * Move an item down one in the ordering
	 *
	 * @return  void
	 */
	public function orderdownTask()
	{
		$this->reorderTask('down');
	}

	/**
	 * Move an item one down or own up int he ordering
	 *
	 * @param   string  $move  Direction to move
	 * @return  void
	 */
	protected function reorderTask($move='down')
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$id = Request::getArray('id', array(0));
		$id = $id[0];
		$pid = Request::getInt('event', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			Notify::warning(Lang::txt('COM_EVENTS_PAGE_NO_ID'));
			return $this->cancelTask();
		}

		// Ensure we have a parent ID to work with
		if (!$pid)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_EVENTS_PAGE_NO_EVENT_ID'),
				'error'
			);
			return;
		}

		// Get the element moving
		$page = Page::oneOrFail($id);

		if (!$page->move($move == 'up' ? -1 : 1))
		{
			Notify::error($page->getError());
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Cancel a task by redirecting to main page
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&event_id=' . Request::getInt('event_id', 0), false)
		);
	}
}
