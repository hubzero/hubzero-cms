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
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_tags')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'entries');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'entries';
        }
        $task = \Hubzero\Facades\Request::getCmd('task', '');

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_TAGS'),
            \Hubzero\Facades\Route::url('index.php?option=com_tags'),
            ($controllerName == 'entries')
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_TAGS_RELATIONSHIPS'),
            \Hubzero\Facades\Route::url('index.php?option=com_tags&controller=relationships'),
            ($controllerName == 'relationships' && $task != 'meta' && $task != 'updatefocusareas')
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_TAGS_FOCUS_AREAS'),
            \Hubzero\Facades\Route::url('index.php?option=com_tags&controller=relationships&task=meta'),
            ($controllerName == 'relationships' && ($task == 'meta' || $task == 'updatefocusareas'))
        );

        if (\Components\Plugins\Helpers\Plugins::getActions()->get('core.manage')) {
            \Hubzero\Facades\Submenu::addEntry(
                \Hubzero\Facades\Lang::txt('COM_TAGS_PLUGINS'),
                \Hubzero\Facades\Route::url('index.php?option=com_plugins&view=plugins&filter_folder=tags&filter_type=tags')
            );
        }

        // Include scripts
        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
