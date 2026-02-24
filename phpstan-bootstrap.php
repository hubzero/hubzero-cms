<?php

/**
 * PHPStan bootstrap file
 *
 * Registers facade class aliases and constants so PHPStan
 * can resolve them during static analysis.
 */

// Define constants used throughout the codebase
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('PATH_ROOT')) {
    define('PATH_ROOT', __DIR__);
}
if (!defined('PATH_CORE')) {
    define('PATH_CORE', __DIR__ . '/core');
}
if (!defined('PATH_APP')) {
    define('PATH_APP', __DIR__ . '/app');
}

// Register Composer autoloader
require_once __DIR__ . '/core/vendor/autoload.php';

// Register the HubZero ClassLoader for Components\*, Plugins\*, etc.
Hubzero\Base\ClassLoader::addDirectories([__DIR__ . '/app', __DIR__ . '/core']);
Hubzero\Base\ClassLoader::register();

// Create class aliases for all facades (union of Site + Admin aliases)
$aliases = [
    'App'        => 'Hubzero\Facades\App',
    'Config'     => 'Hubzero\Facades\Config',
    'Request'    => 'Hubzero\Facades\Request',
    'Response'   => 'Hubzero\Facades\Response',
    'Event'      => 'Hubzero\Facades\Event',
    'Route'      => 'Hubzero\Facades\Route',
    'User'       => 'Hubzero\Facades\User',
    'Lang'       => 'Hubzero\Facades\Lang',
    'Log'        => 'Hubzero\Facades\Log',
    'Date'       => 'Hubzero\Facades\Date',
    'Plugin'     => 'Hubzero\Facades\Plugin',
    'Filesystem' => 'Hubzero\Facades\Filesystem',
    'Component'  => 'Hubzero\Facades\Component',
    'Session'    => 'Hubzero\Facades\Session',
    'Module'     => 'Hubzero\Facades\Module',
    'Pathway'    => 'Hubzero\Facades\Pathway',
    'Notify'     => 'Hubzero\Facades\Notify',
    'Cache'      => 'Hubzero\Facades\Cache',
    'Document'   => 'Hubzero\Facades\Document',
    'Html'       => 'Hubzero\Facades\Html',
    'Toolbar'    => 'Hubzero\Facades\Toolbar',
    'Submenu'    => 'Hubzero\Facades\Submenu',
];

foreach ($aliases as $alias => $class) {
    if (!class_exists($alias, false)) {
        class_alias($class, $alias);
    }
}
