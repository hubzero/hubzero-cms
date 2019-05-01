<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin;

if (!\User::authorise('core.manage', 'com_members'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once dirname(__DIR__) . DS . 'models' . DS . 'member.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'admin.php';

$controllerName = \Request::getCmd('controller', 'members');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'members';
}

// Build sub-menu
require_once __DIR__ . DS . 'helpers' . DS . 'members.php';

\MembersHelper::addSubmenu($controllerName);

// Instantiate controller
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

$controller = new $controllerName();
$controller->execute();
