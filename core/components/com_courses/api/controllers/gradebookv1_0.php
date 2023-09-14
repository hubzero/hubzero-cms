<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Api\Controllers;

use Components\Courses\Models\Course;
use Components\Courses\Models\Member;
use Components\Courses\Tables\GradeBook;
use Components\Courses\Tables\AssetXapp;

use Request;
use App;
use Date;

require_once __DIR__ . DS . 'base.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'course.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'grade.book.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'asset.xapp.php';


/**
 * API controller for the courses gradebook
 */
class Gradebookv1_0 extends base
{
	/**
	 * Processes grade save from external app
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/gradebook/save
	 * @apiParameter {
	 * 		"name":        "asset",
	 * 		"description": "Asset id associated with external app",
	 * 		"type":        "int",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "member",
	 * 		"description": "Member id",
	 * 		"type":        "int",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "course",
	 * 		"description": "Course alias",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "offering",
	 * 		"description": "Offering alias",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "section",
	 * 		"description": "Section alias",
	 * 		"type":        "string", 
	 * 		"required":    false,
	 * 		"default":     '_Default'
	 * }
	 * @apiParameter {
	 * 		"name":        "data",
	 * 		"description": "{passed: int, score: int, details: string}",
	 * 		"type":        "string", 
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function saveTask()
	{
		// Require authentication and authorization
		//$this->authorizeOrFail();

		$asset_id   = Request::getInt('asset', 0);

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

		$member_id   = Request::getInt('member', 0);
		if (!$member_id)
		{
			$user_id = App::get('authn')['user_id'];

			if (!$user_id)
			{
				App::abort(500, 'No member ID and not logged in');
			}

			$member = Member::getInstance($user_id, 0, 0, $section_id);

			if (!$member_id = $member->get('id'))
			{
				App::abort(500, 'Failed to get course member ID');
			}
		}

		$data = Request::getString('data', '');
		$data = trim($data);

		if ($data)
		{
			$message = json_decode($data,true);
		}
		else
		{
			$jsondata = file_get_contents('php://input');
			$post = json_decode($jsondata,true);
			$message = $post['data'];
		}

		if (!$message)
		{
			App::abort(400, 'Missing data');
		}

		$db    = App::get('db');

		// Get timestamp
		$now = Date::toSql();

		// Save the external app details
		$xapp = new AssetXapp($db);
		$xapp->set('member_id', $member_id);
		$xapp->set('asset_id', $asset_id);
		$xapp->set('created', $now);
		$xapp->set('passed', (($message['passed']) ? 1 : 0));
		$xapp->set('details', $message['details']);
		if (!$xapp->store())
		{
			App::abort(500, $xapp->getError());
		}

		// Now set/update the gradebook 
		$gradebook = new GradeBook($db);
		$gradebook->loadByUserAndAssetId($member_id, $asset_id);

		if (isset($message['score']))
		{
			$score = $message['score'];
		}
		else
		{
			if (isset($message['passed']))
			{
				$score = ($message['passed']) ? 100 : 0;
			}
			else
			{
				$score = 0;
			}
		}

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
		$this->send([
			'success' => true, 
			'grade' => $gradebook->get('score')
		]);
	}
}
