<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Feedback\Admin;

if (!\User::authorise('core.manage', 'com_feedback'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once dirname(__DIR__) . DS . 'models' . DS . 'quote.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';

$controllerName = 'quotes';
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Initiate controller
$controller = new $controllerName();
$controller->execute();
