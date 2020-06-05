<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Api\Controllers;

use Components\Support\Models\Ticket;
use Components\Support\Models\Status;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Component;
use Exception;
use stdClass;
use Request;
use Config;
use Route;
use Lang;
use User;

require_once dirname(dirname(__DIR__)) . '/models/ticket.php';
require_once dirname(dirname(__DIR__)) . '/helpers/acl.php';
require_once dirname(dirname(__DIR__)) . '/helpers/utilities.php';
require_once Component::path('com_groups') . DS . 'models' . DS . 'orm' . DS . 'group.php';

/**
 * API controller class for support tickets
 */
class Ticketsv2_1 extends ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->config = Component::params('com_support');
		$this->database = \App::get('db');

		$this->acl = \Components\Support\Helpers\ACL::getACL();
		$this->acl->setUser(User::get('id'));

		parent::execute();
	}

	/**
	 * Display a list of tickets
	 *
	 * @apiMethod GET
	 * @apiUri    /support/tickets/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "limitstart",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "search",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "sort",
	 * 		"description":   "Field to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "created",
	 * 		"allowedValues": "id, type, created, closed, target_date, status, open, owner, severity, name, login, email"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "desc",
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @apiParameter {
	 * 		"name":          "type",
	 * 		"description":   "Ticket type (0 = user submitted, 1 = automatic submission by tool)",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0,
	 * 		"allowedValues": "0, 1"
	 * }
	 * @apiParameter {
	 * 		"name":          "owner",
	 * 		"description":   "List tickets with a specific owner (userid)",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "status",
	 * 		"description":   "List tickets with a specific status id",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "open",
	 * 		"description":   "Specify open/closed state",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "category",
	 * 		"description":   "Category ID the ticket is assigned to",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "severity",
	 * 		"description":   "List tickets with a specific severity",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "group",
	 * 		"description":   "List tickets with a specific group (by alias or group ID)",
	 * 		"type":          "string|integer",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "created",
	 * 		"description":   "A timestamp (YYYY-MM-DD HH:mm:ss) for items created on or after the specified date. A time window can be specified adding a second timestamp, separated by a comma. Example: 2018-01-01,2018-12-31",
	 * 		"type":          "string|integer",
	 * 		"required":      false,
	 * 		"default":       null,
	 * 		"allowedValues": "YYYY-MM or YYYY-MM-DD or YYYY-MM-DD HH:mm:ss"
	 * }
	 * @apiParameter {
	 * 		"name":          "closed",
	 * 		"description":   "A timestamp (YYYY-MM-DD HH:mm:ss) for items closed on or after the specified date. A time window can be specified adding a second timestamp, separated by a comma. Example: 2018-01-01,2018-12-31",
	 * 		"type":          "string|integer",
	 * 		"required":      false,
	 * 		"default":       null,
	 * 		"allowedValues": "YYYY-MM or YYYY-MM-DD or YYYY-MM-DD HH:mm:ss"
	 * }
	 * @return    void
	 */
	public function listTask()
	{
		$this->requiresAuthentication();

		if (!$this->acl->check('read', 'tickets'))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		$tickets = Ticket::all();

		$type = Request::getInt('type', 0);
		if (!is_null($type))
		{
			$tickets->whereEquals('type', $type);
		}

		if ($owner = Request::getInt('owner', null))
		{
			$tickets->whereEquals('owner', $owner);
		}

		if ($status = Request::getInt('status', null))
		{
			$tickets->whereEquals('status', $status);
		}

		$open = Request::getInt('open', null);
		if (!is_null($open))
		{
			$tickets->whereEquals('status', $open);
		}

		if ($category_id = Request::getInt('category', null))
		{
			$tickets->whereEquals('category', $category_id);
		}

		if ($severity = Request::getWord('severity', null))
		{
			if (in_array($severity, array('minor', 'normal', 'major', 'critical')))
			{
				$tickets->whereEquals('severity', $severity);
			}
		}

		if ($group_id = Request::getString('group', null))
		{
			if (!is_numeric($group_id))
			{
				$group = \Hubzero\User\Group::getInstance($group_id);
				$group_id = 0;

				if ($group)
				{
					$group_id = $group->gidNumber;
				}
			}

			$tickets->whereEquals('group_id', $group_id);
		}

		$created = $this->toTimestamp(Request::getString('created', ''));
		if ($created)
		{
			if (is_array($created) && count($created) > 1)
			{
				$tickets->where('created', '>=', $created[0], 1)
					->orWhere('created', '<', $created[1], 1)
					->resetDepth();
			}
			else
			{
				if (is_array($created))
				{
					$created = implode('', $created);
				}
				$tickets->where('created', '>=', $created);
			}
		}

		$closed = $this->toTimestamp(Request::getString('closed', ''));
		if ($closed)
		{
			if (is_array($closed) && count($closed) > 1)
			{
				$tickets->where('closed', '>=', $closed[0], 1)
					->orWhere('closed', '<', $closed[1], 1)
					->resetDepth();
			}
			else
			{
				if (is_array($closed))
				{
					$closed = implode('', $closed);
				}
				$tickets->where('closed', '>=', $closed);
			}
		}

		$total = clone $tickets;

		$sort = Request::getWord('sort', 'created');
		if (!in_array($sort, array('id', 'type', 'created', 'closed', 'status', 'open', 'owner', 'severity', 'target_date', 'name', 'login', 'email')))
		{
			$sort = 'created';
		}
		$sort_dir = Request::getWord('sort_Dir', 'desc');
		if (!in_array($sort_dir, array('asc', 'desc')))
		{
			$sort_dir = 'desc';
		}

		$rows = $tickets->order($sort, $sort_dir)
			->limit(Request::getInt('limit', 25))
			->start(Request::getInt('start', 0))
			->rows();

		$response = new stdClass;
		$response->total = $total->total();
		$response->tickets = array();
		foreach ($rows as $row)
		{
			$temp = $row->toArray();

			$response->tickets[] = $temp;
		}

		$this->send($response);
	}

	/**
	 * Ensure timestamp follows accepted pattern
	 *
	 * @param   string  $val  Timestamp or two timestamps separated by a comma
	 *                        YYYY-MM or YYYY-MM-DD or YYYY-MM-DD HH:mm:ss or YYYY-MM,YYYY-MM
	 * @return  mixed   string or null if not a valid timestamp
	 */
	private function toTimestamp($val=null)
	{
		if ($val)
		{
			$val = strtolower($val);

			if (strstr($val, ','))
			{
				$vals = explode(',', $val);
				foreach ($vals as $i => $v)
				{
					$vals[$i] = $this->toTimestamp(trim($v));
				}
				return $vals;
			}

			if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $val, $regs))
			{
				// Time already matches pattern so do nothing.
			}
			else if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $val, $regs))
			{
				$val .= ' 00:00:00';
			}
			else if (preg_match("/([0-9]{4})-([0-9]{2})/", $val, $regs))
			{
				$val .= '-01 00:00:00';
			}
			else
			{
				// Not an acceptable time
				$val = null;
			}
		}

		return $val;
	}

	/**
	 * Create a new ticket
	 *
	 * @apiMethod POST
	 * @apiUri    /support/tickets
	 * @apiParameter {
	 * 		"name":        "username",
	 * 		"description": "The submitter's username",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "name",
	 * 		"description": "The submitter's name",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "email",
	 * 		"description": "The submitter's email address",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "os",
	 * 		"description": "The submitter's operating system",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "Unknown"
	 * }
	 * @apiParameter {
	 * 		"name":        "browser",
	 * 		"description": "The submitter's browser type",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "Unknown"
	 * }
	 * @apiParameter {
	 * 		"name":        "report",
	 * 		"description": "Description of the user's problem",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "status",
	 * 		"description": "The status code of the ticket",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "severity",
	 * 		"description": "The severity of the issue",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "normal" ,
	 *		"allowed_values": "minor, normal, major, critical"
	 * }
	 * @apiParameter {
	 * 		"name":        "owner",
	 * 		"description": "The id of the user to assign this ticket to",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "group",
	 * 		"description": "Alias of the group to assign the ticket to",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "files",
	 * 		"description": "***STUB*** NOT WORKING",
	 * 		"type":        "binary",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return     void
	 */
	public function createTask()
	{
		//get the userid and attempt to load user profile
		$userid = User::get('id');
		$result = User::getInstance($userid);

		//make sure we have a user
		if (!$result || !$result->get('id'))
		{
			throw new Exception(Lang::txt('User not found.'), 500);
		}

		//check required fields
		if (!Request::getString('name', null))
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_NAME_NOT_FOUND'), 404);
		}

		if (!Request::getString('email', null))
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_EMAIL_NOT_FOUND'), 404);
		}

		if (!Request::getString('report', null))
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_REPORT_NOT_FOUND'), 404);
		}

		// Initiate class and bind data to database fields
		$ticket = Ticket::blank();

		// Set the column values for our new row
		$ticket->set('status', Request::getInt('status', 0));

		//check if a username was sent, otherwise fill in with the session's username
		if ($login = Request::getString('username', null))
		{
			$ticket->set('login', $login);
		}
		else
		{
			$ticket->set('login', $result->get('username'));
		}

		//setting more optional values
		$severity = Request::getWord('severity', 'normal');
		if (!in_array($severity, array('minor', 'normal', 'major', 'critical')))
		{
			$severity = 'normal';
		}
		$ticket->set('severity', $severity);
		$ticket->set('owner', Request::getInt('owner', 0));

		//check if the report was good
		$ticket->set('report', Request::getString('report', ''));
		if (!$ticket->get('report'))
		{
			throw new Exception(Lang::txt('Error: Report contains no text.'), 500);
		}

		// build the summary
		$summary = substr($ticket->get('report'), 0, 70);
		if (strlen($summary) >= 70)
		{
			$summary .= '...';
		}
		$ticket->set('summary', $summary);

		//continue setting values
		$ticket->set('email', Request::getString('email', 'None'));
		$ticket->set('name', Request::getString('name', 'None'));
		$ticket->set('os', Request::getString('os', 'Unknown'));
		$ticket->set('browser', Request::getString('browser', 'Unknown'));
		$ticket->set('ip', Request::ip());
		//$ticket->set('hostname', gethostbyaddr(Request::get('REMOTE_ADDR','','server')));
		$ticket->set('uas', 'API Submission');
		$ticket->set('referrer', '/api/v2.1/support/tickets');
		$ticket->set('instances', 1);
		$ticket->set('section', 1);
		$ticket->set('open', 1);

		if (Request::get('group', null))
		{
			$group_model = \Components\Groups\Models\Orm\Group::oneByCn(Request::getString('group'));
			if ($group_model->get('gidNumber', null))
			{
				$ticket->set('group_id', $group_model->get('gidNumber'));
			}
			else
			{
				throw new Exception(Lang::txt("COM_SUPPORT_ERROR_INVALID_GROUP_CN"), 404);
			}
		}
		// Save the data
		if (!$ticket->save())
		{
			throw new Exception($ticket->getErrors(), 500);
		}

		// Now we have a ticket ID, lets check for attachments
		\Components\Support\Helpers\Utilities::addAttachments($ticket->get('id'));

		// Set the response
		$msg = new stdClass;
		$msg->submitted = $ticket->get('created');
		$msg->ticket    = $ticket->get('id');

		$this->send($msg, 201);
	}

	/**
	 * Displays details for a ticket
	 *
	 * @apiMethod GET
	 * @apiUri    /support/tickets/{ticket}
	 * @apiParameter {
	 * 		"name":        "ticket",
	 * 		"description": "Ticket identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @return    void
	 */
	public function readTask()
	{
		$this->requiresAuthentication();

		if (!$this->acl->check('read', 'tickets'))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		// Initiate class and bind data to database fields
		$ticket_id = Request::getInt('id', 0);

		// Initiate class and bind data to database fields
		$ticket = Ticket::oneOrFail($ticket_id);

		$owner     = $ticket->assignee;
		$submitter = $ticket->submitter;

		$response = new stdClass;
		$response->id = $ticket->get('id');
		$response->owner_id = $owner->get('id');
		$response->submitter_id = $submitter->get('id');
		$response->group_id = $ticket->get('group_id');

		foreach (array('status', 'created', 'severity', 'os', 'browser', 'ip', 'hostname', 'uas', 'referrer', 'open', 'closed') as $prop)
		{
			$response->$prop = $ticket->get($prop);
		}

		$response->summary = $ticket->get('summary');
		$response->report  = $ticket->get('report');

		$response->url = str_replace('/api', '', rtrim(Request::base(), '/') . '/' . ltrim(Route::url('index.php?option=com_support&controller=tickets&task=tickets&id=' . $response->id), '/'));

		$this->send($response);
	}

	/**
	 * Update a ticket
	 *
	 * @apiMethod PUT
	 * @apiUri    /support/tickets/{ticket}
	 * @apiParameter {
	 * 		"name":        "ticket",
	 * 		"description": "Ticket identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "owner",
	 * 		"description": "Ticket owner",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "status",
	 * 		"description": "Ticket status",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "severity",
	 * 		"description": "Ticket severity",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null,
	 *		"allowed_values": "minor, normal, major, critical"
	 * }
	 * @apiParameter {
	 * 		"name":        "group",
	 * 		"description": "Alias of group ticket should be assigned to",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		if (!$this->acl->check('edit', 'tickets'))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		// Initiate class and bind data to database fields
		$ticket_id = Request::getInt('id', 0);
		$status    = Request::getInt('status', null);
		$owner     = Request::getInt('owner', null);
		$severity  = Request::getString('severity', null);
		$group     = Request::getString('group', null);

		// Initiate class and bind data to database fields
		$model = Ticket::oneOrFail($ticket_id);

		if ($status)
		{
			//cheap check to see if we got a valid status
			$status_model = Status::oneOrFail($status);

			if (!$status_model->get('id'))
			{
				throw new Exception(Lang::txt("COM_SUPPORT_ERROR_INVALID_STATUS"), 404);
			}

			$model->set('status', $status);
			$model->set('open', $status_model->get('open'));
		}

		if ($owner)
		{
			//cheap check to see if we got a valid user
			$owner_model = \Hubzero\User\User::oneOrNew($owner);

			if (!$owner_model->get('id'))
			{
				throw new Exception(Lang::txt("COM_SUPPORT_ERROR_INVALID_OWNER"), 404);
			}

			$model->set('owner', $owner);
		}

		if ($severity)
		{
			if (in_array($severity, ['minor', 'normal', 'major', 'critical']))
			{
				$model->set('severity', $severity);
			}
			else
			{
				throw new Exception(Lang::txt("COM_SUPPORT_ERROR_INVALID_SEVERITY"), 404);
			}
		}

		if ($group)
		{
			$group_model = \Components\Groups\Models\Orm\Group::oneByCn($group);

			if ($group_model->get('gidNumber', null))
			{
				$model->set('group_id', $group_model->get('gidNumber'));
			}
			else
			{
				throw new Exception(Lang::txt("COM_SUPPORT_ERROR_INVALID_GROUP_CN"), 404);
			}
		}

		if ($model->save())
		{
			$this->send(null, 204);
		}
		else
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_CANNOT_SAVE'), 500);
		}
	}

	/**
	 * Delete a ticket
	 *
	 * @apiMethod DELETE
	 * @apiUri    /support/tickets/{ticket}
	 * @apiParameter {
	 * 		"name":        "ticket",
	 * 		"description": "Ticket identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @return    void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();

		if (!$this->acl->check('delete', 'tickets'))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		// Initiate class and bind data to database fields
		$ticket_id = Request::getInt('id', 0);

		// Initiate class and bind data to database fields
		$model = Ticket::oneOrFail($ticket_id);

		if (!$model->destroy())
		{
			throw new Exception($model->getError(), 500);
		}

		$this->send(null, 204);
	}
}
