<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Poll\Site;

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
		static $items;

		$segments = array();
		$itemid   = null;

		// Break up the poll id into numeric and alias values.
		if (isset($query['id']) && strpos($query['id'], ':'))
		{
			list($query['id'], $query['alias']) = explode(':', $query['id'], 2);
		}

		// Get the menu items for this component.
		if (!$items)
		{
			$menu      = \App::get('menu');
			$component = \Component::load('com_poll');
			$items     = $menu->getItems('component_id', $component->id);
		}

		// Search for an appropriate menu item.
		if (is_array($items))
		{
			// If only the option and itemid are specified in the query, return that item.
			if (!isset($query['view']) && !isset($query['id']) && !isset($query['catid']) && isset($query['Itemid']))
			{
				$itemid = (int) $query['Itemid'];
			}
			// Search for a specific link based on the criteria given.
			if (!$itemid)
			{
				foreach ($items as $item)
				{
					// Check if this menu item links to this view.
					if (isset($item->query['view']) && $item->query['view'] == 'poll'
						&& isset($query['view']) && $query['view'] != 'category'
						&& isset($item->query['id']) && $item->query['id'] == $query['id'])
					{
						$itemid = $item->id;
					}
				}
			}

			// If no specific link has been found, search for a general one.
			if (!$itemid)
			{
				foreach ($items as $item)
				{
					if (isset($query['view']) && $query['view'] == 'poll' && isset($item->query['view']) && $item->query['view'] == 'poll')
					{
						// Check for an undealt with newsfeed id.
						if (isset($query['id']))
						{
							// This menu item links to the newsfeed view but we need to append the newsfeed id to it.
							$itemid = $item->id;
							$segments[] = isset($query['alias']) ? $query['id'].':'.$query['alias'] : $query['id'];
							break;
						}
					}
				}
			}
		}

		// Check if the router found an appropriate itemid.
		if (!$itemid)
		{
			// Check if a id was specified.
			if (isset($query['id']))
			{
				if (isset($query['alias']))
				{
					$query['id'] .= ':' . $query['alias'];
				}

				// Push the id onto the stack.
				$segments[] = $query['id'];
				unset($query['id']);
				unset($query['alias']);
			}
			if (isset($query['view']) && $query['view'] == 'latest')
			{
				$segments[] = $query['view'];
			}
			unset($query['view']);
		}
		else
		{
			$query['Itemid'] = $itemid;

			// Remove the unnecessary URL segments.
			unset($query['view']);
			unset($query['id']);
			unset($query['catid']);
			unset($query['alias']);
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

		if ($segments[0] == 'latest')
		{
			$vars['task'] = 'latest';
			return $vars;
		}

		//Get the active menu item
		$menu  = \App::get('menu');
		$item  = $menu->getActive();

		$count = count($segments);

		//Standard routing for articles
		if (!isset($item))
		{
			$vars['id'] = $segments[$count - 1];
			$vars['task'] = 'results';
			return $vars;
		}

		// Count route segments
		$vars['id']   = $segments[$count - 1];
		$vars['task'] = 'results';

		return $vars;
	}
}
