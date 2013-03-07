<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'member.php');

/**
 * Courses controller class for managing membership and course info
 */
class CoursesControllerStudents extends Hubzero_Controller
{
	/**
	 * Displays a list of courses
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['offering']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.offering',
			'offering',
			0
		);
		$this->view->filters['section_id']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.section',
			'section',
			0
		);

		$this->view->offering = CoursesModelOffering::getInstance($this->view->filters['offering']);
		/*if (!$this->view->offering->exists())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=courses'
			);
			return;
		}*/
		$this->view->course = CoursesModelCourse::getInstance($this->view->offering->get('course_id'));

		$this->view->filters['search']  = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		)));
		// Filters for returning results
		$this->view->filters['limit']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		//$this->view->filters['role'] = 'student';

		$this->view->filters['count'] = true;

		$this->view->total = $this->view->offering->students($this->view->filters);

		$this->view->filters['count'] = false;

		$this->view->rows = $this->view->offering->students($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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
		JRequest::setVar('hidemainmenu', 1);

		$offering = JRequest::getInt('offering', 0);
		$this->view->offering = CoursesModelOffering::getInstance($offering);

		$id = 0;

		$this->view->row = CoursesModelMember::getInstance($id, $offering);

		$this->view->course = CoursesModelCourse::getInstance($this->view->offering->get('course_id'));

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		$offering = JRequest::getInt('offering', 0);
		$this->view->offering = CoursesModelOffering::getInstance($offering);

		if (is_object($model))
		{
			$this->view->row = $model;
		}
		else
		{
			// Incoming
			$ids = JRequest::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($ids))
			{
				$id = (!empty($ids)) ? $ids[0] : 0;
			}
			else
			{
				$id = 0;
			}

			$this->view->row = CoursesModelMember::getInstance($id, $offering);
		}

		$this->view->course = CoursesModelCourse::getInstance($this->view->offering->get('course_id'));

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Saves data to database and return to the edit form
	 *
	 * @return void
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
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');

		if (strstr($fields['user_id'], ','))
		{
			$user_ids = explode(',', $fields['user_id']);
			$user_ids = array_map('trim', $user_ids);
		}
		else
		{
			$user_ids = array($fields['user_id']);
		}

		$offering = JRequest::getInt('offering', 0);

		foreach ($user_ids as $user_id)
		{
			if (!is_int($user_id))
			{
				$user = JUser::getInstance($user_id);
				$fields['user_id'] = $user->get('id');
			}
			else
			{
				$fields['user_id'] = $user_id;
			}
			// Instantiate the model
			$model = CoursesModelMember::getInstance($fields['user_id'], $offering);

			// Bind posted data
			if (!$model->bind($fields))
			{
				$this->addComponentMessage($model->getError());
				$this->editTask($model);
				return;
			}
			// Store data
			if (!$model->store())
			{
				$this->addComponentMessage($model->getError());
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
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . $fields['offering_id'] . '&section=' . $fields['section_id'],
				JText::_('COM_COURSES_SAVED')
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
	public function deleteTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());
		$offering_id = JRequest::getInt('offering', 0);

		// Get the single ID we're working with
		if (!is_array($ids))
		{
			$ids = array();
		}

		$num = 0;

		// Do we have any IDs?
		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				// Load the course page
				$model = CoursesModelMember::getInstance($id);

				// Ensure we found the course info
				if (!$model->exists())
				{
					continue;
				}

				$data = json_encode($model);

				// Delete course
				if (!$model->delete())
				{
					JError::raiseError(500, JText::_('Unable to delete member'));
					return;
				}

				// Log the course approval
				$log = new CoursesTableLog($this->database);
				$log->scope_id  = $id;
				$log->scope     = 'section_member';
				$log->user_id   = $this->juser->get('id');
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action    = 'offering_deleted';
				$log->actor_id  = $this->juser->get('id');
				$log->comment   = $data;
				if (!$log->store())
				{
					$this->setError($log->getError());
				}

				$num++;
			}
		}

		// Redirect back to the courses page
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . $offering_id . '&section=' . $section_id,
			JText::sprintf('%s Item(s) removed.', $num)
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$offering_id = JRequest::getInt('offering', 0);
		$section_id  = JRequest::getInt('section', 0);

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($offering_id ? '&offering=' . $offering_id : '') . ($section_id ? '&section=' . $section_id : '')
		);
	}
}
