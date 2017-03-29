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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
class Entriesv1_0 extends ApiController
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
	 * @return  void
	 */
	public function listTask()
	{
		$filters = array(
			'scope'      => Request::getCmd('scope'),
			'scope_id'   => Request::getInt('scope_id', 0),
			'search'     => Request::getVar('search', ''),
			'action'     => Request::getCmd('action'),
			'created_by' => Request::getInt('created_by', 0)
		);

		$model = Activity::all();

		if ($filters['scope'])
		{
			$model->whereEquals('scope', $filters['scope']);
		}

		if ($filters['scope_id'])
		{
			$model->whereEquals('scope_id', $filters['scope_id']);
		}

		if ($filters['action'])
		{
			$actions = explode(',', $filters['action']);
			$actions = array_map('trim', $actions);

			$model->whereIn('action', $actions);
		}

		if ($filters['created_by'])
		{
			$model->whereEquals('created_by', $filters['created_by']);
		}

		$response = array();

		$rows = $model
			->ordered('sort', 'sort_Dir')
			->paginated()
			->rows();

		foreach ($rows as $row)
		{
			$row->set('created', with(new Date($row->get('created')))->format('Y-m-d\TH:i:s\Z'));

			$obj = $row->toObject();
			$obj->details = $row->details->toObject();
			$obj->recipients = $row->recipients->toObject();

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
			'scope'          => Request::getVar('scope', '', 'post'),
			'scope_id'       => Request::getInt('scope_id', 0, 'post'),
			'action'         => Request::getVar('action', null, 'post', 'none', 2),
			'description'    => Request::getVar('description', null, 'post', 'none', 2),
			'created'        => Request::getVar('created', with(new Date('now'))->toSql(), 'post'),
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

		if ($recipients = Request::getVar('recipients'))
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
			'scope'          => Request::getVar('scope', $row->get('scope')),
			'scope_id'       => Request::getInt('scope_id', $row->get('scope_id')),
			'action'         => Request::getVar('action', $row->get('action')),
			'description'    => Request::getVar('description', $row->get('description')),
			'created'        => Request::getVar('created', $row->get('created')),
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

		if ($recipients = Request::getVar('recipients'))
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

		$obj = $row->toObject();
		$obj->details = $row->details->toObject();
		$obj->recipients = $row->recipients->toObject();

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

		$ids = Request::getVar('id', array());
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
