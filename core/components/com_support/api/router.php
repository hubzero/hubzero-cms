<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Api;

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

		$vars['controller'] = 'tickets';

		// The v2 tickets controller documents its URIs as /support/tickets[/...];
		// the loader already takes the controller from that segment, so drop it
		// here or "tickets" was read as the task (POST /support/tickets and
		// GET /support/tickets/{id} answered "Task [...] not found")
		if (isset($segments[0]) && $segments[0] == 'tickets')
		{
			array_shift($segments);
		}

		if (isset($segments[0]))
		{
			if (is_numeric($segments[0]))
			{
				$vars['id']     = $segments[0];
				$vars['ticket'] = $segments[0]; // the v1 tickets controller reads 'ticket'
				$method = \App::get('request')->method();
				switch ($method)
				{
					case 'GET':
						$vars['task'] = 'read';
						break;
					case 'PUT':
						$vars['task'] = 'update';
						break;
					case 'DELETE':
						$vars['task'] = 'delete';
						break;
				}
			}
			else
			{
				$vars['task'] = $segments[0];
			}

			if (isset($segments[1]))
			{
				if ($segments[1] == 'comments')
				{
					// /support/{ticket}/comments[/list|/{comment}]: this always
					// answered "list", so the documented comment create, read,
					// update and delete could never be reached
					$vars['controller'] = $segments[1];
					$vars['ticket']     = $segments[0];
					unset($vars['task']);
					if (isset($segments[2]) && is_numeric($segments[2]))
					{
						$vars['comment'] = $segments[2];
						$method = \App::get('request')->method();
						if ($method == 'GET')
						{
							$vars['task'] = 'read';
						}
					}
					else if (isset($segments[2]))
					{
						$vars['task'] = $segments[2];
					}
					else if (\App::get('request')->method() == 'GET')
					{
						$vars['task'] = 'list';
					}
				}
				else
				{
					$vars['task'] = $segments[1];
				}
			}
		}

		return $vars;
	}
}
