<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Help\Site;

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

		//do we have a component
		if (!empty($query['component']))
		{
			$segments[] = $query['component'];
			unset($query['component']);
		}

		//do we have an extension
		if (!empty($query['extension']))
		{
			$segments[] = $query['extension'];
			unset($query['extension']);
		}

		//do we have a page
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

		//do we have a component
		if (isset($segments[0]))
		{
			$vars['component'] = 'com_' . $segments[0];
		}

		//if we have segements it easy
		if (count($segments) > 2)
		{
			$vars['extension'] = $segments[1];
			$vars['page']      = $segments[2];
		}
		elseif (isset($segments[1]))
		{
			$vars['page'] = $segments[1];
		}

		return $vars;
	}
}
