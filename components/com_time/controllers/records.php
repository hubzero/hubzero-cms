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
 * Records controller for time component
 */
class TimeControllerRecords extends TimeControllerBase
{
	/**
	 * Default view function
	 *
	 * @return void
	 */
	public function displayTask()
	{
		$filters = TimeFilters::getFilters("{$this->_option}.{$this->_controller}");
		$records = Record::all();

		// Take filters and apply them to the tasks
		if ($filters['search'])
		{
			foreach ($filters['search'] as $term)
			{
				$records->where('description', 'LIKE', "%{$term}%", 'and', 1);
				$records->orWhereRelatedHas('task', function($task) use ($term)
				{
					$task->where('name', 'LIKE', "%{$term}%");
				}, 1);
			}
		}
		if ($filters['q'])
		{
			foreach ($filters['q'] as $q)
			{
				$records->where($q['column'], $q['o'], $q['value']);
			}
		}

		// Display
		$this->view->filters = $filters;
		$this->view->records = $records->paginated()->ordered()->including('task', 'user');
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
	public function editTask($record=null)
	{
		if (!isset($record) || !is_object($record))
		{
			$record = Record::oneOrNew(JRequest::getInt('id'));
		}

		// Only allow creator of the record to edit or a proxy of the user
		if (!$record->isNew() && !$record->isMine() && !$record->iCanProxy())
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_($this->base),
				JText::_('COM_TIME_RECORDS_WARNING_CANT_EDIT_OTHER'),
				'warning'
			);
			return;
		}

		// Display
		$this->view->start = $this->start($record);
		$this->view->row   = $record;
		$this->view->display();
	}

	/**
	 * Readonly view of single record
	 *
	 * @return void
	 */
	public function readonlyTask()
	{
		// Display
		$this->view->row   = Record::oneOrFail(JRequest::getInt('id'));
		$this->view->start = $this->start($this->view->row);
		$this->view->display();
	}

	/**
	 * Save new time record and redirect to the records page
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Create object
		$record = Record::oneOrNew(JRequest::getInt('id'))->set(array(
			'task_id'     => JRequest::getInt('task_id'),
			'user_id'     => JRequest::getInt('user_id'),
			'time'        => JRequest::getInt('htime') . '.' . JRequest::getInt('mtime'),
			'date'        => JFactory::getDate(JRequest::getVar('date'), JFactory::getConfig()->get('offset'))->toSql(),
			'description' => JRequest::getVar('description')
		));

		// Set end based on start + time length
		$record->set('end', date('Y-m-d H:i:s', (strtotime($record->date) + ($record->time*3600))));

		// Only create records for yourself or your proxies
		if (!$record->isMine() && !$record->iCanProxy())
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_($this->base),
				JText::_('COM_TIME_RECORDS_WARNING_CANT_EDIT_OTHER'),
				'warning'
			);
			return;
		}

		if (!$record->save())
		{
			// Something went wrong...return errors
			foreach ($record->getErrors() as $error)
			{
				$this->view->setError($error);
			}

			$this->view->setLayout('edit');
			$this->view->task = 'edit';
			$this->editTask($record);
			return;
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_($this->base . $this->start($record)),
			JText::_('COM_TIME_RECORDS_SAVE_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Delete records
	 *
	 * @return void
	 */
	public function deleteTask()
	{
		$record = Record::oneOrFail(JRequest::getInt('id'));

		// Only allow creator of the record to edit or delete
		if (!$record->isMine())
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_($this->base),
				JText::_('COM_TIME_RECORDS_WARNING_CANT_DELETE_OTHER'),
				'warning'
			);
			return;
		}

		// Delete record
		$record->destroy();

		// Set the redirect
		$this->setRedirect(
			JRoute::_($this->base . $this->start($record)),
			JText::_('COM_TIME_RECORDS_DELETE_SUCCESSFUL'),
			'passed'
		);
	}
}