<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tags\Site;

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

		if (!empty($query['tag']))
		{
			$segments[] = $query['tag'];
			unset($query['tag']);
		}
		if (!empty($query['area']))
		{
			$segments[] = $query['area'];
			unset($query['area']);
		}
		if (!empty($query['task']))
		{
			if ($query['task'] != 'edit')
			{
				$segments[] = $query['task'];
				unset($query['task']);
			}
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
			if ($segments[0] == 'browse' || $segments[0] == 'delete' || $segments[0] == 'edit')
			{
				$vars['task'] = $segments[0];
			}
			else
			{
				$vars['tag']  = $segments[0];
				$vars['task'] = 'view';
			}
		}
		if (isset($segments[1]))
		{
			if ($segments[1] == 'feed' || $segments[1] == 'feed.rss')
			{
				$vars['task'] = $segments[1];
			}
			else
			{
				$vars['area'] = $segments[1];
			}
		}
		if (isset($segments[2]))
		{
			if (in_array($segments[2], array('delete', 'edit', 'feed', 'feed.rss')))
			{
				$vars['task'] = $segments[2];
			}
		}

		return $vars;
	}
}
