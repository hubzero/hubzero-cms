<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tags\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Tags extends AbstractComponent
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	protected function execute(): void
	{
		if (!\User::authorise('core.manage', 'com_tags')) {
		    \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

        $controllerName = \Request::getCmd('controller', 'entries');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'entries';
        }
        $task = \Request::getCmd('task', '');

		\Submenu::addEntry(
		    \Lang::txt('COM_TAGS'),
		    \Route::url('index.php?option=com_tags'),
		    ($controllerName == 'entries')
		);
		\Submenu::addEntry(
		    \Lang::txt('COM_TAGS_RELATIONSHIPS'),
		    \Route::url('index.php?option=com_tags&controller=relationships'),
		    ($controllerName == 'relationships' && $task != 'meta' && $task != 'updatefocusareas')
		);
		\Submenu::addEntry(
		    \Lang::txt('COM_TAGS_FOCUS_AREAS'),
		    \Route::url('index.php?option=com_tags&controller=relationships&task=meta'),
		    ($controllerName == 'relationships' && ($task == 'meta' || $task == 'updatefocusareas'))
		);
		if (\Components\Plugins\Helpers\Plugins::getActions()->get('core.manage')) {
		    \Submenu::addEntry(
		        \Lang::txt('COM_TAGS_PLUGINS'),
		        \Route::url('index.php?option=com_plugins&view=plugins&filter_folder=tags&filter_type=tags')
		    );
		}

		// Include scripts
		$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

		// Initiate controller
		$controller = new $controllerName();
		$controller->execute();
	}
}
