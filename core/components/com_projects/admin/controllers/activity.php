<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Projects\Models\Project;
use Request;
use Config;
use Route;
use Lang;
use User;
use App;

/**
 * Projects controller class for managing membership
 */
class Activity extends AdminController
{
	/**
	 * Determine task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		if (!User::authorize('core.manage', $this->_option))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false)
			);
		}

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');
		$this->registerTask('star', 'star');
		$this->registerTask('unstar', 'star');

		parent::execute();
	}

	/**
	 * Displays a list of members
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		$filters = array(
			'state' => Request::getState(
				$this->_option . '.projects.' . $this->_controller . '.state',
				'state',
				'-1',
				'int'
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.projects.' . $this->_controller . '.search',
				'search',
				''
			)),
			'action' => Request::getState(
				$this->_option . '.projects.' . $this->_controller . '.action',
				'action',
				''
			),
			'filter' => Request::getState(
				$this->_option . '.projects.' . $this->_controller . '.filter',
				'filter',
				''
			),
			'project' => Request::getState(
				$this->_option . '.projects.' . $this->_controller . '.project',
				'project',
				0,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.projects.' . $this->_controller . '.sort',
				'filter_order',
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.projects.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			)
		);

		if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
		{
			$filters['sort_Dir'] = 'ASC';
		}

		$recipient = \Hubzero\Activity\Recipient::all();

		$r = $recipient->getTableName();
		$l = \Hubzero\Activity\Log::blank()->getTableName();

		$recipient
			->select($r . '.*')
			->including('log')
			->join($l, $l . '.id', $r . '.log_id')
			->whereIn($r . '.scope', array('project', 'project_managers'))
			->whereEquals($r . '.state', \Hubzero\Activity\Recipient::STATE_PUBLISHED);

		if ($filters['state'] >= 0)
		{
			$recipient->whereLike($r . '.state', $filters['state']);
		}

		if ($filters['project'])
		{
			$recipient->whereEquals($r . '.scope_id', $filters['project']);
		}

		if ($filters['search'])
		{
			$recipient->whereLike($l . '.description', $filters['search']);
		}

		if ($filters['action'])
		{
			$recipient->whereEquals($l . '.action', $filters['action']);
		}

		if ($filters['filter'])
		{
			if ($filters['filter'] == 'starred')
			{
				$recipient->whereEquals($r . '.starred', 1);
			}
		}

		$rows = $recipient
			->ordered()
			->paginated('limitstart', 'limit')
			->rows();

		$model = new Project($filters['project']);

		if ($filters['project'] && !$model->exists())
		{
			Notify::error(Lang::txt('COM_PROJECTS_NOTICE_ID_NOT_FOUND'));

			App::redirect(
				Route::url('index.php?option=' . $this->_option, false)
			);
		}

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->set('project', $model)
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
			$row = \Hubzero\Activity\Recipient::oneOrNew($id);
		}

		if ($row->isNew())
		{
			$row->set('created_by', User::get('id'));
			$row->set('created', Date::toSql());
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save changes to an entry
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

		// Initiate extended database class
		$activity = \Hubzero\Activity\Log::oneOrNew($fields['id'])->set($fields);

		// Incoming
		$rfields = Request::getArray('recipient', array(), 'post');

		// Initiate extended database class
		$recipient = \Hubzero\Activity\Recipient::oneOrNew($rfields['id'])->set($rfields);

		// Trigger before save event
		$isNew  = $recipient->isNew();
		$result = Event::trigger('projects.onProjectBeforeSaveActivity', array(&$recipient, $isNew));

		if (in_array(false, $result, true))
		{
			Notify::error($recipient->getError());
			return $this->editTask($recipient);
		}

		// Store new content
		if (!$activity->save())
		{
			Notify::error($activity->getError());
			return $this->editTask($recipient);
		}

		if (!$recipient->save())
		{
			Notify::error($recipient->getError());
			return $this->editTask($recipient);
		}

		// Trigger after save event
		Event::trigger('projects.onProjectAfterSaveActivity', array(&$recipient, $isNew));

		// Notify of success
		Notify::success(Lang::txt('COM_PROJECTS_ITEM_SAVED'));

		// Redirect to main listing or go back to edit form
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Remove member(s) from a project
	 * Disallows removal of last manager (must have at least one)
	 *
	 * @return  void
	 */
	public function deleteTask()
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

		$removed = 0;

		foreach ($ids as $id)
		{
			$entry = \Hubzero\Activity\Recipient::oneOrFail(intval($id));

			// Delete the entry
			if (!$entry->destroy())
			{
				Notify::error($entry->getError());
				continue;
			}

			// Trigger before delete event
			Event::trigger('projects.onProjectAfterDeleteActivity', array($id));

			$removed++;
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_PROJECTS_ITEMS_DELETED', $removed));
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

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$state = ($this->getTask() == 'publish' ? \Hubzero\Activity\Recipient::STATE_PUBLISHED : \Hubzero\Activity\Recipient::STATE_UNPUBLISHED);

		$i = 0;

		foreach ($ids as $id)
		{
			// Update record(s)
			$row = \Hubzero\Activity\Recipient::oneOrFail(intval($id));
			$row->set('state', $state);

			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		// set message
		if ($i)
		{
			if ($state == \Hubzero\Activity\Recipient::STATE_PUBLISHED)
			{
				$message = Lang::txt('COM_PROJECTS_ITEMS_PUBLISHED', $i);
			}
			else
			{
				$message = Lang::txt('COM_PROJECTS_ITEMS_UNPUBLISHED', $i);
			}

			Notify::success($message);
		}

		$this->cancelTask();
	}

	/**
	 * Set featured state of activity
	 *
	 * @return  void
	 */
	public function starTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$i = 0;

		//foreach group id passed in
		foreach ($ids as $id)
		{
			// Update record(s)
			$entry = \Hubzero\Activity\Recipient::oneOrFail(intval($id));

			if ($this->getTask() == 'star')
			{
				if (!$entry->markAsStarred())
				{
					Notify::error($entry->getError());
					continue;
				}
			}
			else
			{
				if (!$entry->markAsNotStarred())
				{
					Notify::error($entry->getError());
					continue;
				}
			}

			// Allow plugins to respond to changes
			Event::trigger('projects.onProjectAfterSaveActivity', array($entry));

			$i++;
		}

		// Output messsage and redirect
		if ($i)
		{
			Notify::success(Lang::txt('COM_PROJECTS_ITEMS_' . $this->getTask(), $i));
		}

		$this->cancelTask();
	}

	/**
	 * Cancel a task
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&project=' . Request::getInt('project', 0), false)
		);
	}
}
