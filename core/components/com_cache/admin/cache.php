<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cache\Admin;

// Access check.
if (!\User::authorise('core.manage', 'com_cache'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once(dirname(__DIR__) . DS . 'models' . DS . 'manager.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'helper.php');
require_once(__DIR__ . DS . 'controllers' . DS . 'cleanser.php');

// Instantiate controller
$controller = new Controllers\Cleanser();
$controller->execute();
