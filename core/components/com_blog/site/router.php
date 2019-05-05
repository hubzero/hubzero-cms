<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Site;

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

		if (!empty($query['task']) && $query['task'] != 'feed.rss' && $query['task'] != 'feed')
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
		if (!empty($query['controller']))
		{
			if ($query['controller'] == 'media')
			{
				$segments[] = $query['controller'];
			}
			unset($query['controller']);
		}
		if (!empty($query['year']))
		{
			$segments[] = $query['year'];
			unset($query['year']);
		}
		if (!empty($query['month']))
		{
			$segments[] = $query['month'];
			unset($query['month']);
		}
		if (!empty($query['task']) && ($query['task'] == 'feed.rss' || $query['task'] == 'feed'))
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
		if (!empty($query['alias']))
		{
			$segments[] = $query['alias'];
			unset($query['alias']);
		}
		if (!empty($query['action']))
		{
			$segments[] = $query['action'];
			unset($query['action']);
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
			if (is_numeric($segments[0]))
			{
				$vars['year'] = $segments[0];
				$vars['task'] = 'browse';
			}
			else
			{
				if ($segments[0] == 'media')
				{
					$vars['controller'] = $segments[0];
				}
				$vars['task'] = $segments[0];
			}
		}
		if (isset($segments[1]))
		{
			if ($segments[1] == 'feed.rss')
			{
				$vars['task'] = 'feed';
			}
			else
			{
				$vars['month'] = $segments[1];
			}
		}
		if (isset($segments[2]))
		{
			if ($segments[2] == 'feed.rss')
			{
				$vars['task'] = 'feed';
			}
			else
			{
				$vars['alias'] = $segments[2];
				$vars['task'] = 'entry';
			}
		}
		if (isset($segments[3]))
		{
			if ($segments[2] == 'feed.rss')
			{
				$vars['task'] = 'feed';
			}
			else if ($segments[3] == 'comments.rss')
			{
				$vars['task'] = 'comments';
			}
			else if (strstr($segments[3], ':'))
			{
				$parts = explode(':', $segments[3]);
				$namespace = strtolower(trim($parts[0]));
				if (in_array($namespace, array('image', 'file')))
				{
					$vars['task'] = 'download';
					$vars['file'] = trim($parts[1]);
				}
			}
			else if ($segments[3] == 'editcomment')
			{
				$vars['action'] = $segments[3];
			}
			else
			{
				$vars['task'] = $segments[3];
			}
		}
		if (in_array($vars['task'], array('deletefile', 'deletefolder', 'upload', 'download')))
		{
			$vars['controller'] = 'media';
		}

		return $vars;
	}
}
