<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Admin;

if (!\User::authorise('core.manage', 'com_massmail'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include models
require_once dirname(__DIR__) . DS . 'models' . DS . 'mailing.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'recipient.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'attachment.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'status.php';

// Include helpers
require_once __DIR__ . DS . 'helpers' . DS . 'permissions.php';

// Instantiate the requested controller. Entering via the Components menu
// (no controller specified) lands on the status dashboard.
$controllerName = \Request::getCmd('controller', 'status');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'status';
}

// Component submenu
$menuItems = array(
	'status'   => \Lang::txt('COM_MASSMAIL_DASHBOARD'),
	'mailings' => \Lang::txt('COM_MASSMAIL_MAILINGS')
);
foreach ($menuItems as $k => $v)
{
	$active = (\Request::getCmd('controller', 'status') == $k);
	\Submenu::addEntry($v, \Route::url('index.php?option=com_massmail&controller=' . $k), $active);
}

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';

$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);
$controller = new $controllerName();
$controller->execute();
