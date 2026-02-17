<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Redirect\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Redirect extends AbstractComponent
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	protected function execute(): void
	{
		// Access check.
		if (!\User::authorise('core.manage', 'com_redirect')) {
		    \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
		    return;
		}

		// Include controller
		$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'links'));
		if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)))) {
			\App::abort(404, \Lang::txt('Controller "%s" not found.', $controllerName));
			return;
		}
		$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

		// Instantiate controller
		$controller = new $controllerName();
		$controller->execute();
	}
}
