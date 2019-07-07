<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/*
                                                            
     _   _ _   _______  ___________ _      _       ___      
    | | | | | | | ___ \|___  /_   _| |    | |     / _ \     
    | |_| | | | | |_/ /   / /  | | | |    | |    / /_\ \    
    |  _  | | | | ___ \  / /   | | | |    | |    |  _  |    
    | | | | |_| | |_/ /./ /____| |_| |____| |____| | | |    
    \_| |_/\___/\____/ \_____/\___/\_____/\_____/\_| |_/    
                                                            
                    _,-}}-._                                
                   /\   }  /\                               
                 _|(O\\_ _/O)                ___|_          
               _|/  (__''__)                |#####|         
             _|\/    WVVVVW                 |#####|         
            \ _\     \MMMM/_            .-----.###|         
          _|\_\    _ '---;  \_          |#####|###|         
    /\   \ __\/     \_   /    \         |#####|###|         
  /  (    _\/     \   \  |'vvv          |#####|###|         
(    '-,._\_.(     'vvv  /              |#####|###|         
 \           /   _)  /  _)              |#####|###|         
  \______--''\__vvv)\__vvv)_____________|#####|###|_________
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
| Set PHP Error Reporting Options
|--------------------------------------------------------------------------
|
| Here we will set the strictest error reporting options, and also turn
| off PHP's error reporting, since all errors will be handled by the
| framework and we don't want any output leaking back to the user.
|
*/

error_reporting(-1);
ini_set('display_errors', 0);

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
	'files'         => 'files',

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
| caching, and more. First, we'll load the core bootstrap list of services
| and then we'll give the app a chance to modify that list.
|
*/

$providers = PATH_CORE . DS . 'bootstrap' . DS . $client .  DS . 'services.php';
$services  = file_exists($providers) ? require $providers : array();

$providers = PATH_CORE . DS . 'bootstrap' . DS . ucfirst($client) .  DS . 'services.php';
$services  = file_exists($providers) ? array_merge($services, require $providers) : $services;

$providers = PATH_APP . DS . 'bootstrap' . DS . $client .  DS . 'services.php';
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
| for the application.  First, we'll load the core bootstrap list of
| aliases and then we'll give the app a chance to modify that list.
|
*/

$facades = PATH_CORE . DS . 'bootstrap' . DS . $client .  DS . 'aliases.php';
$aliases = file_exists($facades) ? require $facades : array();

$facades = PATH_CORE . DS . 'bootstrap' . DS . ucfirst($client) .  DS . 'aliases.php';
$aliases = file_exists($facades) ? array_merge($aliases, require $facades) : $aliases;

$facades = PATH_APP . DS . 'bootstrap' . DS . $client .  DS . 'aliases.php';
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
