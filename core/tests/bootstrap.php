<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * PHPUnit bootstrap.
 *
 * Library tests build their own container, but component and plugin tests
 * require model and helper files at file scope, and those files call facades
 * ("Component::path()", "Lang::txt()") before any setUpBeforeClass() runs.
 *
 * Registration goes through Facade::createAliases() rather than plain
 * class_alias(), because that is what the application itself uses: it installs
 * an autoloader that resolves a facade referenced from inside a namespace by
 * matching the final segment of the class name. Eagerly declaring the global
 * aliases instead would both diverge from runtime behaviour and collide with
 * fixtures that legitimately declare their own classes of the same name.
 */

if (!defined('_HZEXEC_'))
{
	define('_HZEXEC_', true);
}

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('PATH_ROOT'))
{
	define('PATH_ROOT', dirname(__DIR__, 2));
}

if (!defined('PATH_CORE'))
{
	define('PATH_CORE', dirname(__DIR__));
}

if (!defined('PATH_APP'))
{
	define('PATH_APP', PATH_ROOT . DS . 'app');
}

require dirname(__DIR__) . '/vendor/autoload.php';

// Minimal container so facades called at file scope resolve. Test classes
// replace what they need; this only has to get the files loaded.
$app = new Hubzero\Container\Container();

// The App facade resolves the container through an 'app' binding, the same way
// Hubzero\Base\Application registers itself.
$app['app'] = $app;

$app['dispatcher'] = function ()
{
	return new Hubzero\Events\Dispatcher();
};

require_once __DIR__ . '/stubs.php';

// See Hubzero\Test\Stubs\DatabaseStub for why queries throw but the datetime
// format is answered for real.
$app['db'] = function ()
{
	return new Hubzero\Test\Stubs\DatabaseStub();
};

$app['language'] = function ()
{
	return new Hubzero\Test\Stubs\TranslatorStub();
};

$app['component'] = function ($app)
{
	return new Hubzero\Component\Loader($app);
};

Hubzero\Facades\Facade::setApplication($app);
Hubzero\Facades\Facade::createAliases(
	require dirname(__DIR__) . '/bootstrap/Cli/aliases.php'
);

// Plugin tests ship their own bootstrap that requires the plugin class file,
// because plugin classes live outside any autoloaded namespace. PHPUnit runs
// a single bootstrap for the whole run, so pull each of those in here.
foreach (glob(dirname(__DIR__) . '/plugins/*/*/tests/bootstrap.php') as $pluginBootstrap)
{
	require_once $pluginBootstrap;
}
