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
 * @author    HUBzero <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Turn querystring parameters into an SEF route
 *
 * @param  array &$query Querystring
 */
function CartBuildRoute(&$query)
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
function CartParseRoute($segments)
{
	$vars = array();

	$vars['controller'] = $segments[0];
	if (!empty($segments[1]))
	{
		$vars['task'] = $segments[1];
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

