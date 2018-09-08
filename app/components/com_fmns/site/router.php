<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2018 HUBzero Foundation, LLC.
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
 * @author    M. Drew LaMar <drew.lamar@gmail.com>
 * @copyright Copyright 2005-2018 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */
 
namespace Components\Fmns\Site;

use Hubzero\Component\Router\Base;

/**
 * Routing class for the component
 */
class Router extends Base
{
	/**
	 * Build SEF route
	 * 
	 * Incoming data is an associative array of querystring
	 * values to build a SEF URL from.
	 *
	 * Example:
	 *    controller=characters&task=view&id=123
	 *
	 * Gets turned into:
	 *    $segments = array(
	 *       'controller' => 'characters',
	 *       'task'=> 'view',
	 *       'id' => 123
	 *    );
	 *
	 * To generate:
	 *    url: /characters/view/123
	 * 
	 * @param   array  &$query
	 * @return  array 
	 */
	public function build(&$query)
	{
		$segments = array();

		if (!empty($query['controller'])) 
		{
			$segments[] = $query['controller'];
			unset($query['controller']);
		}
		if (!empty($query['task'])) 
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}

		return $segments;
	}

	/**
	 * Parse SEF route
	 *
	 * Incoming data is an array of URL segments.
	 *
	 * Example:
	 *    url: /characters/view/123
	 *    $segments = array('characters', 'view', '123')
	 * 
	 * @param   array  $segments
	 * @return  array
	 */
	public function parse(&$segments)
	{
		$vars = array();

		if (empty($segments))
		{
			return $vars;
		}

		if (isset($segments[0])) 
		{
			$vars['controller'] = $segments[0];
		}
		if (isset($segments[1])) 
		{
			$vars['task'] = $segments[1];
		}

		return $vars;
	}
}
