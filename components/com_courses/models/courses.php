<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'iterator.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'tags.php');

/**
 * Courses model class for a course
 */
class CoursesModelCourses extends \Hubzero\Base\Object
{
	/**
	 * CoursesTableCourse
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
	 * CoursesModelIterator
	 *
	 * @var object
	 */
	private $_courses = null;

	/**
	 * CoursesModelCourse
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
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CoursesTableCourse($this->_db);
	}

	/**
	 * Returns a reference to a course model
	 *
	 * This method must be invoked as:
	 *     $offering = CoursesModelCourse::getInstance($alias);
	 *
	 * @param      mixed $oid Course ID (int) or alias (string)
	 * @return     object CoursesModelCourse
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
	 * @return    object CoursesModelUnit
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
				$this->_course = CoursesModelCourse::getInstance($id);
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

		if (!($this->_courses instanceof CoursesModelIterator) || $clear)
		{
			if (($results = $this->_tbl->getRecords($filters)))
			{
				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesModelCourse($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_courses = new CoursesModelIterator($results);
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
				$results[$key] = new CoursesModelCourse($result);
			}
		}
		else
		{
			$results = array();
		}

		$courses = new CoursesModelIterator($results);

		return $courses;
	}

	/**
	 * Get a list of courses
	 *   Accepts an array of filters to build query from
	 *
	 * @param      array $filters Filters to build query from
	 * @return     mixed
	 */
	public function tags($what='cloud', $limit=null, $tagstring='')
	{
		$ct = new CoursesTags($this->_db);

		$tags = null;

		$what = strtolower(trim($what));
		switch ($what)
		{
			case 'array':
				$tags = $ct->getTags($limit);
			break;

			case 'string':
				$tags = $ct->getTagString($limit);
			break;

			case 'cloud':
				$tags = $ct->getTagCloud($limit, $tagstring);
			break;
		}

		return $tags;
	}

	/**
	 * Turn a string of tags to an array
	 *
	 * @param      string $tag Tag string
	 * @return     mixed
	 */
	public function parseTags($tag, $remove='')
	{
		if (is_array($tag))
		{
			$bunch = $tag;
		}
		else
		{
			$ct = new CoursesTags($this->_db);
			$bunch = $ct->_parse_tags($tag);
		}

		$tags = array();
		if ($remove)
		{
			foreach ($bunch as $t)
			{
				if ($remove == $t)
				{
					continue;
				}
				$tags[] = $t;
			}
		}
		else
		{
			return $bunch;
		}

		return $tags;
	}
}

