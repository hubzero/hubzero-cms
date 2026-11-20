<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Api\Controllers;

use Hubzero\Facades\Request;
use Hubzero\Facades\User;
use Hubzero\Facades\App;
use Hubzero\Facades\Date;

/**
 * API controller for the time component
 */
class Passportv1r0 extends Base
{
    /**
     * Passport badges. Placeholder for now.
     *
     * @apiMethod POST
     * @apiUri    /courses/passport/badge
     * @apiParameter {
     *      "name":        "action",
     *      "description": "Badge action",
     *      "type":        "string",
     *      "required":    true,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "badge_id",
     *      "description": "Passport badge ID",
     *      "type":        "integer",
     *      "required":    true,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "user_email",
     *      "description": "Email address to which the badge was asserted",
     *      "type":        "string",
     *      "required":    true,
     *      "default":     null
     * }
     * @return    void
     */
    public function badgeTask()
    {
        // Require authentication and authorization
        $this->authorizeOrFail();

        $action     = Request::getString('action', '');
        $badge_id   = Request::getString('badge_id', '');
        $user_email = Request::getString('user_email', '');

        if (empty($action)) {
            App::abort(400, 'Please provide action');
        }
        if ($action != 'accept' && $action != 'deny') {
            App::abort(400, 'Bad action. Must be either accept or deny');
        }
        if (empty($badge_id)) {
            App::abort(400, 'Please provide badge ID');
        }
        if (empty($user_email)) {
            App::abort(400, 'Please provide user email');
        }

        // Find user by email
        $user = User::oneByEmail($user_email);
        if (!$user->get('id')) {
            App::abort(404, 'User was not found');
        }

        $user_id = $user->get('id');

        // Get section from provider badge id
        $section_badge = \Components\Courses\Models\Section\Badge::loadByProviderBadgeId($badge_id);

        // Check if there is a match
        if (!$section_id = $section_badge->get('section_id')) {
            App::abort(400, 'No matching badge found');
        }

        // Authorize against the section this badge actually belongs to.
        //
        // This has to be a numeric comparison, not another access() call.
        // Permissions::getInstance() is a singleton that ignores its arguments
        // after the first call, and its access-checked-<item> flags live on
        // Component::params('com_courses'), one shared registry per request.
        // authorizeOrFail() above has already run access() for the course named
        // in the request, so a second access() here returns that same cached
        // answer and can never refuse -- leaving a manager of their own course
        // free to accept or deny a badge in any other course's section.
        $db = App::get('db');
        $db->setQuery(
            "SELECT o.course_id FROM `#__courses_offering_sections` AS s"
            . " JOIN `#__courses_offerings` AS o ON o.id = s.offering_id"
            . " WHERE s.id = " . (int) $section_id,
        );
        $sectionCourseId = (int) $db->loadResult();

        if (!$sectionCourseId || $sectionCourseId !== (int) $this->course_id) {
            App::abort(403, 'Unauthorized');
        }

        // Get member id via user id and section id
        $member = \Components\Courses\Models\Member::getInstance($user_id, 0, 0, $section_id);

        // Check if there is a match
        if (!$member->get('id')) {
            App::abort(400, 'Matching course member not found');
        }

        // Now actually load the badge
        $member_badge = \Components\Courses\Models\MemberBadge::loadByMemberId($member->get('id'));

        // Check if there is a match
        if (!$member_badge->get('id')) {
            App::abort(400, 'This member does not have a matching badge entry');
        }

        $now = Date::toSql();

        $member_badge->set('action', $action);
        $member_badge->set('action_on', $now);
        $member_badge->store();

        // Return message
        $this->send('Passport data saved.');
    }
}
