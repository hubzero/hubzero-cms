<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/*
|--------------------------------------------------------------------------
| SEF Build
|--------------------------------------------------------------------------
|
| Remove the base URI path. This will strip everything up to the bas
|
*/

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
		$query['option'] = 'com_cpanel';
	}

	$query['option'] = \App::get('component')->canonical($query['option']);

	$tmp = isset($query['option']) ? substr($query['option'], 4) . '/' . $tmp : $tmp;

	$route .= $tmp ? '/' . $tmp : '';

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
	$base = \App::get('request')->base(true);
	if (!\App::isSite())
	{
		$base = rtrim($base, '/') . '/' . \App::get('client')->name;
	}
	$uri->setPath($base . '/' . $route);

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
	\App::get('router')->forget('option');

	// Get the path
	$path = $uri->getPath();

	// Remove the base URI path.
	$path = substr_replace($path, '', 0, strlen(\App::get('request')->base(true)));

	// Remove prefix
	$path = str_replace('index.php', '', $path);

	// Set the route
	$uri->setPath(trim($path, '/'));

	return null;
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
	$option = $uri->getVar('option');
	if (is_array($option))
	{
		$option = implode('', $option);
	}

	if (\User::isGuest() || !\User::authorise('core.login.admin'))
	{
		$option = 'com_login';
	}
	else
	{
		$segments = explode('/', $uri->getPath());
		$client = array_shift($segments);
		$option = array_shift($segments);

		if (empty($option))
		{
			if (strtoupper(\App::get('request')->method()) == 'POST')
			{
				$option = \App::get('request')->getCmd('option', '', 'post');
			}
		}
	}

	if (empty($option))
	{
		$option = 'com_cpanel';
	}

	$uri->setVar('option', \App::get('component')->canonical($option));

	return true;
});
