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

include_once(dirname(__DIR__) . DS . 'paths.php');
