<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Tools extends AbstractComponent
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	protected function execute(): void
	{
		if (!\User::authorise('core.manage', 'com_tools')) {
		    \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
		    return;
		}

		// Include scripts
		require_once dirname(__DIR__) . DS . 'helpers' . DS . 'utils.php';
		require_once dirname(__DIR__) . DS . 'helpers' . DS . 'helper.php';
		require_once dirname(__DIR__) . DS . 'models' . DS . 'tool.php';

		$controllerName = \Request::getCmd('controller', 'pipeline');
		if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
		    $controllerName = 'pipeline';
		}

		\Submenu::addEntry(
		    \Lang::txt('COM_TOOLS_PIPELINE'),
		    \Route::url('index.php?option=com_tools&controller=pipeline'),
		    $controllerName == 'pipeline'
		);
		\Submenu::addEntry(
		    \Lang::txt('COM_TOOLS_HOSTS'),
		    \Route::url('index.php?option=com_tools&controller=hosts'),
		    $controllerName == 'hosts'
		);
		\Submenu::addEntry(
		    \Lang::txt('COM_TOOLS_HOST_TYPES'),
		    \Route::url('index.php?option=com_tools&controller=hosttypes'),
		    $controllerName == 'hosttypes'
		);
		if (\Component::params('com_tools')->get('zones')) {
		    \Submenu::addEntry(
		        \Lang::txt('COM_TOOLS_ZONES'),
		        \Route::url('index.php?option=com_tools&controller=zones'),
		        $controllerName == 'zones'
		    );
		}
		\Submenu::addEntry(
		    \Lang::txt('COM_TOOLS_SESSIONS'),
		    \Route::url('index.php?option=com_tools&controller=sessions'),
		    $controllerName == 'sessions'
		);
		\Submenu::addEntry(
		    \Lang::txt('COM_TOOLS_USER_PREFS'),
		    \Route::url('index.php?option=com_tools&controller=preferences'),
		    $controllerName == 'preferences'
		);
		\Submenu::addEntry(
		    \Lang::txt('COM_TOOLS_HANDLERS'),
		    \Route::url('index.php?option=com_tools&controller=handlers'),
		    $controllerName == 'handlers'
		);

		if (\Component::params('com_tools')->get('windows_key_id')) {
		    \Submenu::addEntry(
		        \Lang::txt('COM_TOOLS_WINDOWS'),
		        \Route::url('index.php?option=com_tools&controller=windows'),
		        $controllerName == 'windows'
		    );
		}

		require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
		$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

		// Instantiate controller
		$controller = new $controllerName();
		$controller->execute();
		$controller->redirect();
	}
}
