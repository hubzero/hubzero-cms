<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Support\Models\Category;
use Request;
use Notify;
use Route;
use Lang;

require_once dirname(dirname(__DIR__)) . '/models/category.php';

/**
 * Support controller class for categories
 */
class Categories extends AdminController
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

		// Fetch results
		$rows = Category::all()
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
			if (is_array($id))
			{
				$id = (!empty($id) ? $id[0] : 0);
			}

			// Initiate database class and load info
			$row = Category::oneOrNew($id);
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
		$row = Category::oneOrNew($fields['id'])->set($fields);

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_SUPPORT_CATEGORY_SUCCESSFULLY_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
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
		$ids = Request::getArray('id', array(0));
		if (!is_array($ids))
		{
			$ids = array(0);
		}

		// Check for an ID
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_SUPPORT_ERROR_SELECT_CATEGORY_TO_DELETE'));
			return $this->cancelTask();
		}

		$i = 0;
		foreach ($ids as $id)
		{
			// Delete entry
			$row = Category::oneOrFail(intval($id));

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
			Notify::success(Lang::txt('COM_SUPPORT_CATEGORY_SUCCESSFULLY_DELETED', $i));
		}

		$this->cancelTask();
	}
}
