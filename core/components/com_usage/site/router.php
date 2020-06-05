<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Usage\Site;

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

		if (!empty($query['task']))
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
		if (!empty($query['period']))
		{
			$segments[] = $query['period'];
			unset($query['period']);
		}
		if (!empty($query['type']))
		{
			$segments[] = $query['type'];
			unset($query['type']);
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

		$vars['task'] = $segments[0];
		if (isset($segments[0]))
		{
			switch ($segments[0])
			{
				case 'maps':
					if (isset($segments[1]))
					{
						$vars['type'] = $segments[1];
					}
				break;
				case 'overview':
				default:
					if (isset($segments[1]))
					{
						$vars['period'] = $segments[1];
					}
				break;
			}
		}

		return $vars;
	}
}
