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

namespace Components\Time\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Time\Models\Hub;
use Components\Time\Models\Task;
use Components\Time\Models\Record;
use Components\Time\Models\Contact;
use Components\Time\Models\Permissions;
use Components\Time\Models\Proxy;
use Components\Time\Models\liaison;
use Components\Time\Helpers\Filters;
use stdClass;
use Date;
use Request;

require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'hub.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'task.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'record.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'contact.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'permissions.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'proxy.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'liaison.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'helpers' . DS . 'filters.php';

/**
 * API controller for the time component
 *
 * @FIXME: break into multiple controllers based on entity type
 */
class Timev1_0 extends ApiController
{
	/**
	 * Displays the available options and parameters that the API for this comonent offers
	 *
	 * @apiMethod GET
	 * @apiUri    /blog
	 * @return    void
	 */
	public function indexTask()
	{
		$response = new stdClass();
		$response->component = 'time';
		$response->tasks     = [];

		$this->send($response);
	}

	//--------------------------
	// Records functions
	//--------------------------

	/**
	 * Get time records
	 *
	 * @return void
	 */
	public function indexRecordsTask()
	{
		// Require authentication and authorization
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		$record = Record::all();

		if ($task_id = Request::getInt('tid', false))
		{
			$record->whereEquals('task_id', $task_id);
		}
		if ($start_date = Request::getVar('startdate', false))
		{
			$record->where('date', '>=', $start_date);
		}
		if ($end_date = Request::getVar('enddate', false))
		{
			$record->where('date', '<=', $end_date);
		}
		if ($limit = Request::getInt('limit', 20))
		{
			$record->limit($limit);
		}
		if ($start = Request::getInt('start', 0))
		{
			$record->start($start);
		}
		if (($orderby  = Request::getCmd('orderby', 'id'))
		 && ($orderdir = Request::getCmd('orderdir', 'asc')))
		{
			$record->order($orderby, $orderdir);
		}

		// Create object with records property
		$response          = new stdClass();
		$response->records = $record->rows()->toObject();

		// Return object
		$this->send($response);
	}

	/**
	 * Save a time record
	 *
	 * @return void
	 */
	public function saveRecordTask()
	{
		// Require authentication and authorization
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		// Incoming posted data (grab individually for added security)
		$r                = [];
		$r['task_id']     = Request::getInt('task_id');
		$r['date']        = Date::of(Request::getVar('date'))->toSql();
		$r['description'] = Request::getVar('description');
		$r['time']        = Request::getVar('time');
		$r['user_id']     = JFactory::getApplication()->getAuthn('user_id');
		$r['end']         = date('Y-m-d H:i:s', (strtotime($r['date']) + ($r['time']*3600)));

		// Create object and store content
		$record = Record::oneOrNew(Request::getInt('id'))->set($r);

		// Do the actual save
		if (!$record->save())
		{
			App::abort(500, 'Record creation failed');
		}

		// Return message
		$this->send('Record successfully created', 201);
	}

	//--------------------------
	// Tasks functions
	//--------------------------

	/**
	 * Get time tasks
	 *
	 * @return void
	 */
	public function indexTasksTask()
	{
		// Require authentication and authorization
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		$task = Task::all();

		if ($hub_id = Request::getInt('hid', false))
		{
			$task->whereEquals('hub_id', $hub_id);
		}
		if ($active = Request::getInt('pactive', false))
		{
			$task->whereEquals('active', $active);
		}
		if ($limit = Request::getInt('limit', 20))
		{
			$task->limit($limit);
		}
		if ($start = Request::getInt('start', 0))
		{
			$task->start($start);
		}
		if (($orderby  = Request::getCmd('orderby', 'name'))
		 && ($orderdir = Request::getCmd('orderdir', 'asc')))
		{
			$task->order($orderby, $orderdir);
		}

		// Create object with tasks property
		$response        = new stdClass();
		$response->tasks = $task->rows()->toObject();

		// Return object
		$this->send($response);
	}

	//--------------------------
	// Hubs functions
	//--------------------------

	/**
	 * Get time hubs
	 *
	 * @return  void
	 */
	public function indexHubsTask()
	{
		// Require authentication and authorization
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		$hub = Hub::all();

		if ($active = Request::getInt('active', 1))
		{
			$hub->whereEquals('active', $active);
		}
		if ($limit = Request::getInt('limit', 100))
		{
			$hub->limit($limit);
		}
		if ($start = Request::getInt('start', 0))
		{
			$hub->start($start);
		}
		if (($orderby  = Request::getCmd('orderby', 'id'))
		 && ($orderdir = Request::getCmd('orderdir', 'asc')))
		{
			$hub->order($orderby, $orderdir);
		}

		// Create object with tasks property
		$response = new stdClass();
		$response->hubs = $hub->rows()->toObject();

		// Return object
		$this->send($response);
	}

	/**
	 * Get single hub
	 *
	 * @return  void
	 */
	public function showHubTask()
	{
		// Require authentication and authorization
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		// Incoming posted data
		$id = Request::getInt('id');

		// Error checking
		if (empty($id))
		{
			App::abort(404, 'Missing id parameter');
		}

		try
		{
			$hub = Hub::oneOrFail($id);
		}
		catch (Hubzero\Error\Exception\RuntimeException $e)
		{
			App::abort(404, 'Hub not found');
		}

		$result                   = new stdClass();
		$result->hname            = $hub->name;
		$result->hliaison         = $hub->liaison;
		$result->hsupportlevel    = $hub->support_level;
		$result->hanniversarydate = $hub->anniversary_date;

		// Create object with specific hub properties
		$response = new stdClass();
		$response->hub = $result;

		// Return object
		$this->send($response);
	}

	//--------------------------
	// Miscellaneous
	//--------------------------

	/**
	 * Save hub contact function
	 *
	 * @return  void
	 */
	public function saveContactTask()
	{
		// Require authentication and authorization
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		// Incoming posted data (grab individually for added security)
		$c           = [];
		$c['name']   = Request::getString('name');
		$c['phone']  = Request::getString('phone');
		$c['email']  = Request::getString('email');
		$c['role']   = Request::getString('role');
		$c['hub_id'] = Request::getInt('hid');

		// Create object and store new content
		$contact = Contact::blank()->set($c);
		if (!$contact->save())
		{
			App::abort(500, 'Contact creation failed');
		}

		// Return message (include $contact object for use again by the javascript)
		$this->send($contact->id, 201);
	}

	/**
	 * Get the list of users in the 'time' group
	 *
	 * @return  void
	 */
	public function indexTimeUsersTask()
	{
		// Require authentication and authorization
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		// Get group members query
		$query  = "SELECT u.id, u.name";
		$query .= " FROM #__xgroups_members AS m";
		$query .= " LEFT JOIN #__xgroups AS g ON m.gidNumber = g.gidNumber";
		$query .= " LEFT JOIN #__users AS u ON u.id = m.uidNumber";
		$query .= " WHERE g.cn = 'time'";
		$query .= " ORDER BY u.name ASC";

		// Set the query
		App::get('db')->setQuery($query);
		$users = App::get('db')->loadObjectList();

		// Create object with users property
		$response = new stdClass();
		$response->users = $users;

		// Return object
		$this->send($response);
	}

	/**
	 * Method for getting possible unique values based on table and column
	 *
	 * @return  void
	 */
	public function getValuesTask()
	{
		// Require authentication and authorization
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		// Get table and column values
		$table  = Request::getVar('table', '');
		$column = Request::getVar('column', '');

		// Make sure those values haven't been tampered with
		$acceptable = array('time_tasks', 'time_records');
		if (!in_array($table, $acceptable))
		{
			App::abort(404, 'Table provided is not allowed');
		}

		// Setup query
		$query  = "SELECT DISTINCT(" . $column . ") as val";
		$query .= " FROM #__" . $table;
		$query .= " ORDER BY val ASC";

		App::get('db')->setQuery($query);
		if (!$values = App::get('db')->loadObjectList())
		{
			App::abort(500, 'Query failed');
		}

		// Process any overrides
		$values = Filters::filtersOverrides($values, $column);

		// Create object with values
		$response = new stdClass();
		$response->values = $values;

		// Return object
		$this->send($response);
	}

	/**
	 * Get time records for the logged in user for today
	 *
	 * @return  void
	 */
	public function todayTask()
	{
		// Require authentication and authorization
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		// Create object and get records
		$records = Record::whereEquals('user_id', App::get('authn')['user_id'])
                         ->where('date', '>=', Date::of(strtotime('today'))->toSql())
                         ->where('date', '<', Date::of(strtotime('today+1day'))->toSql());

		// Restructure response into the format that the calendar plugin expects
		$response = [];
		foreach ($records as $r)
		{
			$response[] = [
				'id'          => $r->id,
				'title'       => $r->task->name,
				'start'       => Date::of($r->date)->toLocal(DATE_RFC2822),
				'end'         => Date::of($r->end)->toLocal(DATE_RFC2822),
				'description' => $r->description,
				'task_id'     => $r->task->id,
				'hub_id'      => $r->task->hub->id,
				'color'       => 'red'
			];
		}

		// Return object
		$this->send($response);
	}

	/**
	 * Get the records per day this week
	 *
	 * @return void
	 */
	public function weekTask()
	{
		// Require authentication and authorization
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		// Get the day of the week
		$today = Date::format('N') - 1;

		// Create object and get records
		$records = Record::whereEquals('user_id', App::get('authn')['user_id'])
                         ->where('date', '>=',    Date::of(strtotime("today-{$today}days"))->toSql())
                         ->where('date', '<',     Date::of(strtotime("today+" . (8-$today) . 'days'))->toSql());

		// Restructure response into the format that the calendar plugin expects
		$response = [];
		foreach ($records as $r)
		{
			$dayOfWeek = Date::of($r->date)->format('N') - 1;
			$response[$dayOfWeek][] = $r->time;
		}

		// Return object
		$this->send($response);
	}

	/**
	 * Save a time record, updating it if it already exists
	 *
	 * @return void
	 */
	public function postRecordTask()
	{
		// Require authentication and authorization
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		// Incoming posted data (grab individually for added security)
		$r = [];
		$r['task_id']     = Request::getInt('task_id');
		$r['date']        = Date::of(Request::getVar('start'))->toSql();
		$r['end']         = Date::of(Request::getVar('end'))->toSql();
		$r['description'] = Request::getVar('description');
		$r['time']        = (strtotime($r['end']) - strtotime($r['date'])) / 3600;
		$r['user_id']     = App::get('authn')['user_id'];

		// Create object and store content
		$record = Record::oneOrNew(Request::getInt('id'));
		$update = false;

		// See if we have an incoming id, indicating update
		if (!$record->isNew())
		{
			// Make sure updater is the owner of the record
			if (!$record->isMine())
			{
				App::abort(401, 'You are only allowed to update your own records');
			}

			$update = true;
		}

		// Do the actual save
		if (!$record->set($r)->save())
		{
			App::abort(500, 'Record creation failed');
		}

		// Return response
		$response = ($update) ? 'Record successfully saved' : 'Record successfully created';
		$status   = ($update) ? 200 : 201;

		$this->send($response, $status);
	}

	/**
	 * Helper function to check ensure appropriate authorization
	 *
	 * @return  bool
	 * @throws  Exception
	 */
	private function authorizeOrFail()
	{
		$permissions = new Permissions('com_time');

		// Make sure action can be performed
		if (!$permissions->can('api'))
		{
			App::abort(401, 'Unauthorized');
		}

		return true;
	}

	/**
	 * Check authentication
	 *
	 * @return  void
	 * @throws  Exception
	 */
	protected function requiresAuthentication()
	{
		if (!App::get('authn')['user_id'])
		{
			App::abort(404, 'Not Found');
		}
	}
}