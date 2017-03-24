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

namespace Components\Groups\Api\Controllers;

use Hubzero\Component\ApiController;
use Hubzero\User\Group;
use Exception;
use Request;
use Route;
use Event;
use Lang;
use User;

/**
 * API controller class for Group Plugins
 */
class Pluginsv1_0 extends ApiController
{
	/**
	 * Display a list of records for a plugin type
	 *
	 * @apiMethod GET
	 * @apiUri    /groups/{group}/{plugin}/list
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Group ID or alias that appears in the url for group.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "active",
	 * 		"description":   "Data type. This is the 'active' plugin such as blog, forum, etc.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
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
		//$this->requiresAuthentication();

		$cn = Request::getCmd('id', '');

		// Check for required fields (cn & title)
		if (!$cn)
		{
			throw new Exception(Lang::txt('Group identifier cannot be empty.'), 422);
		}

		$group = Group::getInstance($cn);

		// Check that the group exists
		if (!$group)
		{
			throw new Exception(Lang::txt('Group does not exist.'), 404);
		}

		// Check for an active plugin
		$active = Request::getCmd('active', '');

		if (!$active)
		{
			throw new Exception(Lang::txt('Active data type not specified.'), 422);
		}

		if (!User::authorise('core.admin') && !in_array(User::get('id'), $group->get('members')))
		{
			throw new Exception(Lang::txt('You are not authorized to access this group.'), 403);
		}

		$filters = array(
			'limit' => Request::getInt('limit', 25),
			'start' => Request::getInt('limitstart', 0)
		);

		$data = Event::trigger(
			'groups.onGroupsApiList',
			array(
				$group,
				$active,
				$filters['start'],
				$filters['limit']
			)
		);

		$response = array();

		foreach ($data as $i => $datum)
		{
			if (!$datum)
			{
				continue;
			}

			$response = $datum;
		}

		$this->send($response);
	}

	/**
	 * Create a record for a plugin type
	 *
	 * @apiMethod POST
	 * @apiUri    /groups/{group}/{plugin}
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Group ID or alias that appears in the url for group.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "active",
	 * 		"description":   "Data type. This is the 'active' plugin such as blog, forum, etc.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @return  void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		$cn = Request::getCmd('id', '');

		// Check for required fields (cn & title)
		if (!$cn)
		{
			throw new Exception(Lang::txt('Group identifier cannot be empty.'), 422);
		}

		$group = Group::getInstance($cn);

		// Check that the group exists
		if (!$group)
		{
			throw new Exception(Lang::txt('Group does not exist.'), 404);
		}

		// Check for an active plugin
		$active = Request::getCmd('active', '');

		if (!$active)
		{
			throw new Exception(Lang::txt('Active data type not specified.'), 422);
		}

		// Call the active plugin
		$data = Event::trigger(
			'groups.onGroupsApiCreate',
			array(
				$group,
				$active
			)
		);

		$response = null;

		foreach ($data as $datum)
		{
			if (!$datum)
			{
				continue;
			}

			$response = $datum;
		}

		// Check for errors at this point
		if (!$response)
		{
			throw new Exception(Lang::txt('Expected data object not found.'), 500);
		}

		$this->send($response);
	}

	/**
	 * Retrieve a record for a plugin type.
	 *
	 * @apiMethod GET
	 * @apiUri    /groups/{group}/{plugin}/{record_id}
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Group ID or alias that appears in the url for group.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "active",
	 * 		"description":   "Data type. This is the 'active' plugin such as blog, forum, etc.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "record_id",
	 * 		"description":   "Unique identifier",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @return    void
	 */
	public function readTask()
	{
		$this->requiresAuthentication();

		$cn = Request::getCmd('id', '');

		// Check for required fields (cn & title)
		if (!$cn)
		{
			throw new Exception(Lang::txt('Group identifier cannot be empty.'), 422);
		}

		$group = Group::getInstance($cn);

		// Check that the group exists
		if (!$group)
		{
			throw new Exception(Lang::txt('Group does not exist.'), 404);
		}

		// Check for an active plugin
		$active = Request::getCmd('active', '');

		if (!$active)
		{
			throw new Exception(Lang::txt('Active data type not specified.'), 422);
		}

		$id = Request::getInt('record_id', 0);

		// Call the active plugin
		$data = Event::trigger(
			'groups.onGroupsApiRead',
			array(
				$group,
				$active,
				$id
			)
		);

		$response = null;

		foreach ($data as $datum)
		{
			if (!$datum)
			{
				continue;
			}

			$response = $datum;
		}

		// Check for errors at this point
		if (!$response)
		{
			throw new Exception(Lang::txt('Expected data object not found.'), 500);
		}

		$this->send($response);
	}

	/**
	 * Update a record for a plugin type.
	 *
	 * @apiMethod PUT
	 * @apiUri    /groups/{group}/{plugin}/{id}
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Group ID or alias that appears in the url for group.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "active",
	 * 		"description":   "Data type. This is the 'active' plugin such as blog, forum, etc.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "record_id",
	 * 		"description":   "Unique identifier",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @return    void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		$cn = Request::getCmd('id', '');

		// Check for required fields (cn & title)
		if (!$cn)
		{
			throw new Exception(Lang::txt('Group identifier cannot be empty.'), 422);
		}

		$group = Group::getInstance($cn);

		// Check that the group exists
		if (!$group)
		{
			throw new Exception(Lang::txt('Group does not exist.'), 404);
		}

		// Check for an active plugin
		$active = Request::getCmd('active', '');

		if (!$active)
		{
			throw new Exception(Lang::txt('Active data type not specified.'), 422);
		}

		$id = Request::getInt('record_id', 0);

		// Call the active plugin
		$data = Event::trigger(
			'groups.onGroupsApiUpdate',
			array(
				$group,
				$active,
				$id
			)
		);

		$response = null;

		foreach ($data as $datum)
		{
			if (!$datum)
			{
				continue;
			}

			$response = $datum;
		}

		// Check for errors at this point
		if (!$response)
		{
			throw new Exception(Lang::txt('Expected data object not found.'), 500);
		}

		$this->send($response);
	}

	/**
	 * Delete a record for a plugin type.
	 *
	 * @apiMethod DELETE
	 * @apiUri    /groups/{group}/{plugin}/{id}
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Group ID or alias that appears in the url for group.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "active",
	 * 		"description":   "Data type. This is the 'active' plugin such as blog, forum, etc.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "record_id",
	 * 		"description":   "Unique identifier",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @return    void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();

		$cn = Request::getCmd('id', '');

		// Check for required fields (cn & title)
		if (!$cn)
		{
			throw new Exception(Lang::txt('Group identifier cannot be empty.'), 422);
		}

		$group = Group::getInstance($cn);

		// Check that the group exists
		if (!$group)
		{
			throw new Exception(Lang::txt('Group does not exist.'), 404);
		}

		// Check for an active plugin
		$active = Request::getCmd('active', '');

		if (!$active)
		{
			throw new Exception(Lang::txt('Active data type not specified.'), 422);
		}

		$id = Request::getInt('record_id', 0);

		// Call the active plugin
		Event::trigger(
			'groups.onGroupsApiDelete',
			array(
				$group,
				$active,
				$id
			)
		);

		$this->send(null, 204);
	}
}
