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
		// Instantiate records class
		$records = new TimeRecords($this->database);

		// Set filters for view
		$this->view->filters = TimeFilters::getFilters($this->_option, $this->_controller);

		// Get the total number of records (for pagination)
		$this->view->total = $records->getCount($this->view->filters);

		// Setup pagination
		$this->view->pageNav = TimeFilters::getPagination($this->view->total, $this->view->filters['start'], $this->view->filters['limit']);

		// Get the records
		$this->view->records = $records->getRecords($this->view->filters);

		// Get suborinates of current user
		$juser = JFactory::getUser();
		$this->view->subordinates = TimeHTML::getSubordinates($juser->get('id'));

		// Get the column list and operators
		$this->view->cols      = TimeFilters::getColumnNames('time_records', array("id", "description"));
		$this->view->operators = TimeFilters::buildSelectOperators();

		// Set a few things for the vew
		$this->_buildPathway();
		$this->view->title = $this->_buildTitle();

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
	public function editTask($record=null)
	{
		// Get the id if we're editing a record
		$rid = JRequest::getInt('id');

		// Incoming
		if (isset($record) && is_object($record))
		{
			// Use the prexisting object (i.e. we had an error when saving)
			$this->view->row = $record;
		}
		else
		{
			// Create a new object (i.e. we're coming in clean)
			$record = new TimeRecords($this->database);
			$this->view->row = $record->getRecord($rid);

			// Prepopulate the task passed in URL if it's given
			if ($task = JRequest::getInt('task', NULL))
			{
				$this->view->row->task_id = $task;
			}
		}

		$juser = JFactory::getUser();
		$app   = JFactory::getApplication();

		// Get suborinates of current user
		$subordinates = TimeHTML::getSubordinates($juser->get('id'));

		// Only allow creator of the record to edit or delete or the manager of the user
		if (!empty($this->view->row->id) && ($this->view->row->user_id != $juser->get('id') && !in_array($this->view->row->user_id, $subordinates)))
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				JText::_('COM_TIME_RECORDS_WARNING_CANT_EDIT_OTHER'),
				'warning'
			);
		}

		// Explode the time
		if (strstr($this->view->row->time, '.') !== false)
		{
			list($hrs, $mins) = explode(".", $this->view->row->time);
		}
		else
		{
			$hrs = $this->view->row->time;
			$mins = 0;
		}

		// Build select lists for edit page
		$this->view->htimelist = TimeHTML::buildTimeListHours($hrs);
		$this->view->mtimelist = TimeHTML::buildTimeListMins($mins);
		$this->view->hubslist  = TimeHTML::buildHubsList($this->_controller, $this->view->row->hid);
		$this->view->tasklist  = TimeHTML::buildTasksList($this->view->row->task_id, $this->_controller, $this->view->row->hid, $this->view->row->pactive);

		// Build subordinates list if applicable
		if (isset($subordinates) && !empty($subordinates))
		{
			$this->view->subordinates = TimeHTML::buildSubordinatesList((isset($this->view->row->user_id) ? $this->view->row->user_id : 0), $subordinates);
		}

		// Is this a new record?
		if (empty($this->view->row->user_id))
		{
			// Set some defaults
			$this->view->row->user_id = $juser->get('id');
			$this->view->row->uname   = $juser->get('name');
			$this->view->row->date    = JFactory::getDate()->format('Y-m-d H:00');
		}

		// If viewing a record from a page other than the first, take the user back to that page if they click "all records"
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
	 * Readonly view of single record
	 *
	 * @return void
	 */
	public function readonlyTask()
	{
		// Get the id if we're editing a record
		$rid = JRequest::getInt('id');
		$app = JFactory::getApplication();

		// Instantiate classes
		$record = new TimeRecords($this->database);
		$juser  = JFactory::getUser();

		// Get suborinates of current user
		$this->view->subordinates = TimeHTML::getSubordinates($juser->get('id'));

		// Get the records for time and pass them to the view
		$this->view->row = $record->getRecord($rid);

		// If viewing a record from a page other than the first, take the user back to that page if they click "all records"
		$this->view->start = ($app->getUserState("{$this->_option}.{$this->_controller}.start") != 0)
			? '&start='.$app->getUserState("{$this->_option}.{$this->_controller}.start")
			: '';

		// Set a few things for the vew
		$this->_buildPathway();
		$this->view->title = $this->_buildTitle();

		// Display
		$this->view->display();
	}

	/**
	 * Save new time record and redirect to the records page
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Incoming posted data
		$record = JRequest::getVar('records', array(), 'post');
		$record = array_map('trim', $record);
		$juser  = JFactory::getUser();
		$app    = JFactory::getApplication();

		// Get suborinates of current user
		$subordinates = TimeHTML::getSubordinates($juser->get('id'));

		// Only create records for yourself or your subordinates
		if ($record['user_id'] != $juser->get('id') && !in_array($record['user_id'], $subordinates))
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				JText::_('COM_TIME_RECORDS_WARNING_CANT_EDIT_OTHER'),
				'warning'
			);
		}

		// Combine the time entry
		$record['time'] = $record['htime'] . '.' . $record['mtime'];
		$record['date'] = JFactory::getDate($record['date'], JFactory::getConfig()->get('offset'))->toSql();

		// Create object and store new content
		$records = new TimeRecords($this->database);

		if (!$records->save($record))
		{
			// Add a few things to the records object to pass back to the edit view
			$records->hid     = '';
			$records->pactive = '';
			$records->uname   = $juser->get('name');

			$this->setError($records->getError());
			$this->view->setLayout('edit');
			$this->view->task = 'edit';
			$this->editTask($records);
			return;
		}

		// If saving a record from a page other than the first, take the user back to that page after saving
		$start = ($app->getUserState("{$this->_option}.{$this->_controller}.start") != 0)
			? '&start='.$app->getUserState("{$this->_option}.{$this->_controller}.start")
			: '';

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . $start),
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
		// Incoming posted data
		$record = JRequest::getInt('id');
		$juser  = JFactory::getUser();
		$app    = JFactory::getApplication();

		// If delete a record from a page other than the first, take the user back to that page after deletion
		$start = ($app->getUserState("{$this->_option}.{$this->_controller}.start") != 0)
			? '&start='.$app->getUserState("{$this->_option}.{$this->_controller}.start")
			: '';

		// Create object and store new content
		$records = new TimeRecords($this->database);
		$records->load($record);

		// Only allow creator of the record to edit or delete
		if ($records->user_id != $juser->get('id'))
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				JText::_('COM_TIME_RECORDS_WARNING_CANT_DELETE_OTHER'),
				'warning'
			);
			return;
		}

		// Delete record
		$records->delete();

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . $start),
			JText::_('COM_TIME_RECORDS_DELETE_SUCCESSFUL'),
			'passed'
		);
	}
}