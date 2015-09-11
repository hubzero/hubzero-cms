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

namespace Components\Courses\Models;

use Components\Courses\Tables;
use Hubzero\Base\Object;

require_once(__DIR__ . DS . 'course.php');
require_once(__DIR__ . DS . 'iterator.php');
require_once(__DIR__ . DS . 'tags.php');

/**
 * Courses model class for a course
 */
class Courses extends Object
{
	/**
	 * Tables\Course
	 *
	 * @var object
	 */
	private $_tbl = NULL;

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * \Components\Courses\Models\Iterator
	 *
	 * @var object
	 */
	private $_courses = null;

	/**
	 * \Components\Courses\Models\Course
	 *
	 * @var object
	 */
	private $_course = null;

	/**
	 * Constructor
	 *
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct()
	{
		$this->_db = \App::get('db');

		$this->_tbl = new Tables\Course($this->_db);
	}

	/**
	 * Returns a reference to a course model
	 *
	 * This method must be invoked as:
	 *     $offering = \Components\Courses\Models\Course::getInstance($alias);
	 *
	 * @param      mixed $oid Course ID (int) or alias (string)
	 * @return     object \Components\Courses\Models\Course
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new self();
		}

		return $instances[$oid];
	}

	/**
	 * Method to get/set the current unit
	 *
	 * @param     mixed $id ID or alias of specific unit
	 * @return    object \Components\Courses\Models\Unit
	 */
	public function course($id=null)
	{
		if (!isset($this->_course)
		 || ($id !== null && (int) $this->_course->get('id') != $id && (string) $this->_course->get('alias') != $id))
		{
			$this->_course = null;

			if (isset($this->_courses))
			{
				foreach ($this->courses() as $key => $course)
				{
					if ((int) $course->get('id') == $id || (string) $course->get('alias') == $id)
					{
						$this->_course = $course;
						break;
					}
				}
			}
			else
			{
				$this->_course = Course::getInstance($id);
			}
		}
		return $this->_course;
	}

	/**
	 * Get a list of courses
	 *   Accepts an array of filters to build query from
	 *
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $clear   Clear cached results?
	 * @return     mixed
	 */
	public function courses($filters=array(), $clear=false)
	{
		if (isset($filters['count']) && $filters['count'])
		{
			return $this->_tbl->getCount($filters);
		}

		if (!($this->_courses instanceof Iterator) || $clear)
		{
			if (($results = $this->_tbl->getRecords($filters)))
			{
				foreach ($results as $key => $result)
				{
					$results[$key] = new Course($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_courses = new Iterator($results);
		}

		return $this->_courses;
	}

	/**
	 * Get a list of courses
	 *   Accepts an array of filters to build query from
	 *
	 * @param      array $filters Filters to build query from
	 * @return     mixed
	 */
	public function userCourses($uid, $type='all', $limit=null, $start=0)
	{
		if (($results = $this->_tbl->getUserCourses($uid, $type, $limit, $start)))
		{
			foreach ($results as $key => $result)
			{
				$results[$key] = new Course($result);
			}
		}
		else
		{
			$results = array();
		}

		$courses = new Iterator($results);

		return $courses;
	}

	/**
	 * Get a list of courses
	 *   Accepts an array of filters to build query from
	 *
	 * @param      array $filters Filters to build query from
	 * @return     mixed
	 */
	public function tags($what='cloud', $filters=array(), $clear=false)
	{
		$ct = new Tags();

		return $ct->render($what, $filters, $clear);
	}

	/**
	 * Turn a string of tags to an array
	 *
	 * @param      string $tag Tag string
	 * @return     mixed
	 */
	public function parseTags($tag, $remove='')
	{
		$ct = new Tags();
		return $ct->parseTags($tag, $remove);
	}
}

