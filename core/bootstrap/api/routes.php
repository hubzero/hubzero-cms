<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

/*
|--------------------------------------------------------------------------
| SEF Build
|--------------------------------------------------------------------------
|
|
|
*/

/*
| Remove the base URI path. This will strip everything up to the base
*/
$router->rules('build')->append('content', function ($uri)
{
	// Set URI defaults
	$menu = \App::get('menu');

	// Get the itemid form the URI
	$itemid = $uri->getVar('Itemid');

	if (is_null($itemid))
	{
		if ($option = $uri->getVar('option'))
		{
			$item  = $menu->getItem($this->getVar('Itemid'));
			if (isset($item) && $item->component == $option)
			{
				$uri->setVar('Itemid', $item->id);
			}
		}
		else
		{
			if ($option = $this->getVar('option'))
			{
				$uri->setVar('option', $option);
			}

			if ($itemid = $this->getVar('Itemid'))
			{
				$uri->setVar('Itemid', $itemid);
			}
		}
	}
	else
	{
		if (!$uri->getVar('option'))
		{
			if ($item = $menu->getItem($itemid))
			{
				$uri->setVar('option', $item->component);
			}
		}
	}
	return $uri;
});

/*
| Component
|
| Build the route by component name
*/
$router->rules('build')->append('component', function ($uri)
{
	$route = $uri->getPath();
	$query = $uri->getQuery(true);
	$tmp   = '';

	if (!isset($query['option']) && !isset($query['Itemid']))
	{
		return $uri;
	}

	$query['option'] = \App::get('component')->canonical($query['option']);

	if ($router = \App::get('component')->router($query['option'], 'site'))
	{
		$query = $router->preprocess($query);
		$parts = $router->build($query);

		$tmp   = implode('/', $parts);
	}

	$tmp = isset($query['option']) ? substr($query['option'], 4) . '/' . $tmp : $tmp;

	$route .= $tmp ? '/' . $tmp : '';

	if (isset($item) && $query['option'] == $item->component)
	{
		unset($query['Itemid']);
	}
	unset($query['option']);

	//Set query again in the URI
	$uri->setQuery($query);
	$uri->setPath($route);

	return $uri;
});

/*
| SEF Rewrite
*/
$router->rules('build')->append('rewrite', function ($uri)
{
	// Get the path data
	$route = $uri->getPath();

	if (\App::get('config')->get('sef_suffix') && !(substr($route, -9) == 'index.php' || substr($route, -1) == '/'))
	{
		if ($format = $uri->getVar('format', 'html'))
		{
			$route .= '.' . $format;

			$uri->delVar('format');
		}
	}

	if (\App::get('config')->get('sef_rewrite'))
	{
		if ($route == 'index.php')
		{
			$route = '';
		}
		else
		{
			$route = str_replace('index.php/', '', $route);
		}
	}

	// Add basepath to the uri
	$uri->setPath(\App::get('request')->base(true) . '/' . $route);

	return $uri;
});

/*
| Limit start
*/
$router->rules('build')->append('groups', function ($uri)
{
	if ($limitstart = $uri->getVar('limitstart'))
	{
		$uri->setVar('start', (int) $limitstart);
		$uri->delVar('limitstart');
	}

	return $uri;
});

/*
|--------------------------------------------------------------------------
| Parse Rules
|--------------------------------------------------------------------------
|
| Rules to parse and route an incoming URL to a component
|
*/

/*
| Prepare URI
|
| Remove the base URI path. This will strip everything up to the bas
*/
$router->rules('parse')->append('prep', function ($uri)
{
	// Get the path
	$path = $uri->getPath();

	// Remove the base URI path.
	$path = substr_replace($path, '', 0, strlen(\App::get('request')->base(true)));

	// Remove prefix
	$path = str_replace('index.php', '', $path);

	// Set the route
	$uri->setPath(trim($path , '/'));
});

/*
| Match by component
|
| Match the first segment of the URI by component name. If a match is 
| found, the component's router will be loaded to continue parsing any
| further segments.
*/
$router->rules('parse')->append('component', function ($uri)
{
	$component = $uri->getVar('option');
	$segments  = explode('/', $uri->getPath());

	if (!$component)
	{
		if (count($segments) > 1 && $segments[0] == 'api')
		{
			$prefix = array_shift($segments);
		}
		$component = array_shift($segments);
	}

	if (!$component)
	{
		// No component name found.
		// Nothing else we can do here.
		return;
	}

	$uri->setVar('option', \App::get('component')->canonical($component));

	if (!count($segments))
	{
		return true;
	}

	if ($router = \App::get('component')->router($component, 'site'))
	{
		if ($vars = $router->parse($segments))
		{
			foreach ($vars as $key => $var)
			{
				$uri->setVar($key, $var);
			}
		}

		return true;
	}
});

/*
| Match by posted data
|
| Look for the option var in POST data
*/
$router->rules('parse')->append('post', function ($uri)
{
	if (\App::get('request')->method() == 'POST')
	{
		$component = App::get('request')->getCmd('option', '', 'post');
		$uri->setVar('option', $component);

		return true;
	}
});

