<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Support\Models\Status;
use Request;
use Notify;
use Route;
use Lang;
use App;

require_once dirname(dirname(__DIR__)) . '/models/status.php';

/**
 * Support controller class for managing ticket statuses
 */
class Statuses extends AdminController
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
	 * Displays a list of records
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get paging variables
		$filters = array(
			'open' => Request::getState(
				$this->_option . '.' . $this->_controller . '.open',
				'open',
				-1,
				'int'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'open'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			)
		);

		// Fetch results
		$entries = Status::all();

		if ($filters['open'] >= 0)
		{
			$entries->whereEquals('open', (int)$filters['open']);
		}

		$rows = $entries
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->display();
	}

	/**
	 * Display a form for adding/editing a record
	 *
	 * @param   mixed  $row
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
			$id = (is_array($id) ? $id[0] : $id);

			// Initiate database class and load info
			$row = Status::oneOrNew($id);
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save changes to a record
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

		// Trim and addslashes all posted items
		$fields = Request::getArray('fields', array(), 'post');

		// Initiate class and bind posted items to database fields
		$row = Status::oneOrNew($fields['id'])->set($fields);

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_SUPPORT_STATUS_SUCCESSFULLY_SAVED'));

		// Output messsage and redirect
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Delete one or more records
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

		// Check for an ID
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_SUPPORT_ERROR_SELECT_STATUS_TO_DELETE'));
			return $this->cancelTask();
		}

		$i = 0;
		foreach ($ids as $id)
		{
			// Delete entry
			$row = Status::oneOrFail(intval($id));

			if (!$row->destroy())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		// Output messsage and redirect
		if ($i)
		{
			Notify::success(Lang::txt('COM_SUPPORT_STATUS_SUCCESSFULLY_DELETED', $i));
		}

		$this->cancelTask();
	}
}
