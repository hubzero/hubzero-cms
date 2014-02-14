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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'Hubzero_Course_Invite_Email'
 * 
 * Long description (if any) ...
 */
class CoursesHelper
{
	/**
	 * Short description for 'getPluginAccess'
	 * Long description (if any) ...
	 *
	 * @param string $get_plugin Parameter description (if any) ...
	 * @return mixed Return description (if any) ...
	 */
	public static function getPluginAccess($course, $get_plugin = '')
	{
		// Get plugins
		JPluginHelper::importPlugin('courses');
		$dispatcher =  JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		//then add overview to array
		$hub_course_plugins = $dispatcher->trigger('onCourseAreas', array());
		array_unshift($hub_course_plugins, array('name'=>'overview', 'title'=>'Overview', 'default_access'=>'anyone'));

		//array to store plugin preferences when after retrieved from db
		$active_course_plugins = array();

		//get the course plugin preferences
		//returns array of tabs and their access level (ex. [overview] => 'anyone', [messages] => 'registered')
		$course_plugins = $course->get('plugins');

		if ($course_plugins)
		{
			$course_plugins = explode(',', $course_plugins);

			foreach ($course_plugins as $plugin)
			{
				$temp = explode('=', trim($plugin));

				if ($temp[0])
				{
					$active_course_plugins[$temp[0]] = trim($temp[1]);
				}
			}
		}

		//array to store final course plugin preferences
		//array of acceptable access levels
		$course_plugin_access = array();
		$acceptable_levels = array('nobody', 'anyone', 'registered', 'members');

		//if we have already set some
		if ($active_course_plugins)
		{
			//for each plugin that is active on the hub
			foreach ($hub_course_plugins as $hgp)
			{
				//if course defined access level is not an acceptable value or not set use default value that is set per plugin
				//else use course defined access level
				if (!isset($active_course_plugins[$hgp['name']]) || !in_array($active_course_plugins[$hgp['name']], $acceptable_levels))
				{
					$value = $hgp['default_access'];
				}
				else
				{
					$value = $active_course_plugins[$hgp['name']];
				}

				//store final  access level in array of access levels
				$course_plugin_access[$hgp['name']] = $value;
			}
		}
		else
		{
			//for each plugin that is active on the hub
			foreach ($hub_course_plugins as $hgp)
			{
				$value = $hgp['default_access'];

				//store final  access level in array of access levels
				$course_plugin_access[$hgp['name']] = $value;
			}
		}

		//if we wanted to return only a specific level return that otherwise return all access levels
		if ($get_plugin != '')
		{
			return $course_plugin_access[$get_plugin];
		}
		else
		{
			return $course_plugin_access;
		}
	}
}