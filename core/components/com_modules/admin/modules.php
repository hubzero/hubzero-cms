<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Modules\Admin;

// Access check.
if (!\User::authorise('core.manage', 'com_modules'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'modules.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'module.php';
require_once __DIR__ . DS . 'controllers' . DS . 'modules.php';

// initiate controller
$controller = new Controllers\Modules();
$controller->execute();
