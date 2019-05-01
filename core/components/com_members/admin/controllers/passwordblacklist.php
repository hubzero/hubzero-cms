<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Hubzero\Password\Blacklist;
use Hubzero\Component\AdminController;
use Request;
use Notify;
use Route;
use Lang;
use App;

/**
 * Manage members password blacklist
 */
class PasswordBlacklist extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		Lang::load($this->_option . '.passwords', dirname(__DIR__));

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display password blacklist
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'word'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort_Dir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$rows = Blacklist::all()
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
	 * Edit a blacklisted password
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.create', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!$row)
		{
			// Incoming
			$id = Request::getArray('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			$row = Blacklist::oneOrNew($id);
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setErrors($this->getErrors())
			->setLayout('edit')
			->display();
	}

	/**
	 * Save blacklisted password
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.create', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming password blacklist edits
		$fields = Request::getArray('fields', array(), 'post');

		// Load the record
		$row = Blacklist::oneOrNew($fields['id'])->set($fields);

		// Try to save
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_MEMBERS_PASSWORD_BLACKLIST_SAVE_SUCCESS'));

		// Fall through to edit form
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Removes [a] password blacklist item(s)
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$i = 0;

		// Do we have any IDs?
		if (!empty($ids))
		{
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$id = intval($id);

				// Remove the record
				$row = Blacklist::oneOrFail($id);

				if (!$row->destroy())
				{
					Notify::error($row->getError());
					continue;
				}

				$i++;
			}
		}
		else // no rows were selected
		{
			Notify::warning(Lang::txt('COM_MEMBERS_PASSWORD_BLACKLIST_DELETE_NO_ROW_SELECTED'));
		}

		// Output messsage and redirect
		if ($i)
		{
			Notify::success(Lang::txt('COM_MEMBERS_PASSWORD_BLACKLIST_DELETE_SUCCESS'));
		}

		$this->cancelTask();
	}
}
