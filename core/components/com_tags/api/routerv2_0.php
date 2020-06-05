<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tags\Api;

use Hubzero\Component\Router\Base;

class Router extends Base
{

	public function build(&$query)
	{
		$segments = [];

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

	public function parse(&$segments)
	{
		$vars = [];

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
