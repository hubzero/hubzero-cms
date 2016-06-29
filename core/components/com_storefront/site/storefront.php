<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Storefront\Site;

// require base component controller
require_once(__DIR__ . DS . 'controllers' . DS . 'component.php');

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

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
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

$controller->execute();
$controller->redirect();