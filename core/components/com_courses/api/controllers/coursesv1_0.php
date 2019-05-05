<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Api\Controllers;

use Components\Courses\Tables\Course;
use Components\Courses\Models\Course as CourseModel;
use App;
use Config;
use Request;
use Date;
use Component;
use stdClass;

require_once __DIR__ . DS . 'base.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'unit.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'assetgroup.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'course.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'course.php';

/**
 * API controller for the course units
 */
class Coursesv1_0 extends base
{
	/**
	 * Lists course catalog
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/list
	 * @apiParameter {
	 * 		"name":        "limit",
	 * 		"description": "Number of records to return",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     25 
	 * }
	 * @apiParameter {
	 * 		"name":        "limitstart",
	 * 		"description": "Offset of Records to return",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0 
	 * }
	 * @return    void
	 */
	public function listTask()
	{
		$filters = array(
			'limit' => Request::getInt('limit', 25),
			'start' => Request::getInt('limitstart', 0)
		);

		// Search API hooks
		$admin = false;
		if (User::authorise('core.admin', 'com_users'))
		{
			$admin = true;
			$searchable = Request::getBool('searchable', false);
		}

		$db = App::get('db');
		$courses = new Course($db);
		$total   = $courses->getCount();
		$courses = $courses->getRecords($filters);

		$records = array();
		foreach ($courses as $course)
		{
			if ($admin == true && isset($searchable))
			{
				$entry = new stdClass;
				$entry->hubtype = 'course';
				$entry->title = $course->title;
				$entry->description = $course->blurb;
				$entry->raw_content = $course->description;
				$entry->url = '/courses/' . $course->alias;
				$entry->id = 'course-' . $course->id;

				$model = new CourseModel($course->id);
				$managers = $model->managers();
				$instructors = $model->instructors();

				$owners = array();
				foreach ($managers as $manager)
				{
					array_push($owners, $manager->get('user_id'));
				}

				$authors = array();
				foreach ($instructors as $instructor)
				{
					array_push($owners, $instructor->get('user_id'));
					if ($instructor->get('name') != '')
					{
						array_push($authors, $instructor->get('name'));
					}
				}

				$entry->author = $authors;
				$entry->owner_type = 'user';
				$entry->owner = $owners;
				if ($course->access != 1)
				{
					$entry->access_level = 'private';
				}
				else
				{
					$entry->access_level = 'public';
				}

				array_push($records, $entry);
			}
			else
			{
				$entry = new stdClass;
				$entry->title = $course->title;
				$entry->description = $course->blurb;
				array_push($records, $entry);
			}
		}

		$response = new stdClass;
		$response->courses = $records;
		$response->total = $total;
		$response->success = true;
		$this->send($response);
	}
}
