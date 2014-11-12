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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');

/**
 * Courses controller class for managing membership and course info
 */
class CoursesControllerOfferings extends \Hubzero\Component\AdminController
{
	/**
	 * Displays a list of courses
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array(
			'course' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.course',
				'course',
				0
			)
		);

		$this->view->course = CoursesModelCourse::getInstance($this->view->filters['course']);
		if (!$this->view->course->exists())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=courses'
			);
			return;
		}

		$this->view->filters['search']  = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		)));
		$this->view->filters['state']  = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.state',
			'state',
			'-1'
		));
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
		// Get sorting variables
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'title'
		));
		$this->view->filters['sort_Dir']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		$this->view->filters['count'] = true;

		$this->view->total = $this->view->course->offerings($this->view->filters);

		$this->view->filters['count'] = false;

		$this->view->rows = $this->view->course->offerings($this->view->filters);

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
		$this->editTask();
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

		if (is_object($model))
		{
			$this->view->row = $model;
		}
		else
		{
			// Incoming
			$id = JRequest::getVar('id', array(0));

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			$this->view->row = CoursesModelOffering::getInstance($id);
		}

		if (!$this->view->row->get('course_id'))
		{
			$this->view->row->set('course_id', JRequest::getInt('course', 0));
		}

		$this->view->course = CoursesModelCourse::getInstance($this->view->row->get('course_id'));
		$this->view->config = $this->config;

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
	 * Save a course and fall through to edit view
	 *
	 * @return void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Saves changes to a course or saves a new entry if creating
	 *
	 * @return void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');

		// Instantiate a Course object
		$model = CoursesModelOffering::getInstance($fields['id']);

		if (!$model->bind($fields))
		{
			$this->addComponentMessage($model->getError());
			$this->editTask($model);
			return;
		}

		$p = new JRegistry('');
		$p->loadArray(JRequest::getVar('params', '', 'post'));

		// Make sure the logo gets carried over
		$op = new JRegistry($model->get('params'));
		$p->set('logo', $op->get('logo'));

		$model->set('params', $p->toString());

		if (!$model->store(true))
		{
			$this->addComponentMessage($model->getError());
			$this->editTask($model);
			return;
		}

		if ($redirect)
		{
			// Output messsage and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . $model->get('course_id'),
				JText::_('COM_COURSES_ITEM_SAVED')
			);
			return;
		}

		$this->editTask($model);
	}

	/**
	 * Copy an entry and all associated data
	 *
	 * @return	void
	 */
	public function copyTask()
	{
		// Incoming
		$id = JRequest::getVar('id', 0);

		// Get the single ID we're working with
		if (is_array($id))
		{
			$id = (!empty($id)) ? $id[0] : 0;
		}

		if (!$id)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . JRequest::getInt('course', 0),
				JText::_('COM_COURSES_ERROR_NO_ID'),
				'error'
			);
			return;
		}

		$offering = CoursesModelOffering::getInstance($id);
		if (!$offering->copy())
		{
			// Redirect back to the courses page
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . $offering->get('course_id'),
				JText::_('COM_COURSES_ERROR_COPY_FAILED') . ': ' . $offering->getError(),
				'error'
			);
			return;
		}

		// Redirect back to the courses page
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . $offering->get('course_id'),
			JText::_('COM_COURSES_ITEM_COPIED')
		);
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
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$num = 0;

		// Do we have any IDs?
		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				// Load the course page
				$model = CoursesModelOffering::getInstance($id);

				// Ensure we found the course info
				if (!$model->exists())
				{
					continue;
				}

				// Delete course
				if (!$model->delete())
				{
					JError::raiseError(500, JText::_('COM_COURSES_ERROR_UNABLE_TO_REMOVE_ENTRY'));
					return;
				}

				$num++;
			}
		}

		// Redirect back to the courses page
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . JRequest::getInt('course', 0),
			JText::sprintf('COM_COURSES_ITEMS_REMOVED', $num)
		);
	}

	/**
	 * Publish a course
	 *
	 * @return void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Unpublish a course
	 *
	 * @return void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Set the state of a course
	 *
	 * @return void
	 */
	public function stateTask($state=0)
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		$num = 0;
		if (!empty($ids))
		{
			//foreach course id passed in
			foreach ($ids as $id)
			{
				// Load the course page
				$model = CoursesModelOffering::getInstance($id);

				// Ensure we found the course info
				if (!$model->exists())
				{
					continue;
				}

				//set the course to be published and update
				$model->set('state', $state);
				if (!$model->store())
				{
					$this->setError(JText::sprintf('COM_COURSES_ERROR_UNABLE_TO_SET_STATE', $id));
					continue;
				}

				// Log the course approval
				$model->log($model->get('id'), 'offering', ($state ? 'published' : 'unpublished'));

				$num++;
			}
		}

		if ($this->getErrors())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . JRequest::getInt('course', 0),
				implode('<br />', $this->getErrors()),
				'error'
			);
		}
		else
		{
			// Output messsage and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . JRequest::getInt('course', 0),
				($state ? JText::sprintf('COM_COURSES_ITEMS_PUBLISHED', $num) : JText::sprintf('COM_COURSES_ITEMS_UNPUBLISHED', $num))
			);
		}
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . JRequest::getInt('course', 0)
		);
	}
}
