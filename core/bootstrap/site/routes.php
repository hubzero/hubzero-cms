<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

/*
|--------------------------------------------------------------------------
| SEF Build
|--------------------------------------------------------------------------
|
| Remove the base URI path. This will strip everything up to the bas
|
*/
$router->rules('build')->append('content', function ($uri)
{
	// Set URI defaults
	$menu = \App::get('menu.manager')->menu('site');

	// Get the itemid form the URI
	$itemid = $uri->getVar('Itemid');

	if (is_null($itemid))
	{
		if ($option = $uri->getVar('option'))
		{
			$item  = $menu->getItem($uri->getVar('Itemid'));
			if (isset($item) && $item->component == $option)
			{
				$uri->setVar('Itemid', $item->id);
			}
		}
		else
		{
			if ($option = \App::get('router')->get('option'))
			{
				$uri->setVar('option', $option);
			}

			if ($itemid = \App::get('router')->get('Itemid'))
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
| Content
|
| Handle section, category, alias routing of com_content pages
*/
$router->rules('build')->append('content', function ($uri)
{
	$route = $uri->getPath();
	$query = $uri->getQuery(true);

	if (!isset($query['option']) || (isset($query['option']) && $query['option'] == 'com_content' && isset($query['task']) && $query['task'] == 'view'))
	{
		$segments = array();

		// Don't parse calls to other com_content views
		if (isset($query['view']) && $query['view'] == 'article' && !empty($query['id']))
		{
			$db = \App::get('db');
			$db->setQuery("SELECT `path` FROM `#__menu` WHERE link='index.php?option=com_content&view=article&id={$query['id']}' AND published=1");
			if ($menuitem = $db->loadResult())
			{
				$segments = explode('/', $menuitem);
			}
			else
			{
				$q  = "SELECT cat.`path`, con.`alias` AS con_alias, cat.`alias` AS cat_alias FROM `#__content` AS con";
				$q .= " LEFT JOIN `#__categories` AS cat ON con.catid = cat.id";
				$q .= " WHERE con.state=1 AND con.`id` = '{$query['id']}'";
				$db->setQuery($q);
				if ($result = $db->loadObject())
				{
					if ($result->cat_alias == 'uncategorised')
					{
						$segments[] = $result->con_alias;
					}
					else
					{
						$segments   = explode('/', $result->path);
						$segments[] = $result->con_alias;
					}
				}
			}
		}

		unset($query['task']);
		unset($query['view']);
		unset($query['id']);

		if (empty($segments))
		{
			return $uri;
		}

		$result = implode('/', $segments);
		$result = str_replace(':', '-', $result);

		$route .= ($result != '') ? '/' . $result : '';

		// Unset unneeded query information
		unset($query['Itemid']);
		unset($query['option']);

		// Set query again in the URI
		$uri->setQuery($query);
		$uri->setPath($route);
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

	if (!isset($query['option']))
	{
		$query['option'] = 'com_content';
	}

	$query['option'] = \App::get('component')->canonical($query['option']);

	if ($router = \App::get('component')->router($query['option'], 'site'))
	{
		$query = $router->preprocess($query);
		$parts = $router->build($query);

		$tmp   = implode('/', $parts);
	}

	$built = false;

	if (isset($query['Itemid']) && !empty($query['Itemid']))
	{
		$menu = \App::get('menu.manager')->menu('site');
		$item = $menu->getItem($query['Itemid']);
		if (is_object($item) && $query['option'] == $item->component)
		{
			if (!$item->home || $item->language != '*')
			{
				$tmp = !empty($tmp) ? $item->route . '/' . $tmp : $item->route;
			}

			$built = true;
		}
	}

	if (!$built)
	{
		$tmp = isset($query['option']) ? substr($query['option'], 4) . '/' . $tmp : $tmp;
	}

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
|
| Remove the base URI path. This will strip everything up to the base
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
| SEF Groups
|
| Remove the base URI path. This will strip everything up to the bas
*/
$router->rules('build')->append('groups', function ($uri)
{
	if (!empty($_SERVER['REWROTE_FROM']))
	{
		if (stripos($uri->toString(), $_SERVER['REWROTE_TO']->getPath()) !== false)
		{
			$uri->setPath(str_replace($_SERVER['REWROTE_TO']->getPath(), '', $uri->getPath()));
			$uri->setHost($_SERVER['REWROTE_FROM']->getHost());
			$uri->setScheme($_SERVER['REWROTE_FROM']->getScheme());
		}
	}

	return $uri;
});

/*
| Start
|
| Turn limitstart into start
*/
$router->rules('build')->append('limit', function ($uri)
{
	if ($uri->hasVar('limitstart'))
	{
		$uri->setVar('start', (int) $uri->getVar('limitstart'));
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
| Start
|
| Turn start into limitstart
*/
$router->rules('parse')->append('limit', function ($uri)
{
	$limitstart = $uri->getVar('start');
	if (!is_null($limitstart))
	{
		$uri->setVar('limitstart', (int) $limitstart);
		$uri->delVar('start');
		\App::get('router')->forget('start');
	}
});

/*
| Match by menu
|
| Match the first segment of the URI by component name. If a match is 
| found, the component's router will be loaded to continue parsing any
| further segments.
*/
$router->rules('parse')->append('menu', function ($uri)
{
	$menu  = App::get('menu');
	$route = $uri->getPath();

	// Remove the suffix
	if (\App::get('config')->get('sef_suffix'))
	{
		if ($suffix = pathinfo($route, PATHINFO_EXTENSION))
		{
			$route = str_replace('.' . $suffix, '', $route);
		}
	}

	// Get the variables from the uri
	$query = $uri->getQuery(true);

	// Handle an empty URL (special case)
	if (empty($route) && \App::get('request')->getVar('option', '', 'post') == '')
	{
		// If route is empty AND option is set in the query, assume it's non-sef url, and parse appropriately
		if (isset($query['option'])) // || isset($query['Itemid']))
		{
			return true;
		}

		$item = $menu->getDefault(\App::get('language')->getTag());

		// if user not allowed to see default menu item then avoid notices
		if (is_object($item))
		{
			// Set the information in the request
			$vars = $item->query;

			// Get the itemid
			$vars['Itemid'] = $item->id;

			// Set the active menu item
			$menu->setActive($vars['Itemid']);

			foreach ($vars as $key => $var)
			{
				$uri->setVar($key, $var);
			}
		}

		return true;
	}

	// Need to reverse the array (highest sublevels first)
	$items = array_reverse($menu->getMenu());

	$found           = false;
	$route_lowercase = strtolower($route);
	$lang_tag        = \App::get('language')->getTag();

	foreach ($items as $item)
	{
		//sqlsrv  change
		if (isset($item->language))
		{
			$item->language = trim($item->language);
		}

		// Keep searching for better matches with higher depth
		$depth  = substr_count(trim($item->route, '/'), '/') + 1;

		// Get the length of the route
		$length = strlen($item->route);

		if ($length > 0 && strpos($route_lowercase . '/', $item->route . '/') === 0
		 && $item->type != 'alias'
		 && (!\App::get('language.filter') || $item->language == '*' || $item->language == $lang_tag))
		{
			// Handle external url menu items differently
			if ($item->type == 'url')
			{
				// If menu route exactly matches url route, redirect (if necessary) to menu link
				if (trim($item->route, '/') == trim($route, '/'))
				{
					if (trim($item->route, '/') != trim($item->link, '/')
					 && trim($uri->base(true) . '/' . $item->route, '/') != trim($item->link, '/') // Added because it would cause redirect loop for installs not in top-level webroot
					 && trim($uri->base(true) . '/index.php/' . $item->route, '/') != trim($item->link, '/')) // Added because it would cause redirect loop for installs not in top-level webroot
					{
						\App::redirect($item->link);
					}
				}

				// Pass local URLs through, but record Itemid (we want the content parser to handle this)
				if (strpos($item->route, '://') === false)
				{
					$vars['Itemid'] = $item->id;
					break;
				}
			}

			// We have exact item for this language
			if ($item->language == $lang_tag)
			{
				$found      = $item;
				// Track depth so we can replace with a better match later
				$foundDepth = $depth;
				break;
			}
			// Or let's remember an item for all languages
			elseif (!$found || $depth >= $foundDepth)
			{
				// Deeper or equal depth matches later on are prefered
				$found      = $item;
				// Track depth so we can replace with a better match later
				$foundDepth = $depth;
			}
		}
	}

	// No menu item found.
	// Carry on...
	if (!$found)
	{
		return;
	}

	$route = substr($route, strlen($found->route));
	if ($route)
	{
		$route = substr($route, 1);
	}

	$uri->setVar('Itemid', $found->id);
	$uri->setVar('option', $found->component);
	$uri->setPath($route);
	foreach ($found->query as $key => $val)
	{
		$uri->setVar($key, $val);
	}

	$menu->setActive($uri->getVar('Itemid'));

	// No more segments.
	// No more processing needed.
	if (!$route)
	{
		return $uri;
	}
});

/*
| Match by content
|
| Match the content by article and category aliases. 
*/
$router->rules('parse')->append('content', function ($uri)
{
	if ($uri->getVar('option') && $uri->getVar('option') != 'com_content')
	{
		return;
	}

	$vars  = array();

	//$view  = 'article';
	$menu  = \App::get('menu.manager')->menu('site');
	$item  = $menu->getActive();
	$db    = \App::get('db');
	$segments = explode('/', $uri->getPath());
	$count = count($segments);

	// Item is numeric, assume user knows the article ID, and is trying to access directly
	if ($count == 1 && is_numeric($segments[0]))
	{
		$vars['option'] = 'com_content';
		$vars['id']     = $segments[0];
		$vars['view']   = 'article';

		$item->query['view'] = 'article';
	}
	// Count 1 - we're either looking for an article alias that matches and is in the uncategorised category,
	// or, an article alias and category series that are all the same (ex: about/about/about - supported for legacy reasons)
	else if ($count == 1)
	{
		// First, do query
		$query  = "SELECT con.`id`, cat.`alias`, cat.`path` FROM `#__content` AS con";
		$query .= " LEFT JOIN `#__categories` AS cat ON con.catid = cat.id";
		$query .= " WHERE con.state=1 AND con.`alias` = " . $db->quote(strtolower($segments[0]));
		$db->setQuery($query);
		$result = $db->loadObject();

		if (empty($result))
		{
			return;
		}

		// Now, check for uncategorised article with provided alias
		if ($result->alias == 'uncategorised')
		{
			// Success, that's it
			$segments = array();

			$vars['option'] = 'com_content';
			$vars['id']     = $result->id;
			$vars['view']   = 'article';

			$item->query['view'] = 'article';
		}
		else
		{
			// It wasn't uncategorised, so now try and see if its in a category scheme of all the same aliases
			$path  = explode('/', $result->path);
			$found = true;

			foreach ($path as $p)
			{
				if ($p != $segments[0])
				{
					$found = false;
					continue;
				}
			}

			if ($found)
			{
				// Success, that's it
				$segments = array();

				$vars['option'] = 'com_content';
				$vars['id']     = $result->id;
				$vars['view']   = 'article';

				$item->query['view'] = 'article';
			}
		}
	}
	else if ($count > 1)
	{
		// Build the path
		$path = array();
		for ($i=0; $i < ($count-1); $i++)
		{
			$path[] = $segments[$i];
		}

		$path = implode('/', $path);

		// Now, do query (path is all but last segment, and last segment is article alias)
		$query  = "SELECT con.`id` FROM `#__content` AS con";
		$query .= " LEFT JOIN `#__categories` AS cat ON con.catid = cat.id";
		$query .= " WHERE con.state=1 AND con.`alias` = " . $db->quote(strtolower($segments[$count-1]));
		$query .= " AND cat.`path` = " . $db->quote(strtolower($path));
		$db->setQuery($query);

		if ($result = $db->loadResult())
		{
			// Success, that's it
			$segments = array();

			$vars['option'] = 'com_content';
			$vars['id']     = $result;
			$vars['view']   = 'article';

			$item->query['view'] = 'article';
		}
	}

	if (!empty($vars))
	{
		foreach ($vars as $key => $var)
		{
			$uri->setVar($key, $var);
		}

		return true;
	}
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
		if (count($segments) > 1 && $segments[0] == 'component')
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
| Match by redirection rule
|
| Match the first segment of the URI by component name. If a match is 
| found, the component's router will be loaded to continue parsing any
| further segments.
*/
$router->rules('parse')->append('redirect', function ($uri)
{
	$db = \App::get('db');
	$db->setQuery("SELECT * FROM `#__redirect_links` WHERE `old_url`=" . $db->Quote($uri->getPath()));

	if ($row = $db->loadObject())
	{
		$myuri = new \Hubzero\Routing\Uri($row->newurl);

		$vars = $myuri->getQuery(true);
		foreach ($vars as $key => $var)
		{
			$uri->setVar($key, $var);
		}

		if (isset($vars['Itemid']))
		{
			$menu->setActive($vars['Itemid']);
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

