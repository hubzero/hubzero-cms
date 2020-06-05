<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/*
|--------------------------------------------------------------------------
| Set PHP INI values
|--------------------------------------------------------------------------
|
| Here we will set some of PHP's INI values to make things run a little
| smoother.
|
*/

@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', 0);

/*
|--------------------------------------------------------------------------
| Set PHP default timezone
|--------------------------------------------------------------------------
|
| PHP gets very angry if you don't set the default date timezone. While
| we're here, we'll set the internal encoding to UTF-8 for good measure.
|
*/

date_default_timezone_set('UTC');

if (function_exists('mb_internal_encoding'))
{
	mb_internal_encoding('UTF-8');
}

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

/*
|--------------------------------------------------------------------------
| Register The Core Service Providers
|--------------------------------------------------------------------------
|
| Register all of the core pieces of the framework.
|
*/

$providers = PATH_CORE . DS . 'bootstrap' . DS . 'Cli' .  DS . 'services.php';
$services  = file_exists($providers) ? require $providers : array();

$providers = PATH_APP . DS . 'bootstrap' . DS . 'cli' .  DS . 'services.php';
$services  = file_exists($providers) ? array_merge($services, require $providers) : $services;

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

$facades = PATH_CORE . DS . 'bootstrap' . DS . 'Cli' .  DS . 'aliases.php';
$aliases = file_exists($facades) ? require $facades : array();

$facades = PATH_APP . DS . 'bootstrap' . DS . 'cli' .  DS . 'aliases.php';
$aliases = file_exists($facades) ? array_merge($aliases, require $facades) : $aliases;

$app->registerFacades($aliases);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
