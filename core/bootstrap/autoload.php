<?php
/**
 * HUBzero CMS
 *
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2015 HUBzero Foundation, LLC.
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
| Include Helper Functions
|--------------------------------------------------------------------------
|
| Include some helper functions. There's really no other good spot to do
| this so it happens here.
|
*/

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'Hubzero' . DIRECTORY_SEPARATOR . 'Base' . DIRECTORY_SEPARATOR . 'helpers.php';
