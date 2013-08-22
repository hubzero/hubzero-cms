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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'unit.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

/**
 * Courses controller class for managing membership and course info
 */
class CoursesControllerUnits extends Hubzero_Controller
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
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		$this->view->filters['count'] = true;

		$this->view->total = $this->view->offering->units($this->view->filters);

		$this->view->filters['count'] = false;

		$this->view->rows = $this->view->offering->units($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		$this->_getStyles();

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

			$this->view->row = CoursesModelUnit::getInstance($id);
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
	 * Saves data to the database and return to the editing form
	 *
	 * @return    void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Saves data to the database
	 *
	 * @param     $redirect boolean Redirect after saving?
	 * @return    void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');

		// Instantiate an Hubzero_Course object
		$model = CoursesModelUnit::getInstance($fields['id']);

		if (!$model->bind($fields))
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		if (!$model->store(true))
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		if ($model->get('id') && $model->assetgroups()->total() <= 0)
		{
			$asset_groups = explode(',', $this->config->getValue('default_asset_groups', 'Lectures, Homework, Exam'));
			array_map('trim', $asset_groups);

			foreach ($asset_groups as $key)
			{
				// Get our asset group object
				$assetGroup = new CoursesModelAssetgroup(null);
				$assetGroup->set('title', $key);
				$assetGroup->set('unit_id', $model->get('id'));
				$assetGroup->set('parent', 0);

				// Save the asset group
				if (!$assetGroup->store(true))
				{
					$this->addComponentMessage($model->getError(), 'error');
				}
			}
		}

		if ($redirect)
		{
			// Output messsage and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . JRequest::getInt('offering', 0),
				JText::_('COM_COURSES_UNIT_SAVED')
			);
		}

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
				$model = CoursesModelUnit::getInstance($id);

				// Ensure we found the course info
				if (!$model->exists())
				{
					continue;
				}

				// Delete course
				if (!$model->delete())
				{
					JError::raiseError(500, JText::_('Unable to delete unit'));
					return;
				}

				// Log the course approval
				$log = new CoursesTableLog($this->database);
				$log->scope_id  = $id;
				$log->scope     = 'course_unit';
				$log->user_id   = $this->juser->get('id');
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action    = 'unit_deleted';
				$log->actor_id  = $this->juser->get('id');
				if (!$log->store())
				{
					$this->setError($log->getError());
				}

				$num++;
			}
		}

		// Redirect back to the courses page
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . JRequest::getInt('offering', 0),
			JText::sprintf('%s Item(s) removed.', $num)
		);
	}

	/**
	 * Calls stateTask to publish entries
	 * 
	 * @return     void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}
	
	/**
	 * Calls stateTask to unpublish entries
	 * 
	 * @return     void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Set the state of an entry
	 * 
	 * @param      integer $state State to set
	 * @return     void
	 */
	public function stateTask($state=0)
	{
		// Incoming
		$ids = JRequest::getVar('id', array(0));
		if (!is_array($ids)) 
		{
			$ids = array(0);
		}

		// Check for an ID
		if (count($ids) < 1) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . JRequest::getInt('offering', 0),
				($state == 1 ? JText::_('COM_COURSES_SELECT_PUBLISH') : JText::_('COM_COURSES_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating a category
			$row = new CoursesModelUnit($id);
			$row->set('state', $state);
			$row->store();
		}

		// Set message
		switch ($state)
		{
			case '-1': 
				$message = JText::sprintf('COM_COURSES_ARCHIVED', count($ids));
			break;
			case '1':
				$message = JText::sprintf('COM_COURSES_PUBLISHED', count($ids));
			break;
			case '0':
				$message = JText::sprintf('COM_COURSES_UNPUBLISHED', count($ids));
			break;
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . JRequest::getInt('offering', 0),
			$message
		);
	}

	/**
	 * Reorder a record up
	 * 
	 * @return     void
	 */
	public function orderupTask()
	{
		$this->orderTask();
	}

	/**
	 * Reorder a record up
	 * 
	 * @return     void
	 */
	public function orderdownTask()
	{
		$this->orderTask();
	}

	/**
	 * Reorder a plugin
	 * 
	 * @param      integer $access Access level to set
	 * @return     void
	 */
	public function orderTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$id = JRequest::getVar('id', array(0), 'post', 'array');
		JArrayHelper::toInteger($id, array(0));

		$uid = $id[0];
		$inc = ($this->_task == 'orderup' ? -1 : 1);

		$row = new CoursesTableUnit($this->database);
		$row->load($uid);
		$row->move($inc, 'offering_id=' . $this->database->Quote($row->offering_id));

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . JRequest::getInt('offering', 0)
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&offering=' . JRequest::getInt('offering', 0)
		);
	}
}
