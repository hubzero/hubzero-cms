<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Activity\Api\Controllers;

use Hubzero\Component\ApiController;
use Hubzero\Activity\Log as Activity;
use Hubzero\Activity\Subscription;
use Hubzero\Activity\Recipient;
use Hubzero\Utility\Date;
use Exception;
use stdClass;
use Request;
use Route;
use User;
use Lang;

/**
 * API controller class for activity entries
 */
class Entriesv1_1 extends ApiController
{
	/**
	 * Display a list of entries
	 *
	 * @apiMethod GET
	 * @apiUri    /activity/list
	 * @apiParameter {
	 * 		"name":          "scope",
	 * 		"description":   "Scope type (group, member, etc.)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "site"
	 * }
	 * @apiParameter {
	 * 		"name":          "scope_id",
	 * 		"description":   "Scope object ID",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
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
	 * 		"allowedValues": "created, title, alias, id, publish_up, publish_down, state"
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
	 * 		"name":          "start_date",
	 * 		"description":   "Start timestamp (YYYY-MM-DD or YYYY-MM-DD HH:mm:ss)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "end_date",
	 * 		"description":   "Start timestamp (YYYY-MM-DD or YYYY-MM-DD HH:mm:ss)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "recipients",
	 * 		"description":   "Filter by a list of recipients (type:id) the activity was sent to. Example: recipients=user:1000,project:2413",
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
	 * 		"allowedValues": "created, created_by, id, action, scope, scope_id"
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
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$filters = array(
			'scope'      => Request::getCmd('scope'),
			'scope_id'   => Request::getInt('scope_id', 0),
			'search'     => Request::getString('search', ''),
			'action'     => Request::getCmd('action'),
			'created_by' => Request::getInt('created_by', 0),
			'start_date' => Request::getString('start_date', ''),
			'end_date'   => Request::getString('end_date', ''),
			'recipients' => Request::getString('recipients', ''),
			'sort'       => Request::getString('sort', 'created'),
			'sort_Dir'   => strtolower(Request::getString('sort_Dir', 'desc')),
			'limit'      => Request::getInt('limit', \Config::get('list_limit')),
			'start'      => Request::getInt('start', 0)
		);

		if (!in_array($filters['sort_Dir'], ['asc', 'desc']))
		{
			throw new Exception(Lang::txt('COM_ACTIVITY_ERROR_INVALID_INPUT', $filters['sort']), 409);
		}

		if (!in_array($filters['sort'], ['id', 'created', 'created_by', 'action', 'scope', 'scope_id']))
		{
			throw new Exception(Lang::txt('COM_ACTIVITY_ERROR_INVALID_INPUT', $filters['sort']), 409);
		}

		$recipients = array();

		if ($filters['recipients'])
		{
			$rs = explode(',', $filters['recipients']);
			foreach ($rs as $r)
			{
				$recipient = array(
					'scope'    => 'user',
					'scope_id' => 0
				);

				$parts = explode(':', $r);

				if (count($parts) > 1)
				{
					$recipient['scope'] = trim($parts[0]);
				}
				$recipient['scope_id'] = intval(end($parts));

				if (!$recipient['scope_id'])
				{
					continue;
				}

				$recipients[] = $recipient;
			}
		}

		$l = Activity::blank()->getTableName();

		$query = Activity::all();

		if (!empty($recipients))
		{
			$r = Recipient::blank()->getTableName();

			$query
				->join($r, $r . '.log_id', $l . '.id')
				->whereEquals($r . '.state', Recipient::STATE_PUBLISHED);

			$where = 'and';
			foreach ($recipients as $i => $recipient)
			{
				if ($i > 0)
				{
					$where = 'or';
				}
				$query->where($r . '.scope', '=', $recipient['scope'], $where, 1)
					->whereEquals($r . '.scope_id', $recipient['scope_id'], 1)
					->resetDepth();
			}
		}

		if ($filters['scope'])
		{
			if (strpos($filters['scope'], '*') !== false)
			{
				if ($filters['scope'] == '*')
				{
					// They want all scopes so don't apply any query filter
				}
				else
				{
					// Wildcard! ex: projects.*
					// Get the scope up to the asterisk
					$scope = strstr($filters['scope'], '*', true);

					$query->whereLike($l . '.scope', $scope);
				}
			}
			else
			{
				$query->whereEquals($l . '.scope', $filters['scope']);
			}
		}

		if ($filters['scope_id'])
		{
			$query->whereEquals($l . '.scope_id', $filters['scope_id']);
		}

		if ($filters['action'])
		{
			$actions = explode(',', $filters['action']);
			$actions = array_map('trim', $actions);

			$query->whereIn($l . '.action', $actions);
		}

		if ($filters['created_by'])
		{
			$query->whereEquals($l . '.created_by', $filters['created_by']);
		}

		if ($filters['start_date'])
		{
			if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})$/", $filters['start_date'])
			 || preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filters['start_date']))
			{
				$query->where($l . '.created', '>=', with(new Date($filters['start_date']))->toSql());
			}
			else
			{
				throw new Exception(Lang::txt('COM_ACTIVITY_ERROR_INVALID_INPUT', $filters['start_date']), 409);
			}
		}

		if ($filters['end_date'])
		{
			if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})$/", $filters['end_date'])
			 || preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filters['end_date']))
			{
				$query->where($l . '.created', '<', with(new Date($filters['end_date']))->toSql());
			}
			else
			{
				throw new Exception(Lang::txt('COM_ACTIVITY_ERROR_INVALID_INPUT', $filters['end_date']), 409);
			}
		}

		if ($filters['search'])
		{
			$query->whereLike($l . '.description', $filters['search']);
		}

		//$total = $query->copy()->total();
		$response = array();

		$rows = $query
			->order($l . '.' . $filters['sort'], $filters['sort_Dir'])
			->limit($filters['limit'])
			->start($filters['start'])
			->paginated()
			->rows();

		foreach ($rows as $row)
		{
			$row->set('created', with(new Date($row->get('created')))->format('Y-m-d\TH:i:s\Z'));

			$obj = $row->toObject();
			$obj->details = $row->details->toObject();
			$obj->recipients = $row->recipients->toObject();
			$obj->created_by_name = User::getInstance($obj->created_by)->get('name');

			$dt = Date::of($obj->created);
			$ct = Date::of('now');

			$lapsed = $ct->toUnix() - $dt->toUnix();

			if ($lapsed < 30)
			{
				$obj->created_relative = Lang::txt('COM_ACTIVITY_JUST_NOW');
			}
			elseif ($lapsed > 86400 && $ct->format('Y') != $dt->format('Y'))
			{
				$obj->created_relative = $dt->toLocal('M j, Y');
			}
			elseif ($lapsed > 86400)
			{
				$obj->created_relative = $dt->toLocal('M j') . ' @ ' . $dt->toLocal('g:i a');
			}
			else
			{
				$obj->created_relative = $dt->relative();
			}

			if (User::authorise('core.edit') || User::get('id') == $obj->created_by)
			{
				$obj->canEdit = true;
			}
			if (User::authorise('core.delete') || User::get('id') == $obj->created_by)
			{
				$obj->canDelete = true;
			}

			foreach ($obj->recipients as $k => $recipient)
			{
				$obj->recipients[$k]->created = with(new Date($obj->recipients[$k]->created))->format('Y-m-d\TH:i:s\Z');
				if ($obj->recipients[$k]->viewed)
				{
					$obj->recipients[$k]->viewed = with(new Date($obj->recipients[$k]->viewed))->format('Y-m-d\TH:i:s\Z');
				}
			}

			$response[] = $obj;
		}

		$this->send($response);
	}

	/**
	 * Create an entry
	 *
	 * @apiMethod POST
	 * @apiUri    /activity
	 * @apiParameter {
	 * 		"name":        "scope",
	 * 		"description": "Scope type (group, member, etc.)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_id",
	 * 		"description": "Scope object ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "action",
	 * 		"description": "Action taken",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "alias",
	 * 		"description": "Entry alias",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "description",
	 * 		"description": "Description of the activity",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "created",
	 * 		"description": "Created timestamp (YYYY-MM-DD HH:mm:ss)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "now"
	 * }
	 * @apiParameter {
	 * 		"name":        "crated_by",
	 * 		"description": "User ID of entry creator",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "anonymous",
	 * 		"description": "Anonymous (0 = false, 1 = true)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "parent",
	 * 		"description": "ID of parent activity",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "recipients",
	 * 		"description": "Comma-separated list of scope:scope_id pairs (ex: user:1001,group:1000)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     1
	 * }
	 * @return    void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		$fields = array(
			'scope'          => Request::getString('scope', '', 'post'),
			'scope_id'       => Request::getInt('scope_id', 0, 'post'),
			'action'         => Request::getString('action', null, 'post', 'none', 2),
			'description'    => Request::getString('description', null, 'post', 'none', 2),
			'created'        => Request::getString('created', with(new Date('now'))->toSql(), 'post'),
			'created_by'     => Request::getInt('created_by', User::get('id'), 'post'),
			'anonymous'      => Request::getInt('anonymous', 0, 'post'),
			'parent'         => Request::getInt('parent', 0, 'post')
		);

		$row = Activity::blank();

		if ($fields['parent'])
		{
			$parent = Activity::oneOrNew($fields['parent']);

			if (!$parent->get('id'))
			{
				throw new Exception(Lang::txt('COM_ACTIVITY_ERROR_PARENT_NOT_FOUND'), 402);
			}
		}

		if (!$row->set($fields))
		{
			throw new Exception(Lang::txt('COM_ACTIVITY_ERROR_BINDING_DATA'), 500);
		}

		if (!$row->save())
		{
			throw new Exception(Lang::txt('COM_ACTIVITY_ERROR_SAVING_DATA'), 500);
		}

		if ($recipients = Request::getString('recipients'))
		{
			// Process a string of recipients
			//
			// This will be comma-separated list with each item consisting of scope:scope_id
			// ex: user:1001,group:2013,user:1145
			//
			// If no scope is provided, the ID will assumed to be a user ID
			// ex: 1001,group:2013,1145
			$recipients = explode(',', $recipients);
			$recipients = array_map('trim', $recipients);
			foreach ($recipients as $key => $recipient)
			{
				if (strstr($recipient, ':'))
				{
					$parts = explode(':', $recipient);

					$recipient = array(
						'scope'    => $parts[0],
						'scope_id' => $parts[1]
					);
				}

				$recipients[$key] = $recipient;
			}

			// Get everyone subscribed
			$subscriptions = Subscription::all()
				->whereEquals('scope', $row->get('scope'))
				->whereEquals('scope_id', $row->get('scope_id'))
				->rows();

			foreach ($subscriptions as $subscription)
			{
				$recipients[] = array(
					'scope'    => 'user',
					'scope_id' => $subscription->user_id
				);
			}

			$sent = array();

			// Do we have any recipients?
			foreach ($recipients as $receiver)
			{
				// Default to type 'user'
				if (!is_array($receiver))
				{
					$receiver = array(
						'scope'    => 'user',
						'scope_id' => $receiver
					);
				}

				// Make sure we have expected data
				if (!isset($receiver['scope'])
				 || !isset($receiver['scope_id']))
				{
					$receiver = array_values($receiver);

					$receiver['scope']    = $receiver[0];
					$receiver['scope_id'] = $receiver[1];
				}

				$key = $receiver['scope'] . '.' . $receiver['scope_id'];

				// No duplicate sendings
				if (in_array($key, $sent))
				{
					continue;
				}

				// Create a recipient object that ties a user to an activity
				$recipient = Recipient::blank()->set([
					'scope'    => $receiver['scope'],
					'scope_id' => $receiver['scope_id'],
					'log_id'   => $row->get('id'),
					'state'    => 1
				]);

				$recipient->save();

				$sent[] = $key;
			}
		}

		$row->set('created', with(new Date($row->get('created')))->format('Y-m-d\TH:i:s\Z'));

		$obj = $row->toObject();
		$obj->details = $row->details->toObject();
		$obj->recipients = $row->recipients->toObject();

		foreach ($obj->recipients as $k => $recipient)
		{
			$obj->recipients[$k]->created = with(new Date($obj->recipients[$k]->created))->format('Y-m-d\TH:i:s\Z');
			if ($obj->recipients[$k]->viewed)
			{
				$obj->recipients[$k]->viewed = with(new Date($obj->recipients[$k]->viewed))->format('Y-m-d\TH:i:s\Z');
			}
		}

		$this->send($obj);
	}

	/**
	 * Retrieve an entry
	 *
	 * @apiMethod GET
	 * @apiUri    /activity/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Activity entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function readTask()
	{
		$id = Request::getInt('id', 0);

		$row = Activity::oneOrFail($id);

		if (!$row->get('id'))
		{
			throw new Exception(Lang::txt('COM_ACTIVITY_ERROR_MISSING_RECORD'), 404);
		}

		$row->set('created', with(new Date($row->get('created')))->format('Y-m-d\TH:i:s\Z'));

		$obj = $row->toObject();
		$obj->details    = $row->details->toObject();
		$obj->recipients = $row->recipients->toObject();

		foreach ($obj->recipients as $k => $recipient)
		{
			$obj->recipients[$k]->created = with(new Date($obj->recipients[$k]->created))->format('Y-m-d\TH:i:s\Z');
			if ($obj->recipients[$k]->viewed)
			{
				$obj->recipients[$k]->viewed = with(new Date($obj->recipients[$k]->viewed))->format('Y-m-d\TH:i:s\Z');
			}
		}

		$this->send($obj);
	}

	/**
	 * Update an entry
	 *
	 * @apiMethod PUT
	 * @apiUri    /activity/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Activity entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope",
	 * 		"description": "Scope type (group, member, etc.)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_id",
	 * 		"description": "Scope object ID",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "action",
	 * 		"description": "Action taken",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "alias",
	 * 		"description": "Entry alias",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "description",
	 * 		"description": "Description of the activity",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "created",
	 * 		"description": "Created timestamp (YYYY-MM-DD HH:mm:ss)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "now"
	 * }
	 * @apiParameter {
	 * 		"name":        "crated_by",
	 * 		"description": "User ID of entry creator",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "anonymous",
	 * 		"description": "Anonymous (0 = false, 1 = true)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "parent",
	 * 		"description": "ID of parent activity",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "recipients",
	 * 		"description": "List of recpient channels",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     1
	 * }
	 * @return    void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		$id = Request::getInt('id', 0);

		$row = Activity::oneOrFail($id);

		if ($row->isNew())
		{
			throw new Exception(Lang::txt('COM_ACTIVITY_ERROR_MISSING_RECORD'), 404);
		}

		$fields = array(
			'scope'          => Request::getString('scope', $row->get('scope')),
			'scope_id'       => Request::getInt('scope_id', $row->get('scope_id')),
			'action'         => Request::getString('action', $row->get('action')),
			'description'    => Request::getString('description', $row->get('description')),
			'created'        => Request::getString('created', $row->get('created')),
			'created_by'     => Request::getInt('created_by', $row->get('created_by')),
			'anonymous'      => Request::getInt('anonymous', $row->get('anonymous')),
			'parent'         => Request::getInt('parent', $row->get('parent'))
		);

		if (!$row->set($fields))
		{
			throw new Exception(Lang::txt('COM_ACTIVITY_ERROR_BINDING_DATA'), 422);
		}

		if (!$row->save())
		{
			throw new Exception(Lang::txt('COM_ACTIVITY_ERROR_SAVING_DATA'), 500);
		}

		if ($recipients = Request::getString('recipients'))
		{
			// Process a string of recipients
			//
			// This will be comma-separated list with each item consisting of scope:scope_id
			// ex: user:1001,group:2013,user:1145
			//
			// If no scope is provided, the ID will assumed to be a user ID
			// ex: 1001,group:2013,1145
			$recipients = explode(',', $recipients);
			$recipients = array_map('trim', $recipients);
			foreach ($recipients as $key => $recipient)
			{
				if (strstr($recipient, ':'))
				{
					$parts = explode(':', $recipient);

					$recipient = array(
						'scope'    => $parts[0],
						'scope_id' => $parts[1]
					);
				}

				$recipients[$key] = $recipient;
			}

			// Get everyone subscribed
			$subscriptions = Subscription::all()
				->whereEquals('scope', $row->get('scope'))
				->whereEquals('scope_id', $row->get('scope_id'))
				->rows();

			foreach ($subscriptions as $subscription)
			{
				$recipients[] = array(
					'scope'    => 'user',
					'scope_id' => $subscription->user_id
				);
			}

			$sent = array();

			// Do we have any recipients?
			foreach ($recipients as $receiver)
			{
				// Default to type 'user'
				if (!is_array($receiver))
				{
					$receiver = array(
						'scope'    => 'user',
						'scope_id' => $receiver
					);
				}

				// Make sure we have expected data
				if (!isset($receiver['scope'])
				 || !isset($receiver['scope_id']))
				{
					$receiver = array_values($receiver);

					$receiver['scope']    = $receiver[0];
					$receiver['scope_id'] = $receiver[1];
				}

				$key = $receiver['scope'] . '.' . $receiver['scope_id'];

				// No duplicate sendings
				if (in_array($key, $sent))
				{
					continue;
				}

				// Create a recipient object that ties a user to an activity
				$recipient = Recipient::blank()->set([
					'scope'    => $receiver['scope'],
					'scope_id' => $receiver['scope_id'],
					'log_id'   => $row->get('id'),
					'state'    => 1
				]);

				$recipient->save();

				$sent[] = $key;
			}
		}

		$row->set('created', with(new Date($row->get('created')))->format('Y-m-d\TH:i:s\Z'));

		$obj = $row->toObject();
		$obj->details = $row->details->toObject();
		$obj->recipients = $row->recipients->toObject();

		foreach ($obj->recipients as $k => $recipient)
		{
			$obj->recipients[$k]->created = with(new Date($obj->recipients[$k]->created))->format('Y-m-d\TH:i:s\Z');
			if ($obj->recipients[$k]->viewed)
			{
				$obj->recipients[$k]->viewed = with(new Date($obj->recipients[$k]->viewed))->format('Y-m-d\TH:i:s\Z');
			}
		}

		$this->send($obj);
	}

	/**
	 * Delete an entry
	 *
	 * @apiMethod DELETE
	 * @apiUri    /activity/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Activity entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();

		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) <= 0)
		{
			throw new Exception(Lang::txt('COM_ACTIVITY_ERROR_MISSING_ID'), 500);
		}

		foreach ($ids as $id)
		{
			$row = Activity::oneOrNew(intval($id));

			if (!$row->get('id'))
			{
				throw new Exception(Lang::txt('COM_ACTIVITY_ERROR_MISSING_RECORD'), 404);
			}

			if (!$row->destroy())
			{
				throw new Exception($row->getError(), 500);
			}
		}

		$this->send(null, 204);
	}
}
