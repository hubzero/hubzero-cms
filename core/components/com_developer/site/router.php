<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Site;

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
			if ($query['controller'] == 'applications')
			{
				$segments[] = 'api';
			}
			$segments[] = $query['controller'];
			unset($query['controller']);
		}

		if (!empty($query['id']))
		{
			$segments[] = $query['id'];
			unset($query['id']);
		}

		if (!empty($query['task']) && $query['task'] != 'view')
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}

		if (!empty($query['active']))
		{
			$segments[] = $query['active'];
			unset($query['active']);
		}

		if (!empty($query['version']))
		{
			$segments[] = $query['version'];
			unset($query['version']);
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
			$vars['controller'] = $segments[0];
		}

		if (isset($segments[1]))
		{
			$vars['task'] = $segments[1];

			if ($segments[1] == 'applications')
			{
				$vars['controller'] = 'applications';

				if (isset($segments[2]))
				{
					if (is_numeric($segments[2]))
					{
						$vars['id'] = $segments[2];

						// handle third segment which can either be
						// section or task
						$vars['task'] = 'view';
						$vars['active'] = 'details';
						if (isset($segments[3]))
						{
							switch ($segments[3])
							{
								case 'edit':
								case 'revoke':
								case 'revokeall':
								case 'createPersonalAccess':
								case 'removemember':
									$vars['task'] = $segments[3];
								break;

								default:
									$vars['active'] = $segments[3];
							}
						}
					}
					else
					{
						$vars['task'] = $segments[2];
					}
				}
			}
			else if ($segments[1] == 'oauth')
			{
				$vars['controller'] = 'oauth';

				if (isset($segments[2]))
				{
					$vars['task'] = $segments[2];
				}
			}
		}

		// api documentation versioning
		if (isset($vars['task']) && $vars['task'] == 'docs' && isset($segments[2]))
		{
			$vars['version'] = $segments[2];
		}
		if (isset($vars['task']) && $vars['task'] == 'endpoint' && isset($segments[2]))
		{
			$vars['active'] = $segments[2];
			if (isset($segments[3]))
			{
				$vars['version'] = $segments[3];
			}
		}

		return $vars;
	}
}
