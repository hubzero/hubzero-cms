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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * Lists all applicable time records
	 *
	 * @apiMethod GET
	 * @apiUri    /time/indexRecords
	 * @apiParameter {
	 * 		"name":        "tid",
	 * 		"description": "Task id by which to limit records",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "startdate",
	 * 		"description": "Beginning date threshold",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "enddate",
	 * 		"description": "Ending date threshold",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "limit",
	 * 		"description": "Maximim number of records to return",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     1000
	 * }
	 * @apiParameter {
	 * 		"name":        "start",
	 * 		"description": "Record index to start at (for pagination)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "orderby",
	 * 		"description": "Field by which to order results",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "id"
	 * }
	 * @apiParameter {
	 * 		"name":        "orderdir",
	 * 		"description": "Direction by which to order results",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "asc"
	 * }
	 * @return  void
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
		if ($limit = Request::getInt('limit', 1000))
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
	 * Saves a new time records
	 *
	 * @apiMethod POST
	 * @apiUri    /time/saveRecord
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Record id",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "task_id",
	 * 		"description": "Task ID of record",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "date",
	 * 		"description": "Start date/time of record",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "description",
	 * 		"description": "Record description",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "time",
	 * 		"description": "Duration of record",
	 * 		"type":        "float",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @return  void
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
		$r['user_id']     = App::get('authn')['user_id'];
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

	/**
	 * Lists all applicable tasks
	 *
	 * @apiMethod GET
	 * @apiUri    /time/indexTasks
	 * @apiParameter {
	 * 		"name":        "hid",
	 * 		"description": "Hub id",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "pactive",
	 * 		"description": "Task active status",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "limit",
	 * 		"description": "Maximim number of tasks to return",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     1000
	 * }
	 * @apiParameter {
	 * 		"name":        "start",
	 * 		"description": "Task index to start at (for pagination)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "orderby",
	 * 		"description": "Field by which to order results",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "id"
	 * }
	 * @apiParameter {
	 * 		"name":        "orderdir",
	 * 		"description": "Direction by which to order results",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "asc"
	 * }
	 * @return  void
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
		if ($limit = Request::getInt('limit', 1000))
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

	/**
	 * Lists all applicable hubs
	 *
	 * @apiMethod GET
	 * @apiUri    /time/indexHubs
	 * @apiParameter {
	 * 		"name":        "active",
	 * 		"description": "Hub active status",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "limit",
	 * 		"description": "Maximim number of hubs to return",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     1000
	 * }
	 * @apiParameter {
	 * 		"name":        "start",
	 * 		"description": "Hub index to start at (for pagination)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "orderby",
	 * 		"description": "Field by which to order results",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "id"
	 * }
	 * @apiParameter {
	 * 		"name":        "orderdir",
	 * 		"description": "Direction by which to order results",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "asc"
	 * }
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
		if ($limit = Request::getInt('limit', 1000))
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
	 * Shows a single hub
	 *
	 * @apiMethod GET
	 * @apiUri    /time/showHub
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Hub ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
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

	/**
	 * Saves a hub contact
	 *
	 * @apiMethod POST
	 * @apiUri    /time/saveContact
	 * @apiParameter {
	 * 		"name":        "name",
	 * 		"description": "Contact name",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "phone",
	 * 		"description": "Contact phone",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "email",
	 * 		"description": "Contact email",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "role",
	 * 		"description": "Contact role",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "hid",
	 * 		"description": "Hub id to which the contact belongs",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
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
	 * Retrieves possible unique values based on table and column
	 *
	 * @apiMethod GET
	 * @apiUri    /time/getValues
	 * @apiParameter {
	 * 		"name":        "table",
	 * 		"description": "Table name of interest",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     ""
	 * }
	 * @apiParameter {
	 * 		"name":        "column",
	 * 		"description": "Table column of interest",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     ""
	 * }
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
	 * Grabs time records for the logged in user for today
	 *
	 * @apiMethod GET
	 * @apiUri    /time/today
	 * @return    void
	 */
	public function todayTask()
	{
		// Require authentication and authorization
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		$start = Date::of(Request::getVar('start'), Config::get('offset'))->toSql();
		$end   = Date::of(Request::getVar('end'), Config::get('offset'))->toSql();

		// Create object and get records
		$records = Record::whereEquals('user_id', App::get('authn')['user_id'])
                         ->where('date', '>=', $start)
                         ->where('date', '<', $end);

		// Restructure response into the format that the calendar plugin expects
		$response = [];
		foreach ($records as $r)
		{
			$response[] = [
				'id'          => $r->id,
				'title'       => $r->task->name . ' [' . $r->task->hub->name . ']',
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
	 * Grabs the records per day this week for the current user
	 *
	 * @apiMethod GET
	 * @apiUri    /time/week
	 * @return    void
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
			$dayOfWeek = Date::of($r->date)->toLocal('N') - 1;
			$response[$dayOfWeek][] = $r->time;
		}

		// Return object
		$this->send($response);
	}

	/**
	 * Saves a new time records, updating it if it alread exists
	 *
	 * @apiMethod POST
	 * @apiUri    /time/postRecord
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Record id",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "task_id",
	 * 		"description": "Task ID of record",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "start",
	 * 		"description": "Start date/time of record",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "end",
	 * 		"description": "End date/time of record",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "description",
	 * 		"description": "Record description",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return  void
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
	 * Checks to ensure appropriate authorization
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
}