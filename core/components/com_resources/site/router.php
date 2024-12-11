<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Site;

use Hubzero\Component\Router\Base;
use Components\Resources\Models\Type;

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

		if (!empty($query['task']) && in_array($query['task'], array('new', 'draft', 'start', 'retract', 'delete', 'discard', 'remove', 'reorder', 'access')))
		{
			if (!empty($query['task']))
			{
				if ($query['task'] == 'start')
				{
					$query['task'] = 'draft';
				}
				$segments[] = $query['task'];
				unset($query['task']);
			}
			if (!empty($query['id']))
			{
				$segments[] = $query['id'];
				unset($query['id']);
			}
		}
		else
		{
			if (!empty($query['id']))
			{
				$segments[] = $query['id'];
				unset($query['id']);
			}
			if (!empty($query['alias']))
			{
				$segments[] = $query['alias'];
				unset($query['alias']);
			}
			if (!empty($query['active']))
			{
				$segments[] = $query['active'];
				unset($query['active']);
			}
			if (!empty($query['task']))
			{
				$segments[] = $query['task'];
				unset($query['task']);
			}
			if (!empty($query['file']))
			{
				$segments[] = $query['file'];
				unset($query['file']);
			}
			if (!empty($query['type']))
			{
				$segments[] = $query['type'];
				unset($query['type']);
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
		$vars = array();

		if (empty($segments[0]))
		{
			return $vars;
		}

		if (is_numeric($segments[0]))
		{
			$vars['id'] = $segments[0];
		}
		elseif (in_array($segments[0], array('browse', 'license', 'sourcecode', 'plugin')))
		{
			$vars['task'] = $segments[0];
		}
		elseif (in_array($segments[0], array('new', 'draft', 'start', 'retract', 'delete', 'discard', 'remove', 'reorder', 'access')))
		{
			$vars['task'] = $segments[0];
			$vars['controller'] = 'create';
			if (isset($segments[1]))
			{
				$vars['id'] = $segments[1];
			}
		}
		else
		{
			include_once dirname(__DIR__) . DS . 'models' . DS . 'type.php';

			$types = Type::getMajorTypes();

			// Normalize the title
			// This is so we can determine the type of resource to display from the URL
			// For example, /resources/teachingmaterials => Teaching Materials
			foreach ($types as $type)
			{
				if (trim($segments[0]) == $type->alias)
				{
					$vars['type'] = $segments[0];
					$vars['task'] = 'browsetags';
					break;
				}
			}

			if ($segments[0] == 'license')
			{
				$vars['task'] = $segments[0];
			}
			else
			{
				if (!isset($vars['type']))
				{
					$vars['alias'] = $segments[0];
				}
			}
		}

		if (!empty($segments[1]))
		{
			switch ($segments[1])
			{
				case 'download':
					$vars['task'] = 'download';
					if (isset($segments[2]))
					{
						$vars['file'] = rawurldecode( implode('/', array_slice($segments,2)) ); 
					}
				break;
				case 'play':
				case 'watch':
				case 'video':
				case 'citation':
				case 'feed':
					$vars['task'] = $segments[1];
					break;
				case 'feed.rss':
					$vars['task'] = 'feed';
					break;

				case 'license':
				case 'sourcecode':
					$vars['tool'] = $segments[1];
				break;

				default:
					if ($segments[0] == 'browse')
					{
						$vars['type'] = $segments[1];
					}
					else
					{
						$vars['active'] = $segments[1];
					}
				break;
			}
		}

		return $vars;
	}
}
