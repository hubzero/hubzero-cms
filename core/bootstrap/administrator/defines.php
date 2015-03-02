<?php
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
	define('PATH_ROOT', dirname(JPATH_BASE));
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
	define('PATH_APP', PATH_ROOT);  //  . '/app'
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

define('PATH_CORE', PATH_ROOT);  // . '/core'

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

define('PATH_PUBLIC', PATH_APP);  //  . '/public'

/*
|--------------------------------------------------------------------------
| Joomla framework path definitions.
|--------------------------------------------------------------------------
|
| Ugh.
|
*/

define('JPATH_ROOT',            PATH_ROOT);
define('JPATH_SITE',            PATH_ROOT);
define('JPATH_CONFIGURATION',   PATH_ROOT);
define('JPATH_ADMINISTRATOR',   PATH_ROOT . '/administrator');
define('JPATH_LIBRARIES',       PATH_CORE . '/libraries');
define('JPATH_PLUGINS',         PATH_CORE . '/plugins');
define('JPATH_INSTALLATION',    PATH_CORE . '/installation');
define('JPATH_THEMES',          JPATH_BASE . '/templates');
define('JPATH_CACHE',           JPATH_BASE . '/cache');
define('JPATH_MANIFESTS',       JPATH_ADMINISTRATOR . '/manifests');
