<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Api;

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

		$vars['controller'] = 'groups';

		if (isset($segments[0]))
		{
			// /groups/{id|cn}
			if (is_numeric($segments[0]) || !in_array($segments[0], array('list', 'create')))
			{
				$vars['id'] = $segments[0];
				if (\App::get('request')->method() == 'GET')
				{
					$vars['task'] = 'read';
				}
			}
			// /groups/list
			// /groups/create
			else
			{
				$vars['task'] = $segments[0];
			}

			if (isset($segments[1]))
			{
				// /groups/{id|cn}/read
				// /groups/{id|cn}/update
				// /groups/{id|cn}/delete
				if (in_array($segments[1], array('read', 'update', 'delete')))
				{
					$vars['task'] = $segments[1];
				}
				// /groups/{id|cn}/{plugin}
				else
				{
					$vars['controller'] = 'plugins';
					$vars['task']       = 'index';
					$vars['active']     = $segments[1];

					if ($segments[1] == 'members')
					{
						$vars['controller'] = $segments[1];
					}

					// /groups/{id|cn}/{plugin}/list
					// /groups/{id|cn}/{plugin}/create
					// /groups/{id|cn}/{plugin}/{record}
					if (isset($segments[2]))
					{
						// /groups/{id|cn}/{plugin}/{record}
						if (is_numeric($segments[2]))
						{
							$vars['record_id'] = $segments[2];
							if (\App::get('request')->method() == 'GET')
							{
								$vars['task'] = 'read';
							}
							if (isset($segments[3]))
							{
								if (in_array($segments[3], array('read', 'update', 'delete')))
								{
									$vars['task'] = $segments[3];
								}
							}
						}
						// /groups/{id|cn}/{plugin}/list
						// /groups/{id|cn}/{plugin}/create
						else
						{
							$vars['task'] = $segments[2];
						}
					}
				}
			}
		}

		return $vars;
	}
}
