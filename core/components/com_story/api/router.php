<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Api;

use Hubzero\Component\Router\Base;

/**
 * Routing class for the component's API
 */
class Router extends Base
{
	/**
	 * Build the route for the component
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
	 * Parse the segments of a URL
	 *
	 * @param   array  &$segments
	 * @return  array
	 */
	public function parse(&$segments)
	{
		$vars = array();

		if (!empty($segments[0]))
		{
			$vars['controller'] = array_shift($segments);
		}

		if (!empty($segments[0]))
		{
			$vars['task'] = array_shift($segments);
		}

		return $vars;
	}
}
