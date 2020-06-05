<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Site;

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

		if (!empty($query['year']))
		{
			$segments[] = $query['year'];
			unset($query['year']);
		}
		if (!empty($query['month']))
		{
			$segments[] = $query['month'];
			unset($query['month']);
		}
		if (!empty($query['day']))
		{
			$segments[] = $query['day'];
			unset($query['day']);
		}
		if (!empty($query['task']))
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
		if (!empty($query['id']))
		{
			$segments[] = $query['id'];
			unset($query['id']);
		}
		if (!empty($query['page']))
		{
			$segments[] = $query['page'];
			unset($query['page']);
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

		if (is_numeric($segments[0]))
		{
			$vars['year'] = $segments[0];
			$vars['task'] = 'year';

			if (isset($segments[1]) && is_numeric($segments[1]))
			{
				$vars['month'] = $segments[1];
				$vars['task'] = 'month';
			}

			if (isset($segments[2]) && is_numeric($segments[2]))
			{
				$vars['day']  = $segments[2];
				$vars['task'] = 'day';
			}

			if (isset($segments[3]))
			{
				$vars['task'] = $segments[3];
			}
		}
		else
		{
			$vars['task'] = $segments[0];
			if (isset($segments[1]) && is_numeric($segments[1]))
			{
				$vars['id'] = $segments[1];
			}
			if (isset($segments[2]))
			{
				$vars['page'] = $segments[2];
				if ($segments[2] == 'register')
				{
					$vars['task'] = 'register';
				}
			}
		}

		return $vars;
	}
}
