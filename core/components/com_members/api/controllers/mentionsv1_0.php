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
    // URI: https://woo.aws.hubzero.org/api/members/mentions/list?search=''
    public function listTask() {
		$this->requiresAuthentication();

        $search = Request::getString('search', '');

        $entries = Member::all()
			->whereEquals('block', 0)
			->whereEquals('activation', 1)
			->where('username', 'NOT LIKE', '-%')
			->where('approved', '>', 0)
			->whereIn('access', User::getAuthorisedViewLevels());

        if ($search) {
            $entries->whereLike('name', strtolower((string)$search), 1)
                ->orWhereLike('username', strtolower((string)$search), 1)
                ->resetDepth();
        }

		$rows = $entries->rows();

		$response = array();
        foreach ($rows as $entry) {
            $obj = new stdClass;
            $obj->id        = $entry->get('id');
            $obj->picture   = $entry->picture();
            $obj->username  = $entry->get('username');
            $obj->name      = $entry->get('name');

            $response[] = $obj;
        }

		$this->send($response);
	}

    // List of all members within a specific group, locked down for privacy issues
    // https://woo.aws.hubzero.org/api/members/mentions/group?gid={gid}&search={search}
    public function groupTask() {
        $this->requiresAuthentication();

        $gid = Request::getInt('gid', 0);
        $group = \Hubzero\User\Group::getInstance($gid);

        if (!$group) { throw new Exception("There is no group for this ID", 404); }

        // A hidden group's roster is for its members only; a visible group's
        // forum may be open to non-members, who mention people from it too
        if ($group->get('discoverability') == 1
         && !$group->isMember(User::get('id'))
         && !$group->isManager(User::get('id'))
         && !User::authorise('core.manage', 'com_groups'))
        {
            throw new Exception("There is no group for this ID", 404);
        }

        // Get all group members, managers, etc
        $members    = $group->get('members');
        $managers   = $group->get('managers');
        $mergedMemberIds = array_unique(array_merge($members, $managers));

        $search = Request::getString('search', '');
        $searchInput = preg_quote(strtolower($search), '~');

        $response = array();

        foreach ($mergedMemberIds as $userId) {
            $user = User::getInstance($userId);

            $userName = $user->get('username');
            $name = $user->get('name');

            // Case-sensitive, use preg_grep as it matches a pattern
            // (email is not part of the response, so it is not searched either)
            $os = array(strtolower($userName), strtolower($name));

            $obj = new stdClass;
            $obj->id        = $user->get('id');
            $obj->picture   = $user->picture();
            $obj->username  = $userName;
            $obj->name      = $name;

            // Add to the response array if search is empty or if search term is present, search term is in array
            if ((!empty($search) && preg_grep('~' . $searchInput . '~', $os)) || empty($search)) {
                $response[] = $obj;
            }
        }

        $this->send($response);
    }
}
