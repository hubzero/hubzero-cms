<?php
/**
 * Bootstrap for the Shibboleth plugin tests: the plugin class lives outside any
 * autoloaded namespace, so it is required here (see core/tests/bootstrap.php).
 */

if (!defined('_HZEXEC_'))
{
	define('_HZEXEC_', true);
}

require __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../shibboleth.php';
