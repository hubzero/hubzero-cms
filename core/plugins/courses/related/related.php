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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Courses Plugin class for related course
 */
class plgCoursesRelated extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return data on a course view (this will be some form of HTML)
	 *
	 * @param      object  $course Current course
	 * @param      string  $active Current active area
	 * @return     array
	 */
	public function onCourseViewAfter($course)
	{
		$instructors = $course->instructors();
		if (count($instructors) <= 0)
		{
			return;
		}

		$ids = array();
		foreach ($instructors as $instructor)
		{
			$ids[] = (int) $instructor->get('user_id');
		}

		$database = App::get('db');

		$query  = "SELECT DISTINCT c.*
					FROM `#__courses` AS c
					JOIN `#__courses_members` AS m ON m.course_id=c.id AND m.student=0
					LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
					WHERE r.alias='instructor'
					AND m.user_id IN (" . implode(",", $ids) . ")
					AND m.student=0
					AND c.state=1
					AND c.id !=" . $database->Quote($course->get('id')) . " LIMIT " . (int) $this->params->get('display_limit', 3);

		$database->setQuery($query);
		if ($courses = $database->loadObjectList())
		{
			$view = $this->view('default', 'overview');
			$view->option     = Request::getCmd('option', 'com_courses');
			$view->controller = Request::getWord('controller', 'course');
			$view->course     = $course;
			$view->name       = $this->_name;
			$view->courses    = $courses;
			$view->ids        = $ids;

			// Return the output
			return $view->loadTemplate();
		}
	}
}

