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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'EventsBuildRoute'
 *
 * Long description (if any) ...
 *
 * @param  array &$query Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function EventsBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['year'])) {
		$segments[] = $query['year'];
		unset($query['year']);
	}
	if (!empty($query['month'])) {
		$segments[] = $query['month'];
		unset($query['month']);
	}
	if (!empty($query['day'])) {
		$segments[] = $query['day'];
		unset($query['day']);
	}
	if (!empty($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (!empty($query['id'])) {
		$segments[] = $query['id'];
		unset($query['id']);
	}
	if (!empty($query['page'])) {
		$segments[] = $query['page'];
		unset($query['page']);
	}

	return $segments;
}

/**
 * Short description for 'EventsParseRoute'
 *
 * Long description (if any) ...
 *
 * @param  array $segments Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function EventsParseRoute($segments)
{
	$vars = array();

	if (empty($segments)) {
		return $vars;
	}

	if (is_numeric($segments[0])) {
		$vars['year'] = $segments[0];
		$vars['task'] = 'year';

		if (isset($segments[1]) && is_numeric($segments[1])) {
			$vars['month']  = $segments[1];
			$vars['task'] = 'month';
		}

		if (isset($segments[2]) && is_numeric($segments[2])) {
			$vars['day']  = $segments[2];
			$vars['task'] = 'day';
		}

		if (isset($segments[3])) {
			$vars['task'] = $segments[3];
		}
	} else {
		$vars['task'] = $segments[0];
		if (isset($segments[1]) && is_numeric($segments[1])) {
			$vars['id']  = $segments[1];
		}
		if (isset($segments[2])) {
			$vars['page']  = $segments[2];
			if ($segments[2] == 'register') {
				$vars['task'] = 'register';
			}
		}
	}

	return $vars;
}

?>