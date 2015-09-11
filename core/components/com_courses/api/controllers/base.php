<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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