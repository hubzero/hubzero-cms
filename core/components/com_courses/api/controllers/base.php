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

use Hubzero\Component\ApiController;
use Components\Courses\Models\Course;
use Request;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'course.php';

/**
 * API controller for the courses component
 */
class base extends ApiController
{
	/**
	 * Checks to ensure appropriate authorization
	 *
	 * @return  bool
	 * @throws  Exception
	 */
	protected function authorizeOrFail($action='manage')
	{
		// Make sure we have a valid user
		$this->requiresAuthentication();

		// Get the course id
		$this->course_id      = Request::getInt('course_id', 0);
		$this->offering_alias = Request::getCmd('offering', '');
		$this->section_id     = Request::getInt('section_id', '');

		// Load the course page
		$course   = Course::getInstance($this->course_id);
		$offering = $course->offering($this->offering_alias);
		$section  = $course->offering()->section($this->section_id);

		if (!$course->access($action))
		{
			App::abort(401, 'Unauthorized');
		}

		// Set the course for reuse later
		$this->course = $course;

		return true;
	}
}