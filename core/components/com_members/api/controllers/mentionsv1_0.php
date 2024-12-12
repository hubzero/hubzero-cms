<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Members\Models\Member;
use Component;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;
use User;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'member.php';

/**
 * Mentions API controller class
 */
class Mentionsv1_0 extends ApiController {
    // Full list of all members - used for mentions in ckeditor
    // URI: https://woo.aws.hubzero.org/api/members/mentions/list
    public function listTask() {
		$this->requiresAuthentication();

        $entries = Member::all()
			->whereEquals('block', 0)
			->whereEquals('activation', 1)
			->where('approved', '>', 0);

		$rows = $entries->rows();

		$response = array();
        foreach ($rows as $entry) {
            $obj = new stdClass;
            $obj->id        = $entry->get('id');
            $obj->picture   = $entry->picture();
            $obj->username  = $entry->get('username');
            $obj->name      = $entry->get('name');
            $obj->email     = $entry->get('email');

            $response[] = $obj;
        }

		$this->send($response);
	}

    // List of all members within a specific group, locked down for privacy issues
    // https://woo.aws.hubzero.org/api/members/mentions/group?gid={gid}
    public function groupTask() {
        $this->requiresAuthentication();

        $gid = Request::getInt('gid', 0);
        $group = \Hubzero\User\Group::getInstance($gid);

        if (!$group) { throw new Exception("There is no group for this ID", 404); }

        // Get all group members, managers, etc
        $members    = $group->get('members');
        $managers   = $group->get('managers');
        $mergedMemberIds = array_unique(array_merge($members, $managers));

        $response = array();
        foreach ($mergedMemberIds as $userId) {
            $user = User::getInstance($userId);

            $obj = new stdClass;
            $obj->id        = $user->get('id');
            $obj->picture   = $user->picture();
            $obj->username  = $user->get('username');
            $obj->name      = $user->get('name');
            $obj->email     = $user->get('email');

            $response[] = $obj;
        }

        $this->send($response);
    }
}
