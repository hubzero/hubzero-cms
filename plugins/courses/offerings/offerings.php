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
 * Courses Plugin class for course members
 */
class plgCoursesOfferings extends JPlugin
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
	 * Return the alias and name for this category of content
	 * 
	 * @return     array
	 */
	public function &onCourseViewAreas($course)
	{
		$area = array();
		if ($course->offerings(array('state' => 1, 'sort_Dir' => 'ASC'), true)->total() > 0)
		{
			switch ($this->params->get('plugin_access', 'anyone'))
			{
				case 'managers':
					$memberships = $course->offering()->membership();

					if (count($memberships) > 0)
					{
						foreach ($memberships as $membership)
						{
							if ($membership->get('student') == 0)
							{
								$area['offerings'] = JText::_('PLG_COURSES_' . strtoupper($this->_name));
								break;
							}
						}
					}
				break;

				case 'members':
					if (count($course->offering()->membership()) > 0)
					{
						$area['offerings'] = JText::_('PLG_COURSES_' . strtoupper($this->_name));
					}
				break;

				case 'registered':
					if (!JFactory::getUser()->get('guest'))
					{
						$area['offerings'] = JText::_('PLG_COURSES_' . strtoupper($this->_name));
					}
				break;

				case 'anyone':
				default:
					$area['offerings'] = JText::_('PLG_COURSES_' . strtoupper($this->_name));
				break;
			}
			return $area;
		}
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
			'name'     => 'offerings',
			'html'     => '',
			'metadata' => ''
		);

		//$this->option = JRequest::getVar('option', 'com_courses');
		//$this->course = $course;

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

		//Create user object
		//$juser =& JFactory::getUser();

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

		$arr['html'] = $view->loadTemplate();

		// Return the output
		return $arr;
	}
}

