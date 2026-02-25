<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!defined('_HZEXEC_')) {
    define('_HZEXEC_', 1);
}

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('PATH_ROOT')) {
    define('PATH_ROOT', dirname(dirname(__DIR__)));
}

if (!defined('PATH_CORE')) {
    define('PATH_CORE', PATH_ROOT . '/core');
}

if (!defined('PATH_APP')) {
    define('PATH_APP', PATH_ROOT . '/app');
}

define('JPATH_BASE', PATH_ROOT);
define('JPATH_ROOT', PATH_ROOT);
define('JPATH_SITE', PATH_ROOT);
define('JPATH_CONFIGURATION', PATH_APP . '/config');
define('JPATH_ADMINISTRATOR', PATH_ROOT . '/administrator');
define('JPATH_LIBRARIES', PATH_CORE . '/libraries');
define('JPATH_PLUGINS', PATH_CORE . '/plugins');
define('JPATH_INSTALLATION', PATH_ROOT . '/installation');
define('JPATH_THEMES', PATH_APP . '/templates');
define('JPATH_CACHE', PATH_APP . '/cache');
define('JPATH_MANIFESTS', PATH_CORE . '/manifests');
define('JPATH_API', PATH_ROOT . '/api');

define('HVERSION', '2.4.1');

date_default_timezone_set('UTC');

mb_internal_encoding('UTF-8');

// Work around for issues with SCRIPT_NAME and PHP_SELF set incorrectly by php-fpm
// GH-12996 https://github.com/php/php-src/issues/12996 fixed in 8.2.16
// GH-10869 https://github.com/php/php-src/issues/10869 fixed in 8.1.18
// Don't overrite ORIG_SCRIPT_NAME if already set (e.g. between above versions)

if (PHP_VERSION_ID < 80216 && strpos($_SERVER['PATH_INFO'], '%') !== false) {
    if (!isset($_SERVER['ORIG_SCRIPT_NAME'])) {
        $_SERVER['ORIG_SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'];
    }

    $_SERVER['SCRIPT_NAME'] = str_replace(rawurldecode($_SERVER['PATH_INFO']), '', $_SERVER['SCRIPT_NAME']);
    $_SERVER['PHP_SELF'] = str_replace(rawurldecode($_SERVER['PATH_INFO']), '', $_SERVER['PHP_SELF']);
}

Hubzero\Base\ClassLoader::addDirectories(array( PATH_APP,   PATH_CORE));
Hubzero\Base\ClassLoader::register();

/**
 * Get the root Facade application instance.
 *
 * Inspired by Laravel (http://laravel.com)
 *
 * @param   string  $key
 * @return  mixed
 */
function app($key = null)
{
    if (!is_null($key)) {
        return app()->get($key);
    }

    return \Hubzero\Facades\Facade::getApplication();
}

/**
 * Get the specified configuration value.
 *
 * Inspired by Laravel (http://laravel.com)
 *
 * @param   mixed  $key      array|string
 * @param   mixed  $default  Default value if key isn't found
 * @return  mixed
 */
function config($key = null, $default = null)
{
    if (is_null($key)) {
        return app('config');
    }

    return app('config')->get($key, $default);
}

/**
 * Dump the passed variables and end the script.
 *
 * @param   mixed
 * @return  void
 */
function ddie($var)
{
    foreach (func_get_args() as $var) {
        \Hubzero\Debug\Dumper::dump($var);
    }
    die();
}

/**
 * Dump the passed variables to the debug bar.
 *
 * @param   mixed
 * @return  void
 */
function dlog()
{
    foreach (func_get_args() as $var) {
        \Hubzero\Debug\Dumper::log($var);
    }
}

if (!function_exists('dump')) {
    /**
     * Dump the passed variables.
     *
     * @param   mixed
     * @return  void
     */
    function dump($var)
    {
        foreach (func_get_args() as $var) {
            \Hubzero\Debug\Dumper::dump($var);
        }
    }
}

/**
 * Return the given object. Useful for chaining.
 *
 * Inspired by Laravel (http://laravel.com)
 *
 * @param   mixed  $object
 * @return  mixed
 */
function with($object)
{
    return $object;
}

/**
 * Checks for the existence of the provided class without
 * diving into the HUBzero Facade autoloader.
 *
 * @param   string  $classname  The classname to look for
 * @return  bool
 **/
function classExists($classname)
{
    $result = false;

    foreach (spl_autoload_functions() as $loader) {
        if (is_array($loader) && isset($loader[0]) && $loader[0] == 'Hubzero\Facades\Facade') {
            $autoloader = $loader;
            break;
        }
    }

    if (isset($autoloader)) {
        spl_autoload_unregister($autoloader);

        $result = class_exists($classname);

        spl_autoload_register($autoloader);
    } else {
        $result = class_exists($classname);
    }

    return $result;
}
