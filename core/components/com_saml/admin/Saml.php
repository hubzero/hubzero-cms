<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Saml extends AbstractComponent
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	protected function execute(): void
	{
		if (!\User::authorise('core.manage', 'com_saml')) {
		        \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$controllerName = \Request::getCmd('controller', 'saml');

		if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
		        $controllerName = 'saml';
		}

		require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';

		$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

		$controller = new $controllerName();

		$controller->execute();
	}
}
