<?php
/**
 * HUBzero CMS
 *
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

/*
|--------------------------------------------------------------------------
| Root Path
|--------------------------------------------------------------------------
|
| Typically this will be defined before we even get to this file. But,
| for now, we need to define it here.
|
*/

if (!defined('PATH_ROOT'))
{
	define('PATH_ROOT', dirname(dirname(__DIR__)));
}

/*
|--------------------------------------------------------------------------
| Application Directory Path
|--------------------------------------------------------------------------
|
| Here we define the path to the application directory. Most likely
| you will never need to change this value as the default setup should
| work perfectly fine for the vast majority of applications.
|
*/

if (!defined('PATH_APP'))
{
	define('PATH_APP', PATH_ROOT . DIRECTORY_SEPARATOR . 'app');
}

/*
|--------------------------------------------------------------------------
| Core Path
|--------------------------------------------------------------------------
|
| The core path is where the heart of the installation lives. Most likely 
| you will not need to change this value (and it is highly discouraged). 
| But, if for some wild reason it is necessary you will do so here. 
| Proceed with caution.
|
*/

define('PATH_CORE', PATH_ROOT . DIRECTORY_SEPARATOR . 'core');

/*
|--------------------------------------------------------------------------
| Public Path
|--------------------------------------------------------------------------
|
| The public path contains the assets for your web application, such as
| your JavaScript and CSS files, and also contains the primary entry
| point for web requests into these applications from the outside.
|
*/

define('PATH_PUBLIC', PATH_APP);  // . '/public';

/*
|--------------------------------------------------------------------------
| Joomla framework path definitions.
|--------------------------------------------------------------------------
|
| Ugh.
|
*/

if (!defined('_JEXEC'))
{
	define('_JEXEC', 1);
}
if (!defined('JPATH_BASE'))
{
	define('JPATH_BASE', PATH_ROOT);
}
define('JPATH_ROOT',            PATH_ROOT);
define('JPATH_SITE',            PATH_ROOT);
define('JPATH_CONFIGURATION',   PATH_APP  . DIRECTORY_SEPARATOR . 'config');
define('JPATH_ADMINISTRATOR',   PATH_ROOT . DIRECTORY_SEPARATOR . 'administrator');
define('JPATH_LIBRARIES',       PATH_CORE . DIRECTORY_SEPARATOR . 'libraries');
define('JPATH_PLUGINS',         PATH_CORE . DIRECTORY_SEPARATOR . 'plugins');
define('JPATH_INSTALLATION',    PATH_ROOT . DIRECTORY_SEPARATOR . 'installation');
define('JPATH_THEMES',          PATH_APP  . DIRECTORY_SEPARATOR . 'templates');
define('JPATH_CACHE',           PATH_APP  . DIRECTORY_SEPARATOR . 'cache');
define('JPATH_MANIFESTS',       PATH_CORE . DIRECTORY_SEPARATOR . 'manifests');
define('JPATH_API',             PATH_ROOT . DIRECTORY_SEPARATOR . 'api');
