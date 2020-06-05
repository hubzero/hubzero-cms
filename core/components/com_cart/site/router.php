<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Site;

use Hubzero\Component\Router\Base;

/**
 * Routing class for the component
 */
class Router extends Base
{

	/**
	 * Turn querystring parameters into an SEF route
	 *
	 * @param   array  &$query  Querystring
	 * @return  array
	 */
	public function build(&$query)
	{
		$segments = array();

		if (!empty($query['controller']))
		{
			/*if ($query['controller'] == 'orders')
			{
				$segments[] = $query['controller'];
			}*/
			$segments[] = $query['controller'];
			unset($query['controller']);
		}

		return $segments;
	}

	/**
	 * Parse a SEF route
	 *
	 * @param   array  $segments  Exploded route
	 * @return  array
	 */
	public function parse(&$segments)
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
				$vars['p' . ($index - 2)] = $value;
			}
		}

		return $vars;
	}
}
