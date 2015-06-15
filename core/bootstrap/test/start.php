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

if (php_sapi_name() != 'cli')
{
	exit();
}

/*
|--------------------------------------------------------------------------
| Parent Flag
|--------------------------------------------------------------------------
|
| Set flag that this is a parent file.
|
*/
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

/*
|--------------------------------------------------------------------------
| Define directories
|--------------------------------------------------------------------------
|
| First thing we need to do is set some constants for the app's directory
| and the path to the parent directory containing the app and core.
|
*/

define('JPATH_BASE', dirname(dirname(dirname(__DIR__))));

require_once JPATH_BASE . DS . 'core' . DS . 'bootstrap' . DS . 'site' . DS . 'defines.php';

/*
|--------------------------------------------------------------------------
| Load The Framework
|--------------------------------------------------------------------------
|
| Here we will load the framework. We'll keep this is in a
| separate location so we can isolate the creation of an application
| from the actual running of the application with a given request.
|
*/

require_once PATH_ROOT . DS . 'core' . DS . 'bootstrap' . DS . 'site' .  DS . 'framework.php';

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
| Detect The Application Client
|--------------------------------------------------------------------------
|
| Laravel takes a dead simple approach to your application environments
| so you can just specify a machine name or HTTP host that matches a
| given environment, then we will automatically detect it for you.
|
*/

$client = $app->detectClient(array(

	'administrator' => 'administrator',
	'api'           => 'api',
	'cli'           => 'cli',
	'install'       => 'install',

))->name;

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

$app['config'] = new \Hubzero\Config\Repository($client);

/*
|--------------------------------------------------------------------------
| Register The Core Service Providers
|--------------------------------------------------------------------------
|
| Register all of the core pieces of the framework including session, 
| caching, and more.
|
*/

$providers = PATH_CORE . DS . 'bootstrap' . DS . $client .  DS . 'services.php';
$services = file_exists($providers) ? require $providers : array();
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

$aliases = PATH_CORE . DS . 'bootstrap' . DS . $client .  DS . 'aliases.php';

$app->registerBaseFacades(file_exists($aliases) ? require $aliases : array());

\JFactory::getApplication('site');