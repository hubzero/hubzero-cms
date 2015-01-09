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
		$filters = TimeFilters::getFilters("{$this->_option}.{$this->_controller}");
		$tasks   = Task::all();

		// Take filters and apply them to the tasks
		if ($filters['search'])
		{
			foreach ($filters['search'] as $term)
			{
				$tasks->where('name', 'LIKE', "%{$term}%");
			}
		}
		if ($filters['q'])
		{
			foreach ($filters['q'] as $q)
			{
				$tasks->where($q['column'], $q['o'], $q['value']);
			}
		}

		// Display
		$this->view->filters = $filters;
		$this->view->tasks   = $tasks->paginated()->ordered()->including('liaison', 'assignee', 'hub');
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
		if (!isset($task) || !is_object($task))
		{
			$task = Task::oneOrNew(JRequest::getInt('id'));
		}

		// Display
		$this->view->row    = $task;
		$this->view->config = $this->config;
		$this->view->start  = $this->start($task);
		$this->view->display();
	}

	/**
	 * Save new time task and redirect to the tasks page
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Create object
		$task = Task::oneOrNew(JRequest::getInt('id'))->set(array(
			'name'        => JRequest::getVar('name'),
			'hub_id'      => JRequest::getInt('hub_id'),
			'start_date'  => JRequest::getVar('start_date'),
			'end_date'    => JRequest::getVar('end_date'),
			'active'      => JRequest::getInt('active'),
			'description' => JRequest::getVar('description'),
			'priority'    => JRequest::getInt('priority'),
			'assignee_id' => JRequest::getInt('assignee_id'),
			'liaison_id'  => JRequest::getInt('liaison_id')
		));

		// Save the posted array
		if (!$task->save())
		{
			// Something went wrong...return errors
			foreach ($task->getErrors() as $error)
			{
				$this->view->setError($error);
			}

			$this->view->setLayout('edit');
			$this->view->task = 'edit';
			$this->editTask($task);
			return;
		}

		// Success, we made it, set the redirect
		$this->setRedirect(
			JRoute::_($this->base . $this->start($task)),
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
		$task = Task::oneOrFail(JRequest::getInt('id'));

		// If there are active records, don't allow deletion
		if ($task->records->count())
		{
			$this->setRedirect(
				JRoute::_($this->base . '&task=edit&id=' . JRequest::getInt('id')),
				JText::_('COM_TIME_TASK_DELETE_HAS_ASSOCIATED_RECORDS'),
				'warning'
			);
			return;
		}

		// Delete the task
		$task->destroy();

		// Set the redirect
		$this->setRedirect(
			JRoute::_($this->base . $this->start($task)),
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
		$task = Task::oneOrFail(JRequest::getInt('id'));

		$task->set('active', ($task->active == 0) ? 1 : 0);

		if (!$task->save())
		{
			JError::raiseError(500, implode('<br />', $task->getErrors()));
			return;
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_($this->base . $this->start($task)),
			JText::_('COM_TIME_TASKS_ACTIVE_STATUS_CHANGED'),
			'passed'
		);
	}
}