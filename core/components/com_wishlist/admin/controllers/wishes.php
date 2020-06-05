<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Wishlist\Models\Wishlist;
use Components\Wishlist\Models\Wish;
use Components\Wishlist\Models\Plan;
use Components\Wishlist\Models\Owner;
use Components\Wishlist\Models\OwnerGroup;
use Components\Wishlist\Models\Tags;
use Exception;
use stdClass;
use Request;
use Notify;
use Config;
use Event;
use Route;
use Lang;
use User;
use App;

/**
 * Cotnroller class for wishes
 */
class Wishes extends AdminController
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
		$this->registerTask('grant', 'state');
		$this->registerTask('pending', 'state');
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
			'wishlist' => Request::getState(
				$this->_option . '.' . $this->_controller . '.wishlist',
				'wishlist',
				0,
				'int'
			),
			'status' => Request::getState(
				$this->_option . '.' . $this->_controller . '.status',
				'status',
				'all'
			),
			'tag' => Request::getState(
				$this->_option . '.' . $this->_controller . '.tag',
				'tag',
				''
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'subject'
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

		$wishlist = Wishlist::oneOrNew($filters['wishlist']);

		$model = Wish::all();

		if ($filters['wishlist'])
		{
			$model->whereEquals('wishlist', $filters['wishlist']);
		}

		if ($filters['search'])
		{
			$model
				->whereLike('subject', strtolower((string)$filters['search']), 1)
				->orWhereLike('about', strtolower((string)$filters['search']), 1)
				->resetDepth();
		}

		if ($filters['status'])
		{
			// list  filtering
			switch ($filters['status'])
			{
				case 'granted':
					$model->whereEquals('status', Wish::WISH_STATE_GRANTED);
					break;
				case 'open':
					$model->whereEquals('status', Wish::WISH_STATE_OPEN);
					break;
				case 'accepted':
					$model
						->whereIn('status', array(
							Wish::WISH_STATE_OPEN,
							Wish::WISH_STATE_ACCEPTED
						))
						->whereEquals('accepted', 1);
					break;
				case 'pending':
					$model
						->whereEquals('accepted', 0)
						->whereEquals('status', Wish::WISH_STATE_OPEN);
					break;
				case 'rejected':
					$model->whereEquals('status', Wish::WISH_STATE_REJECTED);
					break;
				case 'withdrawn':
					$model->whereEquals('status', Wish::WISH_STATE_WITHDRAWN);
					break;
				case 'deleted':
					$model->whereEquals('status', Wish::WISH_STATE_DELETED);
					break;
				case 'useraccepted':
					$model
						->whereEquals('accepted', 3)
						->where('status', '!=', Wish::WISH_STATE_DELETED);
					break;
				case 'private':
					$model
						->whereEquals('private', 1)
						->where('status', '!=', Wish::WISH_STATE_DELETED);
					break;
				case 'public':
					$model
						->whereEquals('private', 0)
						->where('status', '!=', Wish::WISH_STATE_DELETED);
					break;
				case 'assigned':
					$model
						->where('status', '!=', Wish::WISH_STATE_DELETED)
						->whereRaw('assigned NOT NULL');
					break;
				case 'all':
				default:
					$model->where('status', '!=', Wish::WISH_STATE_DELETED);
					break;
			}
		}

		// Get records
		$rows = $model
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('wishlist', $wishlist)
			->set('rows', $rows)
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

		$wishlist = Request::getInt('wishlist', 0);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getArray('id', array(0));

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load category
			$row = Wish::oneOrNew($id);
		}

		if ($row->isNew())
		{
			$row->set('wishlist', $wishlist);
		}


		$lists = Wishlist::all()
			->order('title', 'asc')
			->rows();

		// who are list owners?
		$this->admingroup = $this->config->get('group', 'hubadmin');

		$none = new stdClass;
		$none->id = '-1';
		$none->name = Lang::txt('COM_WISHLIST_SELECT');

		$ownerassignees = array();
		$ownerassignees[-1] = array();
		$ownerassignees[-1][] = $none;

		$assignees = null;

		if ($lists)
		{
			foreach ($lists as $k => $list)
			{
				$none = new stdClass;
				$none->id = '0';
				$none->name = Lang::txt('COM_WISHLIST_NONE');

				$ownerassignees[$list->id] = array();
				$ownerassignees[$list->id][] = $none;

				$owners = $list->getOwners($this->admingroup);

				if (count($owners['individuals']) > 0)
				{
					$query = "SELECT a.id, a.name FROM `#__users` AS a WHERE a.block = '0' AND a.id IN (" . implode(',', $owners['individuals']) . ") ORDER BY a.name";
					$this->database->setQuery($query);

					$users = $this->database->loadObjectList();

					foreach ($users as $row2)
					{
						$ownerassignees[$list->id][] = $row2;
					}

					if ($list->id == $row->wishlist)
					{
						$assignees = $ownerassignees[$list->id];
					}
				}
			}
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->set('row', $row)
			->set('lists', $lists)
			->set('ownerassignees', $ownerassignees)
			->set('assignees', $assignees)
			->set('wishlist', $wishlist)
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

		// Keep tags for saving
		$tags = $fields['tags'];
		// Remove tags from the fields
		unset($fields['tags']);

		// Initiate extended database class
		$row = Wish::oneOrNew($fields['id'])->set($fields);

		$row->set('anonymous', (isset($fields['anonymous']) && $fields['anonymous']) ? 1 : 0);
		$row->set('private', (isset($fields['private']) && $fields['private']) ? 1 : 0);
		$row->set('accepted', (isset($fields['accepted']) && $fields['accepted']) ? 1 : 0);

		// Trigger before save event
		$isNew  = $row->isNew();
		$result = Event::trigger('wishlist.onWishlistBeforeSaveWish', array(&$row, $isNew));

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

		// Set tags
		$row->tag($tags, User::get('id'));

		$plan = Request::getArray('plan', array(), 'post');
		$plan['wishid'] = ($plan['wishid'] ? $plan['wishid'] : $row->get('id'));

		$create_revision = isset($plan['create_revision']) ? $plan['create_revision'] : 0;
		unset($plan['create_revision']);

		// Initiate extended database class
		$old  = Plan::oneOrNew($plan['id']);

		$page = Plan::oneOrNew($plan['id']);
		$page->set($plan);

		// Forcefully create a new revision?
		if ($create_revision && rtrim(stripslashes($old->get('pagetext'))) != rtrim(stripslashes($page->get('pagetext'))))
		{
			$page->set('version', $page->get('version') + 1);
			$page->set('id', null);
		}

		if (!$page->save())
		{
			Notify::error($page->getError());
			return $this->editTask($row);
		}

		// Trigger after save event
		Event::trigger('wishlist.onWishlistAfterSaveWish', array(&$row, $isNew));

		Notify::success(Lang::txt('COM_WISHLIST_WISH_SAVED'));

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
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		$removed = 0;
		if (count($ids) > 0)
		{
			// Loop through each ID
			foreach ($ids as $id)
			{
				$row = Wish::oneOrFail(intval($id));

				if (!$row->destroy())
				{
					Notify::error($row->getError());
					continue;
				}

				// Trigger after delete event
				Event::trigger('wishlist.onWishlistAfterDeleteWish', array($id));

				$removed++;
			}
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_WISHLIST_ITEMS_REMOVED', $removed));
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Set the access level of an article
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
		$row = Wish::oneOrFail($id);
		$row->set('private', $access);

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
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$state = $this->getTask() == 'grant' ? Wish::WISH_STATE_GRANTED : Wish::WISH_STATE_OPEN;

		// Incoming
		$cid = Request::getInt('cid', 0);
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			Notify::warning($state ? Lang::txt('COM_WISHLIST_SELECT_PUBLISH') : Lang::txt('COM_WISHLIST_SELECT_UNPUBLISH'));
			return $this->cancelTask();
		}

		// Update record(s)
		$success = 0;
		foreach ($ids as $id)
		{
			$row = Wish::oneOrFail($id);
			$row->set('status', $state);

			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			// Set message
			switch ($this->getTask())
			{
				case 'trash':
					$message = Lang::txt('COM_WISHLIST_TRASHED', $success);
					break;
				case 'grant':
					$message = Lang::txt('COM_WISHLIST_ITEMS_GRANTED', $success);
					break;
				case 'pending':
					$message = Lang::txt('COM_WISHLIST_ITEMS_PENDING', $success);
					break;
			}

			Notify::success($message);
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$wishlist = Request::getInt('wishlist', 0);

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&wishlist=' . $wishlist, false)
		);
	}
}
