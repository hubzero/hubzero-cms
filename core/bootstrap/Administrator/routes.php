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
| Remove the base URI path. This will strip everything up to the bas
|
*/

/*
| SEF Rewrite
|
| Remove the base URI path. This will strip everything up to the base
*/
$router->rules('build')->append('base', function ($uri)
{
	// Get the path data
	$route = $uri->getPath();

	$base = \App::get('request')->base(true);
	if (substr($base, -strlen(\App::get('client')->name)) != \App::get('client')->name
	 && substr($base, -strlen(\App::get('client')->url)) != \App::get('client')->url)
	{
		$base .= '/' . \App::get('client')->name;
	}

	// Add basepath to the uri
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

	if (empty($option))
	{
		if (strtoupper(\App::get('request')->method()) == 'POST')
		{
			$option = \App::get('request')->getCmd('option', '', 'post');
		}
	}

	if (empty($option))
	{
		$option = 'com_cpanel';
	}

	$uri->setVar('option', $option);

	return true;
});
