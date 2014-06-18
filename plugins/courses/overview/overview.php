<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Courses Plugin class for the course overview page
 */
class plgCoursesOverview extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onCourseViewAreas($course)
	{
		$area = array(
			'overview' => JText::_('PLG_COURSES_' . strtoupper($this->_name)),
		);
		return $area;
	}

	/**
	 * Return data on a course view (this will be some form of HTML)
	 *
	 * @param      object  $course Current course
	 * @param      string  $active Current active area
	 * @return     array
	 */
	public function onCourseView($course, $active=null)
	{
		// The output array we're returning
		$arr = array(
			'name'     => 'overview',
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($active))
		{
			if (!in_array($arr['name'], $active))
			{
				return $arr;
			}
		}
		else if ($active != $arr['name'])
		{
			return $arr;
		}

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'overview'
			)
		);
		$view->option     = JRequest::getCmd('option', 'com_courses');
		$view->controller = JRequest::getWord('controller', 'course');
		$view->course     = $course;

		$arr['html'] = $view->loadTemplate();

		// Return the output
		return $arr;
	}
}

