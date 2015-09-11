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
use Exception;
use stdClass;
use Request;
use Lang;

/**
 * API controller class for group members
 */
class Membersv1_0 extends ApiController
{
	/**
	 * Display members of a group
	 *
	 * @apiMethod GET
	 * @apiUri    /groups/{id}/members/list
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Group identifier",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "list",
	 * 		"description":   "Comma-separated list of member status",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "",
	 * 		"allowedValues": "members, managers, invitees, applicants"
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
		$id    = Request::getInt('id', 0);
		$group = \Hubzero\User\Group::getInstance($id);

		if (!$group)
		{
			throw new Exception(Lang::txt('COM_GROUPS_ERROR_MISSING_RECORD'), 404);
		}

		// get all group members, managers, etc
		$members    = $group->get('members');
		$managers   = $group->get('managers');
		$invitees   = $group->get('invitees');
		$applicants = $group->get('applicants');

		// get what the user wants back
		$list = Request::getVar('list', 'members, managers, invitees, applicants');

		// split by comma
		if (is_string($list))
		{
			$list = explode(',', $list);
			$list = array_map('trim', $list);
			$list = array_map('strtolower', $list);
		}

		// var to hold return
		$response = array();

		// add members
		if (in_array('members', $list))
		{
			foreach ($members as $k => $member)
			{
				$members[$k] = array('uidNumber' => $member);
			}
			$response['members'] = $members;
		}

		// add managers
		if (in_array('managers', $list))
		{
			foreach ($managers as $k => $manager)
			{
				$managers[$k] = array('uidNumber' => $manager);
			}
			$response['managers'] = $managers;
		}

		// add invitees
		if (in_array('invitees', $list))
		{
			foreach ($invitees as $k => $invitee)
			{
				$invitees[$k] = array('uidNumber' => $invitee);
			}
			$response['invitees'] = $invitees;
		}

		// add managers
		if (in_array('applicants', $list))
		{
			foreach ($applicants as $k => $applicant)
			{
				$applicants[$k] = array('uidNumber' => $applicant);
			}
			$response['applicants'] = $applicants;
		}

		$this->send($response);
	}
}
