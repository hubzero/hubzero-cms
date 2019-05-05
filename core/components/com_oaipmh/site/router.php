<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oaipmh\Site;

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

		if (isset($query['task']))
		{
			if ($query['task'])
			{
				$segments[] = $query['task'];
				unset($query['task']);
			}

			if (isset($query['stylesheet']))
			{
				if ($query['stylesheet'])
				{
					$segments[] = $query['stylesheet'];
					unset($query['stylesheet']);
				}
			}
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
		$vars  = array();

		if (!empty($segments[0]))
		{
			$vars['task'] = $segments[0];
			if ($vars['task'] == 'stylesheet')
			{
				$vars['format'] = 'raw';
			}

			if (!empty($segments[1]))
			{
				$vars['stylesheet'] = $segments[1];
			}
		}

		return $vars;
	}
}
