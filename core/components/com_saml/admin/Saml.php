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

		if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)))) {
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

		$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

		$controller = new $controllerName();

		$controller->execute();
	}
}
