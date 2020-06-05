<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Admin\Controllers;

use Hubzero\Component\AdminController;
use Exception;
use Request;
use Config;
use Route;
use Lang;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'courses.php';

/**
 * Courses controller class for managing membership and course info
 */
class Courses extends AdminController
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
	 * Displays a list of courses
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
			'search' => urldecode(trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			))),
			// Filters for returning results
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
			),
			'state' => trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				'-1'
			)),
			// Get sorting variables
			'sort' => trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
			)),
			'sort_Dir' => trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			))
		);

		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		$model = \Components\Courses\Models\Courses::getInstance();

		$this->view->filters['count'] = true;

		$this->view->total = $model->courses($this->view->filters);

		$this->view->filters['count'] = false;

		$this->view->rows  = $model->courses($this->view->filters);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Displays an edit form
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getArray('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			$row = \Components\Courses\Models\Course::getInstance($id);
		}

		$this->view->row = $row;

		if (!$this->view->row->exists())
		{
			$this->view->row->set('state', 3);
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			\Notify::error($error);
		}

		$this->view->config = $this->config;

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Saves changes to a course or saves a new entry if creating
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$fields = Request::getArray('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = new \Components\Courses\Models\Course(0);
		if (!$row->bind($fields))
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		$isNew = $this->get('id') ? false : true;

		// Go ahead and store content if new
		$validated = $isNew ? $row->store(true) : $row->check();
		if (!$validated)
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		$tags = Request::getString('tags', '', 'post');
		$row->tag($tags, User::get('id'));
		$row->store(false);

		if ($this->_task == 'apply')
		{
			return $this->editTask($row);
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_COURSES_ITEM_SAVED')
		);
	}

	/**
	 * Copy an entry and all associated data
	 *
	 * @return  void
	 */
	public function copyTask()
	{
		// Incoming
		$id = Request::getArray('id', array());

		// Get the single ID we're working with
		if (is_array($id))
		{
			$id = (!empty($id)) ? $id[0] : 0;
		}

		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_COURSES_ERROR_NO_ID'),
				'error'
			);
			return;
		}

		$course = \Components\Courses\Models\Course::getInstance($id);
		if (!$course->copy())
		{
			// Redirect back to the courses page
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_COURSES_ERROR_COPY_FAILED') . ': ' . $course->getError(),
				'error'
			);
			return;
		}

		// Redirect back to the courses page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_COURSES_ITEM_COPIED')
		);
	}

	/**
	 * Removes a course and all associated information
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$num = 0;

		// Do we have any IDs?
		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				// Load the course page
				$course = \Components\Courses\Models\Course::getInstance($id);

				// Ensure we found the course info
				if (!$course->exists())
				{
					continue;
				}

				// Delete course
				if (!$course->delete())
				{
					throw new Exception(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_REMOVE_ENTRY'), 500);
				}

				$num++;
			}
		}

		// Redirect back to the courses page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_COURSES_ITEM_REMOVED')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Set the state of a course
	 *
	 * @param   integer  $state
	 * @return  void
	 */
	public function stateTask($state=0)
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$state = $this->_task == 'publish' ? 1 : 0;

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		$num = 0;
		if (!empty($ids))
		{
			// foreach course id passed in
			foreach ($ids as $id)
			{
				// Load the course page
				$course = \Components\Courses\Models\Course::getInstance($id);

				// Ensure we found the course info
				if (!$course->exists())
				{
					continue;
				}

				//set the course to be published and update
				$course->set('state', $state);
				if (!$course->store())
				{
					$this->setError(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_SET_STATE', $id));
					continue;
				}

				// Log the course approval
				$course->log($course->get('id'), 'course', ($state ? 'published' : 'unpublished'));

				$num++;
			}
		}

		if ($this->getErrors())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				implode('<br />', $this->getErrors()),
				'error'
			);
		}
		else
		{
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				($state ? Lang::txt('COM_COURSES_ITEMS_PUBLISHED', $num) : Lang::txt('COM_COURSES_ITEMS_UNPUBLISHED', $num))
			);
		}
	}
}
