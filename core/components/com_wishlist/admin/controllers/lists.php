<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Wishlist\Models\Wishlist;
use Request;
use Config;
use Notify;
use Route;
use Event;
use Lang;
use User;
use App;

/**
 * Cotnroller class for wish lists
 */
class Lists extends AdminController
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
		$this->registerTask('accesspublic', 'access');
		$this->registerTask('accessregistered', 'access');
		$this->registerTask('accessspecial', 'access');

		parent::execute();
	}

	/**
	 * Display a list of entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'category' => Request::getState(
				$this->_option . '.' . $this->_controller . '.category',
				'category',
				''
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
			),
			// Get paging variables
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			)
		);

		$model = Wishlist::all();

		if ($filters['search'])
		{
			$model
				->whereLike('title', strtolower((string)$filters['search']), 1)
				->orWhereLike('description', strtolower((string)$filters['search']), 1)
				->resetDepth();
		}

		if ($filters['category'])
		{
			$model->whereEquals('category', $filters['category']);
		}

		// Get records
		$rows = $model
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$categories = Wishlist::all()
			->select('DISTINCT(category)')
			->rows();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->set('categories', $categories)
			->display();
	}

	/**
	 * Edit an entry
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

			// Load category
			$row = Wishlist::oneOrNew($id);
		}

		if ($row->isNew())
		{
			$row->set('created', Date::toSql());
			$row->set('created_by', User::get('id'));
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
		$fields = Request::getArray('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = Wishlist::oneOrNew($fields['id'])->set($fields);
		$row->set('state', (isset($fields['state'])) ? Wishlist::STATE_PUBLISHED : Wishlist::STATE_UNPUBLISHED);
		$row->set('public', (isset($fields['public'])) ? 1 : 0);

		// Trigger before save event
		$isNew  = $row->isNew();
		$result = Event::trigger('wishlist.onWishlistBeforeSave', array(&$row, $isNew));

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
		Event::trigger('wishlist.onWishlistAfterSave', array(&$row, $isNew));

		Notify::success(Lang::txt('COM_WISHLIST_LIST_SAVED'));

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
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Make sure we have an ID to work with
		if (!count($ids))
		{
			Notify::error(Lang::txt('COM_WISHLIST_NO_ID'));
			return $this->cancelTask();
		}

		$removed = 0;
		foreach ($ids as $id)
		{
			$row = Wishlist::oneOrFail($id);

			// Delete the list
			if (!$row->destroy())
			{
				Notify::error($row->getError());
				continue;
			}

			// Trigger after delete event
			Event::trigger('wishlist.onWishlistAfterDelete', array($id));

			$removed++;
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_WISHLIST_ITEMS_REMOVED', $removed));
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Set the access level of an entry
	 *
	 * @return  void
	 */
	public function accessTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$id = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			Notify::error(Lang::txt('COM_WISHLIST_NO_ID'));
			return $this->cancelTask();
		}

		switch ($this->getTask())
		{
			case 'accesspublic':
				$access = 0;
				break;
			case 'accessregistered':
				$access = 1;
				break;
			case 'accessspecial':
				$access = 2;
				break;
		}

		// Load the article
		$row = Wishlist::oneOrFail($id);
		$row->set('public', $access);

		// Check and store the changes
		if (!$row->save())
		{
			Notify::error($row->getError());
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Set the state of an entry
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$state = $this->getTask() == 'publish' ? Wishlist::STATE_PUBLISHED : Wishlist::STATE_UNPUBLISHED;

		// Incoming
		$cid = Request::getInt('cid', 0);
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			Notify::error($state ? Lang::txt('COM_WISHLIST_SELECT_PUBLISH') : Lang::txt('COM_WISHLIST_SELECT_UNPUBLISH'));
			return $this->cancelTask();
		}

		// Update record(s)
		$success = 0;
		foreach ($ids as $id)
		{
			// Updating a category
			$row = Wishlist::oneOrFail($id);
			$row->set('state', $state);

			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$success++;
		}

		// Set message
		if ($success)
		{
			switch ($this->getTask())
			{
				case 'archive':
					Notify::success(Lang::txt('COM_WISHLIST_ARCHIVED', $success));
				break;
				case 'publish':
					Notify::success(Lang::txt('COM_WISHLIST_PUBLISHED', $success));
				break;
				case 'unpublish':
					Notify::success(Lang::txt('COM_WISHLIST_UNPUBLISHED', $success));
				break;
			}
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($cid ? '&id=' . $cid : ''), false)
		);
	}
}
