<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Models\Author;
use Components\Resources\Models\Author\Role;
use Hubzero\Component\AdminController;
use Request;
use Notify;
use Route;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'author.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'author' . DS . 'role.php';

/**
 * Manage resource authors
 */
class Authors extends AdminController
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
	 * List resource authors
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
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'name'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// Get records
		$model = Author::all();

		if ($filters['search'])
		{
			$model->whereLike('name', strtolower($filters['search']));
		}

		$rows = $model
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
	 * Edit an entry
	 *
	 * @param   array  $rows
	 * @return  void
	 */
	public function editTask($rows=null)
	{
		Request::setVar('hidemainmenu', 1);

		$authorid = 0;

		if (!is_array($rows))
		{
			// Incoming
			$authorid = Request::getArray('id', array(0));
			if (is_array($authorid))
			{
				$authorid = (!empty($authorid) ? $authorid[0] : 0);
			}

			$rows = Author::all()
				->whereEquals('authorid', $authorid)
				->rows();
		}

		$roles = Role::all()
			->ordered()
			->rows();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			Notify::error($error);
		}

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('authorid', $authorid)
			->set('roles', $roles)
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

		// Incoming
		$fields   = Request::getArray('fields', array(), 'post');
		$authorid = Request::getInt('authorid', 0);
		$id       = Request::getInt('id', 0);

		if (!$authorid)
		{
			return $this->cancelTask();
		}

		$rows = array();

		if (is_array($fields))
		{
			foreach ($fields as $fieldset)
			{
				$fieldset['authorid'] = $authorid;

				$row = Role::oneOrNew($fieldset['id'])->set($fieldset);

				if (!$row->save())
				{
					$this->setError($row->getError());
				}

				$rows[] = $row;
			}
		}

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($rows);
		}

		$this->cancelTask();
	}
}
