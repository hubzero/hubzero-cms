<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/*
|--------------------------------------------------------------------------
| SEF Build
|--------------------------------------------------------------------------
|
| Rules to build a SEF route from a querystring
|
*/

/*
| Build the route by component name
*/
$router->rules('build')->append('component', function ($uri)
{
	$route = $uri->getPath();
	$query = $uri->getQuery(true);
	$tmp   = '';

	if (!isset($query['option']))
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

	if (isset($query['option']))
	{
		$tmp = substr($query['option'], 0, 4) == 'com_'
			? substr($query['option'], 4) . '/' . $tmp
			: $query['option'] . '/' . $tmp;
	}

	$route .= $tmp ? '/' . $tmp : '';

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

	$base = \App::get('request')->base(true);

	$uri->setPath($base . '/' . $route);

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
	$uri->setPath(trim($path, '/'));
});

/*
| Determine version
*/
$router->rules('parse')->append('version', function ($uri)
{
	$version = '';

	$segments  = explode('/', $uri->getPath());

	// Shift /api off the beginning
	if (isset($segments[0]) && $segments[0] == 'api')
	{
		$prefix = array_shift($segments);
		$uri->setPath(implode('/', $segments));
	}

	// Version from segments. ex: /v1.0/component
	if (isset($segments[0]) && preg_match('/v([0-9]{1,2}\.[0-9x]{1,2}|[0-9x]{1,2})/', $segments[0], $matches))
	{
		$version = array_shift($segments);
		$version = trim($version, 'v');
		$uri->setPath(implode('/', $segments));
	}

	// Does the accept header have version identifier?
	if (preg_match('/application\/vnd\.[a-zA-Z]{2,20}\.v([0-9x]{1,2}\.[0-9x]{1,2}|[0-9x]{1,2})/', \App::get('request')->headers->get('accept'), $matches))
	{
		$version = $matches[1];
	}

	// Does the query string have a version identifier?
	if ($uri->getVar('v'))
	{
		$version = $uri->getVar('v');
	}
	elseif ($uri->getVar('version'))
	{
		$version = $uri->getVar('version');
	}

	// Normalize version
	$calc = array();
	if (strpos($version, '.') !== false)
	{
		$versionParts = array_map('trim', explode('.', $version));
		$calc['major'] = $versionParts[0];
		$calc['minor'] = $versionParts[1];
	}
	else
	{
		$calc['major'] = $version;
		$calc['minor'] = 0;
	}

	$calc['major'] = $calc['major'] ? $calc['major'] : 1;

	// Push data to URI
	$uri->setVar('version.major', $calc['major']);
	$uri->setVar('version.minor', $calc['minor']);
	$uri->setVar('version', implode('.', $calc));
});

/*
| Predefine task based on request method
*/
$router->rules('parse')->append('crud', function ($uri)
{
	switch (strtolower(\App::get('request')->method()))
	{
		case 'get':
			// Task could be 'list' or 'read' or
			// something else entirely so we let
			// the component handle it
		break;

		case 'post':
			$uri->setVar('task', 'create');
		break;

		case 'put':
			$uri->setVar('task', 'update');
		break;

		case 'delete':
			$uri->setVar('task', 'delete');
		break;
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
	if (is_array($component))
	{
		$component = implode('', $component);
	}
	$segments  = explode('/', $uri->getPath());

	if (!$component)
	{
		$component = array_shift($segments);
	}

	if (!$component)
	{
		// No component name found.
		// Nothing else we can do here.
		return;
	}

	$uri->setVar('option', \App::get('component')->canonical($component));

	if ($uri->getVar('version', null))
	{
		$router = \App::get('component')->router($component, 'api', str_replace('.', '_', $uri->getVar('version')));
	}
	else
	{
		$router = \App::get('component')->router($component, 'api');
	}

	if ($router)
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
