<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Site;

use Hubzero\Component\Router\Base;

class Router extends Base
{
	public function build(&$query)
	{
		$segments = [];
		$queryParams = ['controller', 'task', 'id'];

		foreach ($queryParams as $param)
		{
			if (!empty($query[$param]))
			{
				$segments[] = $query[$param];
				unset($query[$param]);
			}
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
		if (isset($segments[2]))
		{
			$vars['task'] = $segments[2];
		}

		return $vars;
	}
}
