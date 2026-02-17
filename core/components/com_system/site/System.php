<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Site;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class System extends AbstractComponent
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	protected function execute(): void
	{
		\App::abort(404);

		$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'info'));
		if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)))) {
			\App::abort(404);
		}
		$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

		// Instantiate controller
		$controller = new $controllerName();
		$controller->execute();
		$controller->redirect();
	}
}
