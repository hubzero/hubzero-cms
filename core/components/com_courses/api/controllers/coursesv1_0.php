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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
			'limit' => Request::getVar('limit', 25),
			'start' => Request::getVar('limitstart', 0)
		);

		// Search API hooks
		$admin = false;
		if (User::authorise('core.admin', 'com_users'))
		{
			$admin = true;
			$searchable = Request::getVar('searchable', false);
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
