<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Site;

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

		if (!empty($query['category']))
		{
			$segments[] = $query['category'];
			unset($query['category']);
		}

		if (!empty($query['rid']))
		{
			$segments[] = $query['rid'];
			unset($query['rid']);
		}

		if (!empty($query['id']))
		{
			$segments[] = $query['id'];
			unset($query['id']);
		}

		if (!empty($query['task']))
		{
			if ($query['task'] != 'wishlist')
			{
				$segments[] = $query['task'];
			}
			unset($query['task']);
		}

		if (!empty($query['wishid']))
		{
			$segments[] = $query['wishid'];
			unset($query['wishid']);
		}

		if (!empty($query['file']))
		{
			$segments[] = $query['file'];
			unset($query['file']);
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

		// Count route segments

		if (empty($segments[0]))
		{
			// default to main wish list
			$vars['task'] = 'wishlist';
			$vars['rid'] = 1;
			$vars['category'] = 'general';
			return $vars;
		}

		if (intval($segments[0]) && empty($segments[1]))
		{
			// we have a specific list id requested
			$vars['task'] = 'wishlist';
			$vars['id'] = $segments[0];
			return $vars;
		}
		else if (!intval($segments[0]) && empty($segments[1]))
		{
			// some general task
			$vars['task'] = $segments[0];
			return $vars;
		}

		if (!empty($segments[1]))
		{
			if (intval($segments[0]))
			{
				// we have a specific list id requested
				$vars['id'] = $segments[0];
				$vars['task'] = $segments[1];
				if (!empty($segments[2]))
				{
					$vars['wishid'] = $segments[2];
					if (!empty($segments[3]))
					{
						$vars['file'] = $segments[3];
						$vars['task'] = 'download';
					}
				}
			}
			else
			{
				switch ($segments[0])
				{
					case 'rateitem':
						$vars['task'] = 'rateitem';
						$vars['id'] = $segments[1];
						return $vars;
					break;
					case 'saveplan':
						$vars['task'] = 'saveplan';
						$vars['wishid'] = $segments[1];
						return $vars;
					break;
					case 'wish':
						$vars['task'] = 'wish';
						$vars['wishid'] = $segments[1];
						return $vars;
					break;
					default:
						if (count($segments) >= 3 && $segments[0] == 'wishlist')
						{
							$component = array_shift($segments);
						}
						// we got a category
						$vars['category'] = $segments[0];
						$vars['rid'] = $segments[1];

						if (!empty($segments[2])
							&& !(intval($segments[2])))
						{
							$vars['task'] = $segments[2];
						}
						elseif (!empty($segments[2])
							&& intval($segments[2]))
						{
							//make assumption we are viewing a wish
							$vars['task'] = 'wish';
							$vars['wishid'] =  $segments[2];
						}
						if (!empty($segments[3]))
						{
							$vars['wishid'] = $segments[3];
						}
						if (!empty($segments[4]))
						{
							$vars['file'] = $segments[4];
							$vars['task'] = 'download';
						}
					break;
				}
			}

			return $vars;
		}

		return $vars;
	}
}
