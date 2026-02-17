<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Site;

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
		require_once dirname(__DIR__) . DS . 'models' . DS . 'application.php';

		$controllerName = \Request::getCmd('controller', 'developer');
		if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
		    $controllerName = 'developer';
		}
		require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
		$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

		// Instantiate the controller
		$component = new $controllerName();
		$component->execute();
	}
}
