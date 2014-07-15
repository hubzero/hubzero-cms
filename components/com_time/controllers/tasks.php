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
 * @author    Sam Wilson <samwilson@purdue.edu
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Tasks controller for time component
 */
class TimeControllerTasks extends TimeControllerBase
{
	/**
	 * Default view function
	 *
	 * @return void
	 */
	public function displayTask()
	{
		// Instantiate tasks class
		$tasks = new TimeTasks($this->database);
		$hub   = new TimeHubs($this->database);

		// Set filters for view
		$this->view->filters = TimeFilters::getFilters($this->_option, $this->_controller);

		// Get the total number of tasks (for pagination)
		$this->view->total = $tasks->getCount($this->view->filters);

		// Setup pagination
		$this->view->pageNav = TimeFilters::getPagination($this->view->total, $this->view->filters['start'], $this->view->filters['limit']);

		// Get the tasks
		$this->view->tasks = $tasks->getTasks($this->view->filters);
		$this->view->tasks = TimeFilters::highlight($this->view->tasks, $this->view->filters);

		// Get the column list and operators
		$this->view->cols      = TimeFilters::getColumnNames('time_tasks', array("id", "description"));
		$this->view->operators = TimeFilters::buildSelectOperators();

		// Set a few things for the vew
		$this->_buildPathway();
		$this->view->title = $this->_buildTitle();

		if (isset($this->view->filters['error']))
		{
			$this->setError($this->view->filters['error']);
		}

		// Set a few things for the vew
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Display
		$this->view->display();
	}

	/**
	 * New task
	 *
	 * @return void
	 */
	public function newTask()
	{
		$this->view->setLayout('edit');
		$this->view->task = 'edit';
		$this->editTask();
	}

	/**
	 * New/Edit function
	 *
	 * @return void
	 */
	public function editTask($task=null)
	{
		// Get the id if we're editing a task
		$pid = JRequest::getInt('id');
		$app = JFactory::getApplication();

		// Incoming
		if (isset($task) && is_object($task))
		{
			// Use the prexisting object (i.e. we had an error when saving)
			$this->view->row = $task;
		}
		else
		{
			// Create a new object (i.e. we're coming in clean)
			$task = new TimeTasks($this->database);
			$task->load($pid);
			$this->view->row = $task;
		}

		// Build the hubs list, priority list, assignee list, and liaison list
		$this->view->hlist         = TimeHTML::buildHubsList($this->_controller, $this->view->row->hub_id, 1);
		$this->view->priority_list = TimeHTML::buildPriorityList($this->view->row->priority, $this->_controller);
		$this->view->alist         = TimeHTML::buildUserList($this->view->row->assignee, $this->_controller, 2);
		$this->view->llist         = TimeHTML::buildLiaisonList($this->view->row->liaison, $this->_controller, 1);

		// If viewing an entry from a page other than the first, take the user back to that page if they click "all xxx"
		$this->view->start = ($app->getUserState("{$this->_option}.{$this->_controller}.start") != 0)
			? '&start='.$app->getUserState("{$this->_option}.{$this->_controller}.start")
			: '';

		// Set a few things for the vew
		$this->_buildPathway();
		$this->view->title = $this->_buildTitle();

		// Set a few things for the vew
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Display
		$this->view->display();
	}

	/**
	 * Save new time task and redirect to the tasks page
	 *
	 * @return void
	 */
	public function saveTask()
	{
		$task = JRequest::getVar('tasks', array(), 'post');
		$task = array_map('trim', $task);

		// Create object
		$tasks = new TimeTasks($this->database);

		// Save the posted array
		if (!$tasks->save($task))
		{
			// Something went wrong...return errors to view
			$this->setError($tasks->getError());
			$this->view->setLayout('edit');
			$this->view->task = 'edit';
			$this->editTask($tasks);
			return;
		}

		$app = JFactory::getApplication();

		// If saving a task from a page other than the first, take the user back to that page after saving
		$start = ($app->getUserState("{$this->_option}.{$this->_controller}.start") != 0)
			? '&start='.$app->getUserState("{$this->_option}.{$this->_controller}.start")
			: '';

		// Success, we made it, set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . $start),
			JText::_('COM_TIME_TASKS_SAVE_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Delete task
	 *
	 * @return void
	 */
	public function deleteTask()
	{
		// Incoming posted data
		$tid = JRequest::getInt('id');
		$app = JFactory::getApplication();

		// If deleting a record from a page other than the first, take the user back to that page after deletion
		$start = ($app->getUserState("{$this->_option}.{$this->_controller}.start") != 0)
			? '&start='.$app->getUserState("{$this->_option}.{$this->_controller}.start")
			: '';

		// Check if the task has any active records
		$records = new TimeRecords($this->database);
		$count   = $records->getCount($filters = array('task'=>$tid));

		// If there are active records, don't allow deletion
		if ($count > 0)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=tasks&task=edit&id=' . $tid),
				JText::_('COM_TIME_TASK_DELETE_HAS_ASSOCIATED_RECORDS'),
				'warning'
			);
			return;
		}

		// Create object and load by task id
		$tasks = new TimeTasks($this->database);
		$tasks->load($tid);

		// Delete the task
		$tasks->delete();

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . $start),
			JText::_('COM_TIME_TASKS_DELETE_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Toggle a task's active status
	 *
	 * @return void
	 */
	public function toggleActiveTask()
	{
		// Incoming posted data
		$pid = JRequest::getInt('id');
		$app = JFactory::getApplication();

		// If delete a record from a page other than the first, take the user back to that page after toggle
		$start = ($app->getUserState("{$this->_option}.{$this->_controller}.start") != 0)
			? '&start='.$app->getUserState("{$this->_option}.{$this->_controller}.start")
			: '';

		// Create object and store new content
		$task = new TimeTasks($this->database);
		$task->load($pid);
		$active = ($task->active == 0) ? 1 : 0;

		$task->active = $active;
		if (!$task->store())
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . $start),
			JText::_('COM_TIME_TASKS_ACTIVE_STATUS_CHANGED'),
			'passed'
		);
	}
}