<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Whatsnew\Site;

use Hubzero\Component\Router\Base;

/**
 * Routing class for the component
 */
class Router extends Base
{
	/**
	 * Build the route for the component.
	 *
	 * @param   array  &$query  An array of URL arguments
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 */
	public function build(&$query)
	{
		$segments = array();

		if (!empty($query['period']))
		{
			$segments[] = $query['period'];
			unset($query['period']);
		}
		if (!empty($query['task']))
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
		if (!empty($query['controller']))
		{
			unset($query['controller']);
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 * @return  array  The URL attributes to be used by the application.
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
			$vars['period'] = $segments[0];
		}
		if (isset($segments[1]))
		{
			$vars['task'] = $segments[1];
		}

		return $vars;
	}
}
