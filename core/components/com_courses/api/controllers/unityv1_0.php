<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'asset.unity.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'grade.book.php';

/**
 * API controller for the time component
 */
class Unityv1_0 extends base
{
	/**
	 * Processes grade save from unity app
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/unity/save
	 * @apiParameter {
	 * 		"name":        "referrer",
	 * 		"description": "Host page",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "$_SERVER['HTTP_REFERER']"
	 * }
	 * @apiParameter {
	 * 		"name":        "payload",
	 * 		"description": "Score notes/content",
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

		// Parse some things out of the referer
		$referer = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : Request::getString('referrer');
		preg_match('/\/asset\/([[:digit:]]*)/', $referer, $matches);

		if (!$asset_id = $matches[1])
		{
			App::abort(400, 'Failed to get asset ID');
		}

		// Get course info...this seems a little wonky
		preg_match('/\/courses\/([[:alnum:]\-\_]*)\/([[:alnum:]\:\-\_]*)/', $referer, $matches);

		$course_alias   = $matches[1];
		$offering_alias = $matches[2];
		$section_alias  = null;

		if (strpos($offering_alias, ":"))
		{
			$parts = explode(":", $offering_alias);
			$offering_alias = $parts[0];
			$section_alias  = $parts[1];
		}

		$course = Course::getInstance($course_alias);
		$course->offering($offering_alias);
		$course->offering()->section($section_alias);
		$section_id = $course->offering()->section()->get('id');

		$member = Member::getInstance($user_id, 0, 0, $section_id);

		if (!$member_id = $member->get('id'))
		{
			App::abort(500, 'Failed to get course member ID');
		}

		if (!$data = Request::getString('payload', ''))
		{
			App::abort(400, 'Missing payload');
		}

		// Get the key and IV - Trim the first xx characters from the payload for IV
		$key  = $course->config()->get('unity_key', 0);
		$iv   = substr($data, 0, 32);
		$data = substr($data, 32);

		$message = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($data), MCRYPT_MODE_CBC, $iv);
		$message = trim($message);
		$message = json_decode($message);

		if (!$message || !is_object($message))
		{
			App::abort(500, 'Failed to decode message');
		}

		// Get timestamp
		$now = Date::toSql();

		// Save the unity details
		$unity = new AssetUnity($this->db);
		$unity->set('member_id', $member_id);
		$unity->set('asset_id', $asset_id);
		$unity->set('created', $now);
		$unity->set('passed', (($message->passed) ? 1 : 0));
		$unity->set('details', $message->details);
		if (!$unity->store())
		{
			App::abort(500, $unity->getError());
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
