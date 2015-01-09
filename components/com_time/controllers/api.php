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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JLoader::import('Hubzero.Api.Controller');

/**
 * API controller for the time component
 */
class TimeControllerApi extends \Hubzero\Component\ApiController
{
	/**
	 * Execute!
	 *
	 * @return void
	 */
	function execute()
	{
		// Import some Joomla libraries
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		// Get the request type
		$this->format = JRequest::getVar('format', 'application/json');

		// Get a database object
		$this->db = JFactory::getDBO();

		// Import time JTable libraries
		require_once JPATH_ROOT . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'hub.php';
		require_once JPATH_ROOT . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'task.php';
		require_once JPATH_ROOT . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'record.php';
		require_once JPATH_ROOT . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'contact.php';
		require_once JPATH_ROOT . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'permissions.php';
		require_once JPATH_ROOT . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'proxy.php';
		require_once JPATH_ROOT . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'liaison.php';

		// Switch based on task (i.e. "/api/time/xxxxx")
		switch ($this->segments[0])
		{
			// Records
			case 'indexRecords':             $this->indexRecords();             break;
			case 'saveRecord':               $this->saveRecord();               break;
			// Tasks
			case 'indexTasks':               $this->indexTasks();               break;
			// Hubs
			case 'indexHubs':                $this->indexHubs();                break;
			case 'showHub':                  $this->showHub();                  break;
			// Miscellaneous
			case 'saveContact':              $this->saveContact();              break;
			case 'indexTimeUsers':           $this->indexTimeUsers();           break;
			case 'getValues':                $this->getValues();                break;
			// Overview page methods (will be combined with records methods at some point)
			case 'today':                    $this->today();                    break;
			case 'week':                     $this->week();                     break;
			case 'postRecord':               $this->postRecord();               break;

			default:                         $this->method_not_found();         break;
		}
	}

	//--------------------------
	// Records functions
	//--------------------------

	/**
	 * Get time records
	 *
	 * @return void
	 */
	private function indexRecords()
	{
		// Set message format
		$this->setMessageType($this->format);

		// Require authorization
		if (!$this->authorize())
		{
			$this->setMessage('', 401, 'Unauthorized');
			return;
		}

		$record = Record::all();

		if ($task_id = JRequest::getInt('tid', false))
		{
			$record->whereEquals('task_id', $task_id);
		}
		if ($start_date = JRequest::getVar('startdate', false))
		{
			$record->where('date', '>=', $start_date);
		}
		if ($end_date = JRequest::getVar('enddate', false))
		{
			$record->where('date', '<=', $end_date);
		}
		if ($limit = JRequest::getInt('limit', 20))
		{
			$record->limit($limit);
		}
		if ($start = JRequest::getInt('start', 0))
		{
			$record->start($start);
		}
		if (($orderby  = JRequest::getCmd('orderby', 'id'))
		 && ($orderdir = JRequest::getCmd('orderdir', 'asc')))
		{
			$record->order($orderby, $orderdir);
		}

		// Create object with records property
		$obj = new stdClass();
		$obj->records = $record->rows()->toObject();

		// Return object
		$this->setMessage($obj, 200, 'OK');
	}

	/**
	 * Save a time record
	 *
	 * @return void
	 */
	private function saveRecord()
	{
		// Set message format
		$this->setMessageType($this->format);

		// Require authorization
		if (!$this->authorize())
		{
			$this->setMessage('', 401, 'Unauthorized');
			return;
		}

		// Incoming posted data (grab individually for added security)
		$r = [];
		$r['task_id']     = JRequest::getInt('task_id');
		$r['date']        = JFactory::getDate(JRequest::getVar('date'))->toSql();
		$r['description'] = JRequest::getVar('description');
		$r['time']        = JRequest::getVar('time');
		$r['user_id']     = JFactory::getApplication()->getAuthn('user_id');
		$r['end']         = date('Y-m-d H:i:s', (strtotime($r['date']) + ($r['time']*3600)));

		// Create object and store content
		$record = Record::oneOrNew(JRequest::getInt('id'))->set($r);

		// Do the actual save
		if (!$record->save())
		{
			$this->setMessage('Record creation failed', 500, 'Internal server error');
			return;
		}

		// Return message
		$this->setMessage('Record successfully created', 201, 'Created');
	}

	//--------------------------
	// Tasks functions
	//--------------------------

	/**
	 * Get time tasks
	 *
	 * @return void
	 */
	private function indexTasks()
	{
		// Set message format
		$this->setMessageType($this->format);

		// Require authorization
		if (!$this->authorize())
		{
			$this->setMessage('', 401, 'Unauthorized');
			return;
		}

		$task = Task::all();

		if ($hub_id = JRequest::getInt('hid', false))
		{
			$task->whereEquals('hub_id', $hub_id);
		}
		if ($active = JRequest::getInt('pactive', false))
		{
			$task->whereEquals('active', $active);
		}
		if ($limit = JRequest::getInt('limit', 20))
		{
			$task->limit($limit);
		}
		if ($start = JRequest::getInt('start', 0))
		{
			$task->start($start);
		}
		if (($orderby  = JRequest::getCmd('orderby', 'name'))
		 && ($orderdir = JRequest::getCmd('orderdir', 'asc')))
		{
			$task->order($orderby, $orderdir);
		}

		// Create object with tasks property
		$obj = new stdClass();
		$obj->tasks = $task->rows()->toObject();

		// Return object
		$this->setMessage($obj, 200, 'OK');
	}

	//--------------------------
	// Hubs functions
	//--------------------------

	/**
	 * Get time hubs
	 *
	 * @return array of hubs objects
	 */
	private function indexHubs()
	{
		// Set message format
		$this->setMessageType($this->format);

		// Require authorization
		if (!$this->authorize())
		{
			$this->setMessage('', 401, 'Unauthorized');
			return;
		}

		$hub = Hub::all();

		if ($active = JRequest::getInt('active', 1))
		{
			$hub->whereEquals('active', $active);
		}
		if ($limit = JRequest::getInt('limit', 100))
		{
			$hub->limit($limit);
		}
		if ($start = JRequest::getInt('start', 0))
		{
			$hub->start($start);
		}
		if (($orderby  = JRequest::getCmd('orderby', 'id'))
		 && ($orderdir = JRequest::getCmd('orderdir', 'asc')))
		{
			$hub->order($orderby, $orderdir);
		}

		// Create object with tasks property
		$obj = new stdClass();
		$obj->hubs = $hub->rows()->toObject();

		// Return object
		$this->setMessage($obj, 200, 'OK');
	}

	/**
	 * Get single hub
	 *
	 * @return object - single hub instance
	 */
	private function showHub()
	{
		// Set message format
		$this->setMessageType($this->format);

		// Require authorization
		if (!$this->authorize())
		{
			$this->setMessage('', 401, 'Unauthorized');
			return;
		}

		// Incoming posted data
		$id = JRequest::getInt('id');

		// Error checking
		if (empty($id))
		{
			$this->setMessage('Missing id parameter', 422, 'Unprocessable entity');
			return;
		}

		try
		{
			$hub = Hub::oneOrFail($id);
		}
		catch (Hubzero\Error\Exception\RuntimeException $e)
		{
			$this->setMessage('Hub not found', 404, 'Not found');
			return;
		}

		$result = new stdClass();
		$result->hname = $hub->name;
		$result->hliaison = $hub->liaison;
		$result->hanniversarydate = $hub->anniversary_date;
		$result->hsupportlevel = $hub->support_level;

		// Create object with specific hub properties
		$obj = new stdClass();
		$obj->hub = $result;

		// Return object
		$this->setMessage($obj, 200, 'OK');
	}

	//--------------------------
	// Miscellaneous
	//--------------------------

	/**
	 * Save hub contact function
	 *
	 * @return 201 created on success (include newly created object in body)
	 */
	private function saveContact()
	{
		// Set message format
		$this->setMessageType($this->format);

		// Require authorization
		if (!$this->authorize())
		{
			$this->setMessage('', 401, 'Unauthorized');
			return;
		}

		// Incoming posted data (grab individually for added security)
		$c = array();
		$c['name']   = JRequest::getString('name');
		$c['phone']  = JRequest::getString('phone');
		$c['email']  = JRequest::getString('email');
		$c['role']   = JRequest::getString('role');
		$c['hub_id'] = JRequest::getInt('hid');

		// Create object and store new content
		$contact = Contact::blank()->set($c);
		if (!$contact->save())
		{
			$this->setMessage('Contact creation failed', 500, 'Internal server error');
			return;
		}

		// Return message (include $contact object for use again by the javascript)
		$this->setMessage($contact->id, 201, 'Created');
	}

	/**
	 * Get the list of users in the 'time' group
	 *
	 * @return 200 OK on success
	 */
	private function indexTimeUsers()
	{
		// Set message format
		$this->setMessageType($this->format);

		// Require authorization
		if (!$this->authorize())
		{
			$this->setMessage('', 401, 'Unauthorized');
			return;
		}

		// Get group members query
		$query  = "SELECT u.id, u.name";
		$query .= " FROM #__xgroups_members AS m";
		$query .= " LEFT JOIN #__xgroups AS g ON m.gidNumber = g.gidNumber";
		$query .= " LEFT JOIN #__users AS u ON u.id = m.uidNumber";
		$query .= " WHERE g.cn = 'time'";
		$query .= " ORDER BY u.name ASC";

		// Set the query
		$this->db->setQuery($query);
		$users = $this->db->loadObjectList();

		// Create object with users property
		$obj = new stdClass();
		$obj->users = $users;

		// Return object
		$this->setMessage($obj, 200, 'OK');
	}

	/**
	 * Method for getting possible unique values based on table and column
	 *
	 * @return 200 on success
	 */
	private function getValues()
	{
		// Set message format
		$this->setMessageType($this->format);

		// Require authorization
		if (!$this->authorize())
		{
			$this->setMessage('', 401, 'Unauthorized');
			return;
		}

		// Get table and column values
		$table  = JRequest::getVar('table', '');
		$column = JRequest::getVar('column', '');

		// Make sure those values haven't been tampered with
		$acceptable = array('time_tasks', 'time_records');
		if (!in_array($table, $acceptable))
		{
			$this->setMessage('Table provided is not allowed', 422, 'Unprocessable entity');
			return;
		}

		// Setup query
		$query  = "SELECT DISTINCT(" . $column . ") as val";
		$query .= " FROM #__" . $table;
		$query .= " ORDER BY val ASC";

		$this->db->setQuery($query);
		if (!$values = $this->db->loadObjectList())
		{
			$this->setMessage('Query failed', 500, 'Internal server error');
			return;
		}

		// Process any overrides
		require_once JPATH_ROOT . DS . 'components' . DS . 'com_time' . DS . 'helpers' . DS . 'filters.php';
		$values = TimeFilters::filtersOverrides($values, $column);

		// Create object with values
		$obj = new stdClass();
		$obj->values = $values;

		// Return object
		$this->setMessage($obj, 200, 'OK');
	}

	/**
	 * Get time records for the logged in user for today
	 *
	 * @return void
	 */
	private function today()
	{
		// Set message format
		$this->setMessageType($this->format);

		// Require authorization
		if (!$this->authorize())
		{
			$this->setMessage('', 401, 'Unauthorized');
			return;
		}

		// Create object and get records
		$records = Record::whereEquals('user_id', JFactory::getApplication()->getAuthn('user_id'))
                         ->where('date', '>=', JFactory::getDate(strtotime('today'))->toSql())
                         ->where('date', '<', JFactory::getDate(strtotime('today+1day'))->toSql());

		$results = array();

		// Restructure results into the format that the calendar plugin expects
		foreach ($records as $r)
		{
			$results[] = array(
				'id'          => $r->id,
				'title'       => $r->task->name,
				'start'       => \JHtml::_('date', $r->date, DATE_RFC2822),
				'end'         => \JHtml::_('date', $r->end, DATE_RFC2822),
				'description' => $r->description,
				'task_id'     => $r->task->id,
				'hub_id'      => $r->task->hub->id,
				'color'       => 'red'
			);
		}

		// Return object
		$this->setMessage($results, 200, 'OK');
	}

	/**
	 * Get the records per day this week
	 *
	 * @return void
	 */
	private function week()
	{
		// Set message format
		$this->setMessageType($this->format);

		// Require authorization
		if (!$this->authorize())
		{
			$this->setMessage('', 401, 'Unauthorized');
			return;
		}

		// Get the day of the week
		$today = \JFactory::getDate()->format('N') - 1;

		// Create object and get records
		$records = Record::whereEquals('user_id', JFactory::getApplication()->getAuthn('user_id'))
                         ->where('date', '>=',    JFactory::getDate(strtotime("today-{$today}days"))->toSql())
                         ->where('date', '<',     JFactory::getDate(strtotime("today+" . (8-$today) . 'days'))->toSql());

		$results = array();

		// Restructure results into the format that the calendar plugin expects
		foreach ($records as $r)
		{
			$dayOfWeek = \JFactory::getDate($r->date)->format('N') - 1;
			$results[$dayOfWeek][] = $r->time;
		}

		// Return object
		$this->setMessage($results, 200, 'OK');
	}

	/**
	 * Save a time record, updating it if it already exists
	 *
	 * @return void
	 */
	private function postRecord()
	{
		// Set message format
		$this->setMessageType($this->format);

		// Require authorization
		if (!$this->authorize())
		{
			$this->setMessage('', 401, 'Unauthorized');
			return;
		}

		// Incoming posted data (grab individually for added security)
		$r = [];
		$r['task_id']     = JRequest::getInt('task_id');
		$r['date']        = JFactory::getDate(JRequest::getVar('start'))->toSql();
		$r['end']         = JFactory::getDate(JRequest::getVar('end'))->toSql();
		$r['description'] = JRequest::getVar('description');
		$r['time']        = (strtotime($r['end']) - strtotime($r['date'])) / 3600;
		$r['user_id']     = JFactory::getApplication()->getAuthn('user_id');

		// Create object and store content
		$record = Record::oneOrNew(JRequest::getInt('id'))->set($r);
		$update = false;

		// See if we have an incoming id, indicating update
		if (!$record->isNew())
		{
			// Make sure updater is the owner of the record
			if (!$record->isMine())
			{
				$this->setMessage('You are only allowed to update your own records', 401, 'Unauthorized');
				return;
			}

			$update = true;
		}

		// Do the actual save
		if (!$record->save())
		{
			$this->setMessage('Record creation failed', 500, 'Internal server error');
			return;
		}

		// Return message
		$message = ($update) ? 'Record successfully saved' : 'Record successfully created';
		$status  = ($update) ? 200 : 201;
		$code    = ($update) ? 'OK' : 'Created';
		$this->setMessage($message, $status, $code);
	}

	/**
	 * Default method - not found
	 *
	 * @return 404 error
	 */
	private function method_not_found()
	{
		// Set the error message
		$this->_response->setErrorMessage(404, 'Not found');
		return;
	}

	/**
	 * Helper function to check whether or not someone is using oauth and in the 'time' group
	 *
	 * @return bool - true if in group, false otherwise
	 */
	private function authorize()
	{
		// Get the user id
		$user_id = JFactory::getApplication()->getAuthn('user_id');

		if (!is_numeric($user_id))
		{
			return false;
		}

		$permissions = new TimeModelPermissions('com_time');

		// Make sure action can be performed
		if (!$permissions->can('api'))
		{
			return false;
		}

		return true;
	}
}