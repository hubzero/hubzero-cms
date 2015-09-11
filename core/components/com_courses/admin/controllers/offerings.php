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

namespace Components\Courses\Admin\Controllers;

use Hubzero\Component\AdminController;
use Exception;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'course.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'offering.php');

/**
 * Courses controller class for managing membership and course info
 */
class Offerings extends AdminController
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
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
			'course' => Request::getState(
				$this->_option . '.' . $this->_controller . '.course',
				'course',
				0
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				'-1'
			),
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

		$this->view->course = \Components\Courses\Models\Course::getInstance($this->view->filters['course']);
		if (!$this->view->course->exists())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=courses', false)
			);
			return;
		}

		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		$this->view->filters['count'] = true;

		$this->view->total = $this->view->course->offerings($this->view->filters);

		$this->view->filters['count'] = false;

		$this->view->rows = $this->view->course->offerings($this->view->filters);

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
	 * @return  void
	 */
	public function editTask($model=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($model))
		{
			// Incoming
			$id = Request::getVar('id', array(0));

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			$model = \Components\Courses\Models\Offering::getInstance($id);
		}

		$this->view->row = $model;

		if (!$this->view->row->get('course_id'))
		{
			$this->view->row->set('course_id', Request::getInt('course', 0));
		}

		$this->view->course = \Components\Courses\Models\Course::getInstance($this->view->row->get('course_id'));
		$this->view->config = $this->config;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			\Notify::error($error);
		}

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
		$fields = Request::getVar('fields', array(), 'post');

		// Instantiate a Course object
		$model = \Components\Courses\Models\Offering::getInstance($fields['id']);

		if (!$model->bind($fields))
		{
			$this->setError($model->getError());
			$this->editTask($model);
			return;
		}

		$p = new \Hubzero\Config\Registry('');
		$p->parse(Request::getVar('params', '', 'post'));

		// Make sure the logo gets carried over
		$op = new \Hubzero\Config\Registry($model->get('params'));
		$p->set('logo', $op->get('logo'));

		$model->set('params', $p->toString());

		if (!$model->store(true))
		{
			$this->setError($model->getError());
			$this->editTask($model);
			return;
		}

		if ($this->_task == 'apply')
		{
			return $this->editTask($model);
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . $model->get('course_id'), false),
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
		$id = Request::getVar('id', 0);

		// Get the single ID we're working with
		if (is_array($id))
		{
			$id = (!empty($id)) ? $id[0] : 0;
		}

		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . Request::getInt('course', 0), false),
				Lang::txt('COM_COURSES_ERROR_NO_ID'),
				'error'
			);
			return;
		}

		$offering = \Components\Courses\Models\Offering::getInstance($id);
		if (!$offering->copy())
		{
			// Redirect back to the courses page
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . $offering->get('course_id'), false),
				Lang::txt('COM_COURSES_ERROR_COPY_FAILED') . ': ' . $offering->getError(),
				'error'
			);
			return;
		}

		// Redirect back to the courses page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . $offering->get('course_id'), false),
			Lang::txt('COM_COURSES_ITEM_COPIED')
		);
	}

	/**
	 * Removes a course and all associated information
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$num = 0;

		// Do we have any IDs?
		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				// Load the course page
				$model = \Components\Courses\Models\Offering::getInstance($id);

				// Ensure we found the course info
				if (!$model->exists())
				{
					continue;
				}

				// Delete course
				if (!$model->delete())
				{
					throw new Exception(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_REMOVE_ENTRY'), 500);
				}

				$num++;
			}
		}

		// Redirect back to the courses page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . Request::getInt('course', 0), false),
			Lang::txt('COM_COURSES_ITEMS_REMOVED', $num)
		);
	}

	/**
	 * Set the state of a course
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$state = $this->_task == 'publish' ? 1 : 0;

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		$num = 0;
		if (!empty($ids))
		{
			//foreach course id passed in
			foreach ($ids as $id)
			{
				// Load the course page
				$model = \Components\Courses\Models\Offering::getInstance($id);

				// Ensure we found the course info
				if (!$model->exists())
				{
					continue;
				}

				//set the course to be published and update
				$model->set('state', $state);
				if (!$model->store())
				{
					$this->setError(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_SET_STATE', $id));
					continue;
				}

				// Log the course approval
				$model->log($model->get('id'), 'offering', ($state ? 'published' : 'unpublished'));

				$num++;
			}
		}

		if ($this->getErrors())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . Request::getInt('course', 0), false),
				implode('<br />', $this->getErrors()),
				'error'
			);
		}
		else
		{
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . Request::getInt('course', 0), false),
				($state ? Lang::txt('COM_COURSES_ITEMS_PUBLISHED', $num) : Lang::txt('COM_COURSES_ITEMS_UNPUBLISHED', $num))
			);
		}
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . Request::getInt('course', 0), false)
		);
	}
}
