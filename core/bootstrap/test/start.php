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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
define('_HZEXEC_', 1);
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

define('PATH_ROOT', dirname(dirname(dirname(__DIR__))));

require_once PATH_ROOT . DS . 'core' . DS . 'bootstrap' . DS . 'paths.php';

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

require_once PATH_ROOT . DS . 'core' . DS . 'bootstrap' . DS . 'autoload.php';

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
| Here, we try to automatically detect the client type being called. This
| will determine the set of services, facades, etc. that get loaded
| further on in the application lifecycle.
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

$app->registerFacades(file_exists($aliases) ? require $aliases : array());