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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Api\Controllers;

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

require_once(dirname(dirname(__DIR__)) . '/models/ticket.php');
require_once(dirname(dirname(__DIR__)) . '/models/orm/ticket.php');
require_once(dirname(dirname(__DIR__)) . '/models/orm/status.php');
require_once(dirname(dirname(__DIR__)) . '/models/orm/attachment.php');
require_once(dirname(dirname(__DIR__)) . '/helpers/acl.php');
require_once(dirname(dirname(__DIR__)) . '/helpers/utilities.php');
require_once Component::path('com_groups') . DS . 'models' . DS . 'orm' . DS . 'group.php';

/**
 * API controller class for support tickets
 */
class Ticketsv2_0 extends ApiController
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
	 * @apiUri    /support/tickets
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
	 * 		"name":          "severity",
	 * 		"description":   "List tickets with a specific severity",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "group",
	 * 		"description":   "List tickets with a specific group (by alias)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
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

		$tickets = \Components\Support\Models\Orm\Ticket::all();

		if (Request::getInt('owner', null))
		{
			$tickets = $tickets->whereEquals('owner', Request::get('owner'));
		}

		if (Request::getInt('status', null))
		{
			$tickets = $tickets->whereEquals('status', Request::get('status'));
		}

		if (Request::getString('severity', null))
		{
			$tickets = $tickets->whereEquals('severity', Request::get('severity'));
		}

		if (Request::getString('group', null))
		{
			$tickets = $tickets->whereEquals('group', Request::getString('group'));
		}

		$response = new StdClass;
		$response->total = $tickets->count();
		$response->tickets = array();
		foreach ($tickets->rows() as $row)
		{
			$temp = array();
			$temp['id'] = $row->id;
			$temp['name'] = $row->name;
			$temp['login'] = $row->login;
			$temp['email'] = $row->email;
			$temp['status'] = $row->status;
			$temp['severity'] = $row->severity;
			$temp['owner'] = $row->owner;
			$temp['summary'] = $row->summary;
			$temp['group'] = $row->group;
			$temp['target_date'] = $row->target_date;

			$response->tickets[] = $temp;
		}

		$this->send($response);
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
		if (!Request::get('name', null))
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_NAME_NOT_FOUND'), 404);
		}

		if (!Request::get('email', null))
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_EMAIL_NOT_FOUND'), 404);
		}

		if (!Request::get('report', null))
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_REPORT_NOT_FOUND'), 404);
		}

		// Initiate class and bind data to database fields
		$ticket = new \Components\Support\Models\Orm\Ticket;

		// Set the column values for our new row
		$ticket->set('status', Request::getInt('status', 0));

		//check if a username was sent, otherwise fill in with the session's username
		if (Request::getString('username', null))
		{
			$ticket->set('login', Request::get('username', 'None'));
		}
		else
		{
			$ticket->set('login', $result->get('username'));
		}

		//setting more optional values
		$ticket->set('severity', Request::get('severity', 'normal'));
		$ticket->set('owner', Request::get('owner', 0));

		//check if the report was good
		$ticket->set('report', Request::get('report', '', 'none', 2));
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
		$ticket->set('email', Request::get('email', 'None'));
		$ticket->set('name', Request::get('name', 'None'));
		$ticket->set('os', Request::get('os', 'Unknown'));
		$ticket->set('browser', Request::get('browser', 'Unknown'));
		$ticket->set('ip', Request::ip());
		//$ticket->set('hostname', gethostbyaddr(Request::get('REMOTE_ADDR','','server')));
		$ticket->set('uas', 'API Submission');
		$ticket->set('referrer', '/api/v2.0/support/tickets');
		$ticket->set('instances', 1);
		$ticket->set('section', 1);
		$ticket->set('open', 1);

		if (Request::get('group', null))
		{
			$group_model = \Components\Groups\Models\Orm\Group::oneByCn(Request::get('group'));
			if ($group_model->get('gidNumber', null))
			{
				$model->set('group_id', $group_model->get('gidNumber'));
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
		$ticket = \Components\Support\Models\Orm\Ticket::oneOrFail($ticket_id);

		$owner = $ticket->get_owner;
		$submitter = $ticket->submitter;

		$response = new stdClass;
		$response->id = $ticket->get('id');
		$response->owner_id = $owner->get('id');
		$response->submitter_id = $submitter->get('id');

		$response->group_id = $ticket->group_id;

		foreach (array('status', 'created', 'severity', 'os', 'browser', 'ip', 'hostname', 'uas', 'referrer', 'open', 'closed') as $prop)
		{
			$response->$prop = $ticket->get($prop);
		}

		$response->summary = $ticket->get('summary');
		$response->report = $ticket->get('report');

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
		$status = Request::getInt('status', null);
		$owner = Request::getInt('owner', null);
		$severity = Request::getString('severity', null);
		$group = Request::getString('group', null);

		// Initiate class and bind data to database fields
		$model = \Components\Support\Models\Orm\Ticket::oneOrFail($ticket_id);

		if ($status)
		{
			//cheap check to see if we got a valid status
			$status_model = \Components\Support\Models\Orm\Status::oneOrFail($status);
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
			$owner_model = \Hubzero\User\User::one($owner);
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
		$model = \Components\Support\Models\Orm\Ticket::oneOrFail($ticket_id);

		if (!$model->destroy())
		{
			throw new Exception($model->getError(), 500);
		}

		$this->send(null, 204);
	}
}
