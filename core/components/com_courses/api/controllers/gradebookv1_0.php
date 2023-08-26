<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Api\Controllers;

use Components\Courses\Models\Course;
use Components\Courses\Models\Member;
use Request;
use App;
use Date;

require_once __DIR__ . DS . 'base.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'course.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'asset.app.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'grade.book.php';

/**
 * API controller for the courses component
 */
class Gradebookv1_0 extends base
{
	/**
	 * Processes grade save from external app
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/gradebook/save
	 * @apiParameter {
	 * 		"name":        "asset_id",
	 * 		"description": "Asset id for external app",
	 * 		"type":        "int",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "course_alias",
	 * 		"description": "Course alias",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "offering_alias",
	 * 		"description": "Offering alias",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "section_alias",
	 * 		"description": "Section alias",
	 * 		"type":        "string", 
	 * 		"required":    false,
	 * 		"default":     '_Default'
	 * }
	 * @apiParameter {
	 * 		"name":        "data",
	 * 		"description": "Score (passed), details (json format)",
	 * 		"type":        "string", 
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function saveTask()
	{
		// Require authentication and authorization
		$this->authorizeOrFail();

		$user_id = App::get('authn')['user_id'];
		$asset_id   = Request::getInd('asset', '');

		if (!$asset_id)
		{
			App::abort(400, 'Failed to get asset ID');
		}

		$course_alias   = Request::getString('course', '');

		if (!$course_alias)
		{
			App::abort(400, 'Failed to get couurse alias');
		}

		$offering_alias = Request::getString('offering', '');

		if (!$offering_alias)
		{
			App::abort(400, 'Failed to get offering alias');
		}

		$section_alias  = Request::getString('section', '_Default');

		$course = Course::getInstance($course_alias);
		$course->offering($offering_alias);
		$course->offering()->section($section_alias);
		$section_id = $course->offering()->section()->get('id');

		$member = Member::getInstance($user_id, 0, 0, $section_id);

		if (!$member_id = $member->get('id'))
		{
			App::abort(500, 'Failed to get course member ID');
		}

		$data = Request::getString('data', '');

		$dats = trim($data);
		$message = json_decode($data);


		if (!$data)
		{
			App::abort(400, 'Missing data');
		}

		// Get timestamp
		$now = Date::toSql();

		// Save the external app details
		$app = new AssetApp($this->db);
		$app->set('member_id', $member_id);
		$app->set('asset_id', $asset_id);
		$app->set('created', $now);
		$app->set('passed', (($message->passed) ? 1 : 0));
		$app->set('details', $message->details);
		if (!$app->store())
		{
			App::abort(500, $app->getError());
		}

		// Now set/update the gradebook item
		$gradebook = new GradeBook($this->db);
		$gradebook->loadByUserAndAssetId($member_id, $asset_id);

		// Score is either 100 or 0
		$score = ($message->passed) ? 100 : 0;

		// See if gradebook entry already exists
		if ($gradebook->get('id'))
		{
			// Entry does exist, see if current score is better than previous score
			if ($score > $gradebook->get('score'))
			{
				$gradebook->set('score', $score);
				$gradebook->set('score_recorded', Date::toSql());
				if (!$gradebook->store())
				{
					App::abort(500, $gradebook->getError());
				}
			}
		}
		else
		{
			$gradebook->set('member_id', $member_id);
			$gradebook->set('score', $score);
			$gradebook->set('scope', 'asset');
			$gradebook->set('scope_id', $asset_id);
			$gradebook->set('score_recorded', Date::toSql());
			if (!$gradebook->store())
			{
				App::abort(500, $gradebook->getError());
			}
		}

		// Return message
		$this->send(['success' => true]);
	}
}
