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

// Course model pulls in other classes we need
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

/**
 * Courses controller class for managing course pages
 */
class CoursesControllerPages extends Hubzero_Controller
{
	/**
	 * Manage course pages
	 *
	 * @return void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
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

		$this->view->filters['offering']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.offering',
			'offering',
			0
		);

		$this->view->offering = CoursesModelOffering::getInstance($this->view->filters['offering']);
		if ($this->view->offering->exists())
		{
			$this->view->filters['course']    = $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.course',
				'course',
				$this->view->offering->get('course_id')
			);
		}
		else
		{
			$using = 'course';
			$this->view->filters['course']    = $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.course',
				'course',
				0
			);
		}

		$this->view->course = CoursesModelCourse::getInstance($this->view->filters['course']);
		if (!$this->view->course->exists())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=courses'
			);
			return;
		}

		if ($this->view->offering->exists())
		{
			$list = $this->view->offering->pages();
		}
		else
		{
			
			$list = $this->view->course->pages();
		}

		$this->view->total = count($list);

		$this->view->rows = array_slice($list, $this->view->filters['start'], $this->view->filters['limit']);

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
	 * Create a course page
	 *
	 * @return void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a course page
	 *
	 * @return void
	 */
	public function editTask($model = null)
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

			$this->view->row = new CoursesTablePage($this->database);
			$this->view->row->load($id);
		}

		if (!$this->view->row->get('course_id'))
		{
			$this->view->row->set('course_id', JRequest::getInt('course', 0));
		}
		if (!$this->view->row->get('offering_id'))
		{
			$this->view->row->set('offering_id', JRequest::getInt('offering', 0));
		}

		$this->view->course   = CoursesModelCourse::getInstance($this->view->row->get('course_id'));
		$this->view->offering = CoursesModelOffering::getInstance($this->view->row->get('offering_id'));

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
	 * Save a course page and fall through to edit view
	 *
	 * @return void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save a course page
	 *
	 * @return void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// load the request vars
		$fields = JRequest::getVar('fields', array(), 'post');

		// instatiate course page object for saving
		$row = new CoursesTablePage($this->database);

		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($redirect)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . $fields['course_id'] . '&offering=' . $fields['offering_id'],
				JText::_('Page successfully saved')
			);
			return;
		}

		$this->editTask($row);
	}

	/**
	 * Remove one or more types
	 * 
	 * @return     void Redirects back to main listing
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming (expecting an array)
		$ids = JRequest::getVar('id', array());
		$rtrn = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . JRequest::getInt('course', 0) . '&offering=' . JRequest::getInt('offering', 0);

		// Ensure we have an ID to work with
		if (empty($ids))
		{
			// Redirect with error message
			$this->setRedirect(
				$rtrn,
				JText::_('No page selected'),
				'error'
			);
			return;
		}

		$tbl = new CoursesTablePage($this->database);

		$i = 0;
		foreach ($ids as $id)
		{
			// Delete the type
			if (!$tbl->delete($id))
			{
				$this->setError($tbl->getError());
			}
			else
			{
				$i++;
			}
		}

		// Redirect
		if ($i)
		{
			$this->setRedirect(
				$rtrn,
				JText::sprintf('%s Page(s) successfully removed', $i)
			);
			return;
		}

		$this->setRedirect(
			$rtrn,
			$this->getError(),
			'error'
		);
	}

	/**
	 * Cancel a course page task
	 *
	 * @return void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . JRequest::getInt('course', 0) . '&offering=' . JRequest::getInt('offering', 0)
		);
	}
}
