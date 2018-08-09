<?php
// Declare the namespace.
namespace Components\Drwho\Site;

use Hubzero\Component\Router\Base;

// Most component client types (site, admin, api) will have
// an associated router. All routers must have a class name
// of `Router` and extend `Hubzero\Component\Router\Base`.
//
// Every router class implements two methods:
//     build - for turning querystrings into nice, SEF URLs
//     parse - for turning SEF URLs into the querystring params

/**
 * Routing class for the component
 */
class Router extends Base
{
	/**
	 * Build SEF route
	 * 
	 * Incoming data is an associative array of querystring
	 * values to build a SEF URL from.
	 *
	 * Example:
	 *    task=view&id=123
	 *
	 * Gets truned into:
	 *    $segments = array(
	 *       'task'=> 'view',
	 *       'id' => 123
	 *    );
	 *
	 * To generate:
	 *    url: /view/123
	 * 
	 * @param   array  &$query
	 * @return  array 
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

		return $segments;
	}

	/**
	 * Parse SEF route
	 *
	 * Incoming data is an array of URL segments.
	 *
	 * Example:
	 *    url: /view/123
	 *    $segments = array('view', '123')
	 * 
	 * @param   array  $segments
	 * @return  array
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
		}
		if (isset($segments[1])) 
		{
			$vars['id'] = $segments[1];
		}

		return $vars;
	}
}
