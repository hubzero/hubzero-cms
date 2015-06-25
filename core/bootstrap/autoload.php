<?php
/**
 * HUBzero CMS
 *
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2015 Purdue University. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
|--------------------------------------------------------------------------
| Include Helper Functions
|--------------------------------------------------------------------------
|
| Include some helper functions. There's really no other good spot to do
| this so it happens here.
|
*/

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'Hubzero' . DIRECTORY_SEPARATOR . 'Base' . DIRECTORY_SEPARATOR . 'helpers.php';
