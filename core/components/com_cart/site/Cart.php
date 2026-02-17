<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Site;

use Request;
use App;
use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Cart extends AbstractComponent
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	protected function execute(): void
	{

		$controllerName = Request::getCmd('controller', '');
		if (empty($controllerName)) {
		    App::redirect(Request::base() . 'cart/cart');
		}
		if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)))) {
		    App::abort(404, \Lang::txt('Page Not Found'));
		}
		$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

		// Instantiate controller
		$controller = new $controllerName();
		$controller->execute();
	}
}
