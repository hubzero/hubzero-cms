<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Api;

use Exception;
use Hubzero\Component\Router\Base;
use Lang;

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
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 * @return  array  The URL attributes to be used by the application.
	 */
	public function parse(&$segments)
	{
		$vars = array();

		if (!isset($segments[0]))
		{
			return $vars;
		}

		$method = \App::get('request')->method();

		// /support/{ticket}[/comments[/{comment}]] -- the documented comment
		// path and the shape the default router serves; a bare id is a ticket
		if (is_numeric($segments[0]))
		{
			array_unshift($segments, 'tickets');
		}

		$vars['controller'] = $segments[0];

		// The next url segment should be the id
		// Task should already be set by the loader
		if (isset($segments[1]))
		{
			if (is_numeric($segments[1]))
			{
				$vars['id'] = $segments[1];

				// /support/tickets/{ticket}/comments[/{comment}]
				if ($vars['controller'] == 'tickets' && isset($segments[2]) && $segments[2] == 'comments')
				{
					$vars['controller'] = 'comments';
					$vars['ticket']     = $segments[1];
					unset($vars['id']);

					if (isset($segments[3]) && is_numeric($segments[3]))
					{
						$vars['id'] = $segments[3];
						if ($method == 'GET')
						{
							$vars['task'] = 'read';
						}
					}
					else if ($method == 'GET')
					{
						$vars['task'] = 'list';
					}
				}
				// Read needs to be set explicttly because read, list, and the api docblock all use GETs
				else if ($method == 'GET')
				{
					$vars['task'] = 'read';
				}
			}
			else if ($segments[1] == 'list')
			{
				$vars['task'] = 'list';
			}
			else
			{
				throw new Exception(Lang::txt("COM_SUPPORT_TASK_NOT_FOUND"), 404);
			}
		}
		else
		{
			if ($method == 'GET')
			{
				$vars['task'] = 'list';
			}
		}

		return $vars;
	}
}
