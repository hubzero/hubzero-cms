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

jimport('joomla.plugin.plugin');

/**
 * Courses Plugin class for related course
 */
class plgCoursesRelated extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

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

		$database =& JFactory::getDBO();

		//$now = date('Y-m-d H:i:s', time());

		$query  = "SELECT c.* 
					FROM #__courses AS c 
					JOIN #__courses_members AS m ON m.course_id=c.id AND m.student=0
					LEFT JOIN #__courses_roles AS r ON r.id=m.role_id
					WHERE r.alias='instructor' 
					AND m.user_id IN (" . implode(",", $ids) . ")
					AND m.student=0
					AND c.state=1
					AND c.id !=" . $database->Quote($course->get('id'));
	/*				AND (c.publish_up = '0000-00-00 00:00:00' OR c.publish_up <= " . $database->Quote($now) . ")
					AND (c.publish_down = '0000-00-00 00:00:00' OR c.publish_down >= " . $database->Quote($now) . ")";*/

		$database->setQuery($query);
		if (($courses = $database->loadObjectList()))
		{
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'courses',
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

