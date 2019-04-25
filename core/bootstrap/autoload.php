<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for the application. We'll require it here so that we do not have to 
| worry about the loading of any of the classes "manually".
|
*/

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

/*
| Apps may have their own separate composer instance. So, let's check
| for that and include it, if found.
*/
if (file_exists(PATH_APP . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php'))
{
	require PATH_APP . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
}

/*
|--------------------------------------------------------------------------
| Register The Extension Auto Loader
|--------------------------------------------------------------------------
|
| We register an auto-loader "behind" the Composer loader that can load
| classes on the fly. We'll add it to the stack here. It supports PSR-4 and
| lowercase PSR-4 paths. Ideally, everything would be PSR-4 and the
| Composer autoloader may be able to handle everything. But, for now, we
| need this to support the lowercase paths currently in use.
|
*/

Hubzero\Base\ClassLoader::addDirectories(array(
	PATH_APP,
	PATH_CORE
));
Hubzero\Base\ClassLoader::register();

/*
|--------------------------------------------------------------------------
| Include Helper Functions
|--------------------------------------------------------------------------
|
| Include some helper functions. There's really no other good spot to do
| this so it happens here.
|
*/

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'hubzero' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Base' . DIRECTORY_SEPARATOR . 'helpers.php';
