<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Site;

// require base component controller
require_once __DIR__ . DS . 'controllers' . DS . 'component.php';

// require models
require_once dirname(__DIR__) . DS . 'models' . DS . 'Warehouse.php';

//build controller path and name
$controllerName = \Request::getCmd('controller', '');

if (empty($controllerName))
{
	// Load default controller if no controller provided
	$controllerName = 'storefront';
}
elseif (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	\App::abort(404, \Lang::txt('Page Not Found'));
}

$controllerRequested = $controllerName;

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller and execute
$controller = new $controllerName();

// See if user has to be logged in to see the component
$loginRequired = $controller->config->get('requirelogin', 0);

if ($loginRequired && $controllerRequested != 'overview')
{
	// Check if they're logged in
	if (\User::isGuest())
	{
		$return = base64_encode($_SERVER['REQUEST_URI']);
		// Redirect to the landing page
		if ($controllerRequested == 'storefront')
		{
			\App::redirect(
				\Route::url('index.php?option=com_storefront') . 'overview'
			);
		}
		// Require login
		\App::redirect(
			\Route::url('index.php?option=com_users&view=login&return=' . $return),
			'Please login to continue',
			'warning'
		);
	}
}

// Update any restrictions that were entered before the account existed
// @TODO: Move to a plugin that responds after login?
if (!\User::isGuest())
{
	require_once dirname(__DIR__) . DS . 'admin' . DS . 'helpers' . DS . 'restrictions.php';

	\Components\Storefront\Admin\Helpers\RestrictionsHelper::updateUser(\User::get('id'), \User::get('username'));
}

$controller->execute();
$controller->redirect();
