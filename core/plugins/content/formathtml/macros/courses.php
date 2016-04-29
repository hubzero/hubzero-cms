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

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'courses.php';

/**
 * Wiki macro class for displaying hello world
 */
class Courses extends Macro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['html']  = '<p>Displays a list of published courses on the hub. Any of the parameters can be combined in one macro.</p>';
		$txt['html'] .= '<p>Examples:</p>';
		$txt['html'] .= '<ul>';
		$txt['html'] .= '<li><pre>[[Courses()]]</pre></li>';
		$txt['html'] .= '<li><pre>[[Courses(limit=3)]] - Displays a list of 3 courses</pre></li>';
		$txt['html'] .= '<li><pre>[[Courses(tag=testing)]] - Displays courses tagged "testing"</pre></li>';
		$txt['html'] .= '<li><pre>[[Courses(group=testgroup)]] - Displays courses owned by the group "testgroup"</pre></li>';
		$txt['html'] .= '<li><pre>[[Courses(instructor=testuser)]] - Displays courses where one of the instructors is "testuser"</pre></li>';
		$txt['html'] .= '<li><pre>[[Courses(hideinstructors=1)]] - Displays courses with instructors hidden</pre></li>';
		$txt['html'] .= '<li><pre>[[Courses(hidedescription=1)]] - Displays courses with course descriptions hidden</pre></li>';
		$txt['html'] .= '<li><pre>[[Courses(showdates=1)]] - Displays a list of courses with the section start & end dates.</pre>';
		$txt['html'] .= '<ul>';
		$txt['html'] .= '<li>You have the ability to control which offering & section dates get outputted by passing the offering & section parameters:</li>';
		$txt['html'] .= '<li><pre>[[Courses(showdates=1,offering=testoffering)]]<br />- Displays dates for the "testoffering" offering, using the "__default" section.</pre></li>';
		$txt['html'] .= '<li><pre>[[Courses(showdates=1,offering=testoffering,section=testsection)]]<br />- Displays dates for the "testoffering" offering, using the "testsection" section.</pre></li>';
		$txt['html'] .= '</ul></li>';
		$txt['html'] .= '</ul>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		// get & render courses
		return $this->_renderCourses($this->_getCourses());
	}

	/**
	 * Get a list of courses
	 *
	 * @return \Hubzero\Base\ItemList
	 */
	private function _getCourses()
	{
		// Filters for courses
		$filters = array(
			'state'    => 1,
			'group'    => $this->_getArg('group', ''),
			'tag'      => $this->_getArg('tag', ''),
			'limit'    => $this->_getArg('limit', 5),
			'sort'     => 'title',
			'sort_Dir' => 'ASC'
		);

		// make sure to replace group cname with group id
		if (isset($filters['group']))
		{
			$group = \Hubzero\User\Group::getInstance($filters['group']);
			if ($group)
			{
				$filters['group_id'] = $group->get('gidNumber');
				unset($filters['group']);
			}
		}

		// instantiate courses model
		$model = \Components\Courses\Models\Courses::getInstance();

		// get a list of courses
		// make sure to clear in case we have more then one
		$courses = $model->courses($filters, true);

		// return courses
		return $courses;
	}

	/**
	 * Render Courses List
	 *
	 * @param  array  $courses  Courses list
	 * @return string
	 */
	private function _renderCourses($courses = array())
	{
		$html = '<div class="course-list">';

		if ($courses->count() > 0)
		{
			foreach ($courses as $course)
			{
				// get instructors
				$instructors = $course->instructors();

				// check to see if we want to limit by instructor
				if ($this->_getArg('instructor', null))
				{
					// get an array of instructor uid
					$instructorIds = array_map(function($instructor)
					{
						return $instructor->get('user_id');
					}, $instructors);

					// get the profile from the instructor param
					$profile = \User::getInstance($this->_getArg('instructor'));
					if ($profile->get('id'))
					{
						if (!in_array($profile->get('id'), $instructorIds))
						{
							continue;
						}
					}
				}

				// show dates for first offerings first section
				if ($this->_getArg('showdates', 0))
				{
					$offeringParam = $this->_getArg('offering', '');
					$sectionParam  = $this->_getArg('section', '__default');

					// did we want a specific offering?
					// otherwise jsut grab first
					if ($offeringParam)
					{
						//get the offering based on offering alias
						$offering = new \Components\Courses\Models\Offering($offeringParam, $course->get('id'));
					}
					else
					{
						$offering = $course->offerings()->fetch('first');
					}

					// if we have an offering
					if (isset($offering))
					{
						// load the section (default is __default)
						$section = $offering->section($sectionParam);

						// if we have section
						if ($section->get('id'))
						{
							$html .= '<span class="entry-time">';
							$html .= \Date::of($section->get('start_date'))->toLocal('F d') . ' - ';
							$html .= \Date::of($section->get('end_date'))->toLocal('F d, Y');
							$html .= '</span><br />';
						}
					}
				}

				// add course title with link
				$html .= '<a class="entry-title" href="' . $course->link() . '">' . htmlentities(stripslashes($course->get('title'))) . '</a><br />';

				// do we have any instructors
				// also make sure we dont want to hide instructors
				if (count($instructors) > 0 && !(bool) $this->_getArg('hideinstructors'))
				{
					$instr = array();
					foreach ($instructors as $instructor)
					{
						$profile = \User::getInstance($instructor->get('user_id'));
						if ($profile->get('id'))
						{
							$instr[] = '<a href="' . \Route::url('index.php?option=com_members&id=' . $profile->get('id')) . '">' . htmlentities(stripslashes($profile->get('name'))) . '</a>';
						}

					}
					$html .= '<span class="entry-details">Instructors: ' . implode(', ', $instr) . '</span>';
				}

				// make sure we dont want to hide the description
				if (!(bool) $this->_getArg('hidedescription'))
				{
					$html .= '<p>' . \Hubzero\Utility\String::truncate(stripslashes($course->get('blurb')), 200) . '</p>';
				}
			}
		}
		else
		{
			$html .= '<em>' . \Lang::txt('Sorry, there were no courses matching your search.') . '</em>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Get Arg by search
	 *
	 * @param  string $arg     Argument to search for
	 * @param  string $default Default value if no match is found
	 * @return string
	 */
	private function _getArg($arg, $default = '')
	{
		// check to see if we have an arg matching our search
		if (preg_match('/' . $arg . '=([^,|\s]*)/', $this->args, $matches))
		{
			return trim($matches[1]);
		}

		// return default
		return $default;
	}
}