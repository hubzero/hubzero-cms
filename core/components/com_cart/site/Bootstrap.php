<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Site;

use Request;
use App;

/**
 * Component bootstrap
 */
class Bootstrap
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	public function start()
	{
		// require base component controller
		require_once __DIR__ . DS . 'controllers' . DS . 'component.php';

		$controllerName = Request::getCmd('controller', '');
		if (empty($controllerName)) {
		    App::redirect(Request::base() . 'cart/cart');
		}
		if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
		    App::abort(404, \Lang::txt('Page Not Found'));
		}
		require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
		$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

		// Instantiate controller
		$controller = new $controllerName();
		$controller->execute();
	}
}
