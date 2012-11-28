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

require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'page.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');
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
		$this->view->filters['offering']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.offering',
			'offering',
			0
		);

		$this->view->offering = CoursesModelOffering::getInstance($this->view->filters['offering']);
		if (!$this->view->offering->exists())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=courses'
			);
			return;
		}
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

		$list = $this->view->offering->pages();

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

		if (!$this->view->row->get('offering_id'))
		{
			$this->view->row->set('offering_id', JRequest::getInt('offering', 0));
		}

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
	 * Save a course page
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// load the request vars
		$fields = JRequest::getVar('fields', array(), 'post');

		// Load the course page
		//$offering = CoursesModelOffering::getInstance($fields['offering_id']);

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

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . $fields['offering_id']
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
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . JRequest::getVar('offering', '')
		);
	}
}
