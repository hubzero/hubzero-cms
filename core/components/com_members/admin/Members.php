<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Members extends AbstractComponent
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	protected function execute(): void
	{
		if (!\User::authorise('core.manage', 'com_members')) {
		    \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
		    return;
		}

		$controllerName = \Request::getCmd('controller', 'members');
		if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
		    $controllerName = 'members';
		}

		// Build sub-menu
		require_once __DIR__ . DS . 'helpers' . DS . 'members.php';

		\Components\Members\Admin\Helpers\MembersHelper::addSubmenu($controllerName);

		// Instantiate controller
		require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
		$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

		$controller = new $controllerName();
		$controller->execute();
	}
}
