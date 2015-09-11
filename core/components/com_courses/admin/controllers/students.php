<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Courses\Admin\Controllers;

use Components\Courses\Tables;
use Hubzero\Component\AdminController;
use Exception;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'course.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'offering.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'member.php');

/**
 * Courses controller class for managing membership and course info
 */
class Students extends AdminController
{
	/**
	 * Displays a list of courses
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
			'offering' => Request::getState(
				$this->_option . '.' . $this->_controller . '.offering',
				'offering',
				0
			),
			'section_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.section',
				'section',
				0
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
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
			)
		);

		$this->view->offering = \Components\Courses\Models\Offering::getInstance($this->view->filters['offering']);
		$this->view->filters['offering_id'] = $this->view->filters['offering'];
		/*if (!$this->view->offering->exists())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=courses', false)
			);
			return;
		}*/
		$this->view->course = \Components\Courses\Models\Course::getInstance($this->view->offering->get('course_id'));

		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);
		//$this->view->filters['role'] = 'student';

		//$this->view->filters['count'] = true;

		/*if (!$this->view->filters['section_id'])
		{
			$this->view->filters['section_id'] = array();
			foreach ($this->view->offering->sections() as $section)
			{
				$this->view->filters['section_id'][] = $section->get('id');
			}
		}*/
		if (!$this->view->filters['offering_id'])
		{
			$this->view->filters['offering_id'] = null;
		}
		if (!$this->view->filters['section_id'])
		{
			$this->view->filters['section_id'] = null;
		}
		$this->view->filters['student'] = 1;

		$tbl = new Tables\Member($this->database);

		$this->view->total = $tbl->count($this->view->filters); //$this->view->offering->students($this->view->filters);

		//$this->view->filters['count'] = false;

		$this->view->rows = $tbl->find($this->view->filters); //$this->view->offering->students($this->view->filters);
		if ($this->view->rows)
		{
			foreach ($this->view->rows as $key => $row)
			{
				$this->view->rows[$key] = new \Components\Courses\Models\Student($row);
			}
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new course
	 *
	 * @return	void
	 */
	public function addTask()
	{
		Request::setVar('hidemainmenu', 1);

		$offering = Request::getInt('offering', 0);
		$this->view->offering = \Components\Courses\Models\Offering::getInstance($offering);

		$id = 0;

		$this->view->row = \Components\Courses\Models\Member::getInstance($id, $offering);

		$this->view->course = \Components\Courses\Models\Course::getInstance($this->view->offering->get('course_id'));

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
	 * @return	void
	 */
	public function editTask($model=null)
	{
		Request::setVar('hidemainmenu', 1);

		$offering = Request::getInt('offering', 0);
		$this->view->offering = \Components\Courses\Models\Offering::getInstance($offering);

		if (!is_object($model))
		{
			// Incoming
			$id = Request::getVar('id', array(0));

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			$course_id  = $this->view->offering->get('course_id');
			$section_id = $this->view->offering->section()->get('id');

			$model = \Components\Courses\Models\Student::getInstance($id, null, null, null); //, $course_id, $offering, $section_id);
		}

		$this->view->row = $model;

		$this->view->course = \Components\Courses\Models\Course::getInstance($this->view->offering->get('course_id'));

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Saves data to database and return to the edit form
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Saves data to the database
	 *
	 * @return void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$fields = Request::getVar('fields', array(), 'post');

		if (strstr($fields['user_id'], ','))
		{
			$user_ids = explode(',', $fields['user_id']);
			$user_ids = array_map('trim', $user_ids);
		}
		else
		{
			$user_ids = array($fields['user_id']);
		}

		$offering = Request::getInt('offering', 0);
		if (!$offering && isset($fields['offering_id']))
		{
			$offering = $fields['offering_id'];
		}
		$offeringObj = \Components\Courses\Models\Offering::getInstance($offering);

		$c = 0;
		foreach ($user_ids as $user_id)
		{
			if (!is_int($user_id))
			{
				$user = User::getInstance($user_id);
				if (!is_object($user))
				{
					\Notify::error(Lang::txt('COM_COURSES_ERROR_USER_NOTFOUND') . ' ' . $user_id);
					$this->editTask( );
					return;
				}
				$fields['user_id'] = $user->get('id');
			}
			else
			{
				$fields['user_id'] = $user_id;
			}
			// Instantiate the model
			$fields['course_id'] = $offeringObj->get('course_id');
			//$section_id = $offeringObj->section()->get('id');

			//$model = \Components\Courses\Models\Member::getInstance($fields['user_id'], $fields['course_id'], $offering, $section_id);
			$model = \Components\Courses\Models\Member::getInstance($fields['user_id'], $fields['course_id'], null, null);

			// Is there an existing record and are they a student?
			if ($model->exists() && !$model->get('student'))
			{
				\Notify::error(Lang::txt('COM_COURSES_ERROR_ALREADY_COURSE_MANAGER', $user_id));
				continue;
			}
			// If the section is the same
			if ($model->exists() && $model->get('section_id') == $fields['section_id'])
			{
				\Notify::warning(Lang::txt('COM_COURSES_ERROR_ALREADY_STUDENT', $user_id));
				continue;
			}

			// Ensure it's a new record as the check above
			// could pull a record for another section
			$model->set('id', null);

			// Safe to proceed...

			// Bind posted data
			if (!$model->bind($fields))
			{
				\Notify::error($model->getError());
				$this->editTask($model);
				return;
			}

			// Store data
			if (!$model->store())
			{
				\Notify::error($model->getError());
				$this->editTask($model);
				return;
			}
		}

		if (count($user_ids) > 1)
		{
			$redirect = true;
		}

		// Are we redirecting?
		if ($redirect)
		{
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . $fields['offering_id'] . '&section=' . $fields['section_id'], false),
				($c > 0 ? Lang::txt('COM_COURSES_STUDENTS_SAVED', $c) : null)
			);
			return;
		}

		// Display edit form with posted data
		$this->editTask($model);
	}

	/**
	 * Removes a course and all associated information
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$offering_id = Request::getInt('offering', 0);
		$section_id  = Request::getInt('section', 0);

		$num = 0;

		// Do we have any IDs?
		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				// Load the student record
				$model = \Components\Courses\Models\Student::getInstance($id, null, null, null); //, $offering->get('course_id'), $offering_id, $section_id);

				// Ensure we found the course info
				if (!$model->exists())
				{
					continue;
				}

				// Delete course
				if (!$model->delete())
				{
					\Notify::error(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_REMOVE_STUDENT', $model->get('user_id'), $model->get('section_id')));
					continue;
				}

				$num++;
			}
		}

		// Redirect back to the courses page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($offering_id ? '&offering=' . $offering_id : '') . ($section_id ? '&section=' . $section_id : ''), false),
			($num > 0 ? Lang::txt('COM_COURSES_STUDENTS_REMOVED', $num) : null)
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$offering_id = Request::getInt('offering', 0);
		$section_id  = Request::getInt('section', 0);

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($offering_id ? '&offering=' . $offering_id : '') . ($section_id ? '&section=' . $section_id : ''), false)
		);
	}

	/**
	 * Save students info as CSV file
	 *
	 * @return  void
	 */
	public function csvTask()
	{
		$this->view->filters = array(
			'offering' => Request::getState(
				$this->_option . '.' . $this->_controller . '.offering',
				'offering',
				0
			),
			'section_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.section',
				'section',
				0
			)
		);

		$this->view->offering = \Components\Courses\Models\Offering::getInstance($this->view->filters['offering']);
		$this->view->filters['offering_id'] = $this->view->filters['offering'];
		$this->view->course = \Components\Courses\Models\Course::getInstance($this->view->offering->get('course_id'));

		if (!$this->view->filters['offering_id'])
		{
			$this->view->filters['offering_id'] = null;
		}
		if (!$this->view->filters['section_id'])
		{
			$this->view->filters['section_id'] = null;
		}
		$this->view->filters['student'] = 1;

		$tbl = new Tables\Member($this->database);

		$this->view->rows = $tbl->find($this->view->filters); //$this->view->offering->students($this->view->filters);
		if ($this->view->rows)
		{
			foreach ($this->view->rows as $key => $row)
			{
				$this->view->rows[$key] = new \Components\Courses\Models\Student($row);
			}
		}

		// Output the CSV
		$this->view->display();
	}
}
