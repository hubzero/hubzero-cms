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

	// Add basepath to the uri
	$uri->setPath(\App::get('request')->base(true) . '/' . $route);

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
	$uri->setPath(trim($path , '/'));

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
