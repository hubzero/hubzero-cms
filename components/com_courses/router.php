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
 * Turn querystring parameters into an SEF route
 * 
 * @param  array &$query Querystring
 */
function CoursesBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['controller'])) 
	{
		unset($query['controller']);
	}

	if (!empty($query['gid'])) 
	{
		$segments[] = $query['gid'];
		unset($query['gid']);
	}
	if (!empty($query['offering'])) 
	{
		$segments[] = $query['offering'];
		unset($query['offering']);
	}
	if (!empty($query['active'])) 
	{
		$segments[] = $query['active'];
		if ($query['active'] == '' && !empty($query['task'])) 
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
		unset($query['active']);
	} 
	else 
	{
		if ((empty($query['scope']) || $query['scope'] == '') && !empty($query['task']))
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
	}
	if (!empty($query['unit'])) 
	{
		$segments[] = $query['unit'];
		unset($query['unit']);
	}
	if (!empty($query['b'])) 
	{
		$segments[] = $query['b'];
		unset($query['b']);
	}
	if (!empty($query['c'])) 
	{
		$segments[] = $query['c'];
		unset($query['c']);
	}

	return $segments;
}

/**
 * Parse a SEF route
 * 
 * @param  array $segments Exploded route
 * @return array 
 */
function CoursesParseRoute($segments)
{
	$vars = array();

	if (empty($segments))
	{
		return $vars;
	}

	if (isset($segments[0])) 
	{
		if (in_array($segments[0], array('intro', 'browse'))) 
		{
			$vars['controller'] = 'courses';
			$vars['task'] = $segments[0];
		}
		else 
		{
			if ($segments[0] == 'new')
			{
				$vars['task'] = $segments[0];
			}
			else
			{
				$vars['gid'] = $segments[0];
				$vars['task'] = 'display';
			}
			$vars['controller'] = 'course';
		}
	}

	/*if (isset($vars['gid']))
	{
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'offering.php');
		$inst = new CoursesTableInstance(JFactory::getDBO());
		$insts = $inst->getCourseInstances(array(
			'course_alias' => $vars['gid']
		));
		//$course = CoursesCourse::getInstance($vars['gid']);
		if ($insts && count($insts) == 1)
		{
			JRequest::setVar('offering', $insts[0]->alias);
			$vars['offering'] = $insts[0]->alias;
			$vars['controller'] = 'offering';
		}
	}*/

	if (isset($segments[1])) 
	{
		$vars['controller'] = 'course';
		switch ($segments[1])
		{
			case 'overview':
			case 'discussions':
			case 'calendar':
			case 'messages':
			case 'enrollment':
			case 'syllabus':
				if (!isset($vars['offering']) && isset($vars['gid']))
				{
					require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'instance.php');
					$inst = new CoursesTableInstance(JFactory::getDBO());
					$insts = $inst->getCourseInstances(array('course_alias' => $vars['gid']));
					if ($insts && count($insts) == 1)
					{
						JRequest::setVar('offering', $insts[0]->alias);
						$vars['offering'] = $insts[0]->alias;
						//$vars['task'] = 'offering';
						$vars['active'] = $segments[1];
						$vars['controller'] = 'offering';
					}
				}
			break;

			case 'edit':
			case 'delete':
			case 'join':
			case 'accept':
			case 'cancel':
			case 'invite':
			case 'customize':
			case 'manage':
				if(isset($segments[2]))
				{
					$vars['task'] = 'editoutline';
					$vars['offering'] = $segments[2];
				}
				$vars['controller'] = 'offering';
			break;

			case 'editoutline':
			case 'offerings':
			//case 'managemodules':
			case 'ajaxupload':
				$vars['task'] = $segments[1];
				$vars['controller'] = 'media';
			break;
			default:
				$vars['offering'] = $segments[1];
				$vars['controller'] = 'offering';
			break;
		}
	}

	if (isset($segments[2])) 
	{
		$vars['active'] = $segments[2];
		$vars['controller'] = 'offering';
	}
	if (isset($segments[3])) 
	{
		$vars['unit'] = $segments[3];
	}
	if (isset($segments[4])) 
	{
		$vars['group'] = $segments[4];
	}
	if (isset($segments[5])) 
	{
		$vars['asset'] = $segments[5];
	}

	return $vars;
}

