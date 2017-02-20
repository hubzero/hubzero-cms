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
 * @author    Sam Wilson <samwilson@purdue.edu
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Time\Site\Controllers;

use Components\Time\Helpers\Filters;
use Components\Time\Models\Task;
use Request;
use Route;
use Lang;
use App;

/**
 * Tasks controller for time component
 */
class Tasks extends Base
{
	/**
	 * Default view function
	 *
	 * @return void
	 */
	public function displayTask()
	{
		$filters = Filters::getFilters("{$this->_option}.{$this->_controller}");
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
			$task = Task::oneOrNew(Request::getInt('id'));
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
		$task = Task::oneOrNew(Request::getInt('id'))->set(array(
			'name'        => Request::getVar('name'),
			'hub_id'      => Request::getInt('hub_id'),
			'start_date'  => Request::getVar('start_date'),
			'end_date'    => Request::getVar('end_date'),
			'active'      => Request::getInt('active'),
			'description' => Request::getVar('description'),
			'priority'    => Request::getInt('priority'),
			'assignee_id' => Request::getInt('assignee_id'),
			'liaison_id'  => Request::getInt('liaison_id'),
			'billable'    => Request::getInt('billable')
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
		App::redirect(
			Route::url($this->base . $this->start($task)),
			Lang::txt('COM_TIME_TASKS_SAVE_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Merge task
	 *
	 * @return void
	 */
	public function mergeTask()
	{
		$ids = (array)Request::getVar('ids');

		if (!$primary = Request::getInt('primary'))
		{
			// Set the redirect
			App::redirect(
				Route::url($this->base . $this->start(Task::blank())),
				Lang::txt('COM_TIME_TASKS_MERGE_NO_PRIMARY'),
				'error'
			);
			return;
		}

		// Loop through the tasks given
		foreach ($ids as $id)
		{
			// Leave the primary task alone
			if ($id == $primary)
			{
				continue;
			}

			// Get all the records for the given task and update their task id
			$task = Task::oneOrFail((int)$id);
			foreach ($task->records as $record)
			{
				$record->set('task_id', $primary)->save();
			}

			$task->destroy();
		}

		// Set the redirect
		App::redirect(
			Route::url($this->base . $this->start($task)),
			Lang::txt('COM_TIME_TASKS_MERGE_SUCCESSFUL'),
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
		$ids = (array)Request::getVar('id');
		$failed = false;

		foreach ($ids as $id)
		{
			$task = Task::oneOrFail((int)$id);

			// If there are active records, don't allow deletion
			if ($task->records->count())
			{
				$failed = true;
			}
			else
			{
				// Delete the task
				$task->destroy();
			}
		}

		// If we only have one id, go to that tasks edit page
		// Otherwise, we go back to the tasks display
		if ($failed && count($ids) == 1)
		{
			App::redirect(
				Route::url($this->base . '&task=edit&id=' . Request::getInt('id')),
				Lang::txt('COM_TIME_TASK_DELETE_HAS_ASSOCIATED_RECORDS'),
				'warning'
			);
		}
		else if ($failed)
		{
			// Set the redirect
			App::redirect(
				Route::url($this->base . $this->start($task)),
				Lang::txt('COM_TIME_TASK_DELETE_MULTIPLE_HAS_ASSOCIATED_RECORDS'),
				'warning'
			);
		}
		else
		{
			// Set the redirect
			App::redirect(
				Route::url($this->base . $this->start($task)),
				Lang::txt('COM_TIME_TASKS_DELETE_SUCCESSFUL'),
				'passed'
			);
		}
	}

	/**
	 * Toggle a task's active status
	 *
	 * @return void
	 */
	public function toggleActiveTask()
	{
		$task = Task::oneOrFail(Request::getInt('id'));

		$task->set('active', ($task->active == 0) ? 1 : 0);

		if (!$task->save())
		{
			App::abort(500, implode('<br />', $task->getErrors()));
			return;
		}

		// Set the redirect
		App::redirect(
			Route::url($this->base . $this->start($task)),
			Lang::txt('COM_TIME_TASKS_ACTIVE_STATUS_CHANGED'),
			'passed'
		);
	}
}
