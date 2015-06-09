<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new application instance which
| serves as the "glue" for all the parts of a hub, and is the IoC container
| for the system binding all of the various parts.
|
*/

$app = new Hubzero\Base\Application;

/*
|--------------------------------------------------------------------------
| Set The Application Client
|--------------------------------------------------------------------------
|
| We're just setting this explicitly here - no need for dynamic detection.
| The fact that we're in this file is detection enough.
|
*/

$app['client'] = new Hubzero\Base\Client\Cli;

/*
|--------------------------------------------------------------------------
| Bind The Application In The Container
|--------------------------------------------------------------------------
|
| This may look strange, but we actually want to bind the app into itself
| in case we need to Facade test an application. This will allow us to
| resolve the "app" key out of this container for this app's facade.
|
*/

$app['app'] = $app;

/*
|--------------------------------------------------------------------------
| Register The Configuration Repository
|--------------------------------------------------------------------------
|
| The configuration repository is used to lazily load in the options for
| this application from the configuration files. The files are easily
| separated by their concerns so they do not become really crowded.
|
*/

$app['config'] = new Hubzero\Config\Repository('cli');
$app['config']->set('session_handler', 'none');

if (!defined('JDEBUG'))   define('JDEBUG',   $app['config']->get('debug'));
if (!defined('JPROFILE')) define('JPROFILE', $app['config']->get('debug') || $app['config']->get('profile'));

/*
|--------------------------------------------------------------------------
| Register The Core Service Providers
|--------------------------------------------------------------------------
|
| Register all of the core pieces of the framework.
|
*/

$providers = PATH_CORE . DS . 'core' . DS . 'bootstrap' . DS . 'cli' .  DS . 'services.php';
$services = file_exists($providers) ? require $providers : [];
foreach ($services as $service)
{
	$app->register($service);
}

/*
|--------------------------------------------------------------------------
| Load The Aliases
|--------------------------------------------------------------------------
|
| The alias loader is responsible for lazy loading the class aliases setup
| for the application.
|
*/

$aliases = PATH_CORE . DS . 'core' . DS . 'bootstrap' . DS . 'cli' .  DS . 'aliases.php';

$app->registerBaseFacades(file_exists($aliases) ? require $aliases : []);

return $app;