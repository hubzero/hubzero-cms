<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Api;

use Hubzero\Component\Router\Base;

class Router extends Base

{

	/**
	 * Build API URL
	 *
	 * @param   array  &$query  URL parameters
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
	 * Parse URL segments
	 *
	 * @param   array  &$segments  URL segments
	 * @return  array
	 */
	public function parse(&$segments)
	{
		$vars = array();

		if (isset($segments[0]))
		{
			$vars['controller'] = $segments[0];
		}
		if (isset($segments[1]))
		{
			if (is_numeric($segments[1]))
			{
				$vars['id'] = $segments[1];
			}
			else
			{
				$vars['task'] = $segments[1];
			}
		}

		return $vars;
	}

}
