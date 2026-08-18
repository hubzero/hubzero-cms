<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Admin;

if (!\User::authorise('core.manage', 'com_saml'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include models
require_once dirname(__DIR__) . DS . 'models' . DS . 'ServiceProvider.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'SamlSession.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'IdP.php';

$controllerName = \Request::getCmd('controller', 'saml');

if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'saml';
}

\Submenu::addEntry(
	\Lang::txt('COM_SAML_MENU_OVERVIEW'),
	\Route::url('index.php?option=com_saml&controller=saml', false),
	$controllerName == 'saml'
);
\Submenu::addEntry(
	\Lang::txt('COM_SAML_MENU_SERVICE_PROVIDERS'),
	\Route::url('index.php?option=com_saml&controller=serviceproviders', false),
	$controllerName == 'serviceproviders'
);
\Submenu::addEntry(
	\Lang::txt('COM_SAML_MENU_SESSIONS'),
	\Route::url('index.php?option=com_saml&controller=sessions', false),
	$controllerName == 'sessions'
);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';

$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

$controller = new $controllerName();

$controller->execute();
