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
 * @author    SIlya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_HZEXEC_') or die();

/**
 * Turn querystring parameters into an SEF route
 *
 * @param  array &$query Querystring
 */
function StorefrontBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['controller']))
	{
		unset($query['controller']);
	}

	return $segments;
}

/**
 * Parse a SEF route
 *
 * @param  array $segments Exploded route
 * @return array
 */
function StorefrontParseRoute($segments)
{
	$vars = array();

	$vars['controller'] = $segments[0];
	if (!empty($segments[1]))
	{
		$vars['task'] = $segments[1];

		if ($segments[0] == 'product')
		{
			$vars['task'] = 'display';
			$vars['product'] = $segments[1];
		}
	}

	foreach ($segments as $index => $value)
	{
		// skip first two segments -- these are controller and task
		if ($index < 2)
		{
			continue;
		}
		else
		{
			$vars['p' . ($index - 2)]	= $value;
		}
	}

	//print_r($vars);
	return $vars;
}
