<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Site;

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

		if (!empty($query['task']))
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
		if (!empty($query['id']))
		{
			$segments[] = $query['id'];
			unset($query['id']);
		}
		if (!empty($query['format']))
		{
			$segments[] = $query['format'];
			unset($query['format']);
		}
		/*
		if (!empty($query['area']))
		{
			$segments[] = $query['area'];
			unset($query['area']);
		}
		*/
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
			$vars['task'] = $segments[0];
			switch ($vars['task'])
			{
				case 'import':
					$vars['controller'] = 'import';
					$vars['task'] = 'display';
				break;

				case 'import_upload':
				case 'import_review':
				case 'import_save':
				case 'import_saved':
					$vars['controller'] = 'import';
					$vars['task'] = str_replace('import_', '', $vars['task']);
				break;

				default:
					$vars['controller'] = 'citations';
				break;
			}
		}
		if (isset($segments[1]))
		{
			$vars['id'] = $segments[1];
			/*
			if (isset($segments[2]))
			{
				$vars['area'] = $segments[2];
			}
			*/
		}
		if (isset($segments[2]))
		{
			$vars['format'] = $segments[2];
		}
		return $vars;
	}
}
