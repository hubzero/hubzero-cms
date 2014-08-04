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

		$database = JFactory::getDBO();

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
		if (($courses = $database->loadObjectList()))
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'overview'
				)
			);
			$view->option     = JRequest::getCmd('option', 'com_courses');
			$view->controller = JRequest::getWord('controller', 'course');
			$view->course     = $course;
			$view->name       = $this->_name;
			$view->courses    = $courses;
			$view->ids        = $ids;

			// Return the output
			return $view->loadTemplate();
		}
	}
}

