<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Resources extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        $option = \Hubzero\Facades\Request::getCmd('option', 'com_resources');
        $task = \Hubzero\Facades\Request::getWord('task', '');

        if (!\Hubzero\Facades\User::authorise('core.manage', $option)) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        // Get controller name
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'items');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'items';
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_RESOURCES'),
            \Hubzero\Facades\Route::url('index.php?option=' . $option),
            ($controllerName == 'items' && $task != 'orphans')
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_RESOURCES_ORPHANS'),
            \Hubzero\Facades\Route::url('index.php?option=' . $option . '&controller=items&task=orphans'),
            $task == 'orphans'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_RESOURCES_TYPES'),
            \Hubzero\Facades\Route::url('index.php?option=' . $option . '&controller=types'),
            $controllerName == 'types'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_RESOURCES_LICENSES'),
            \Hubzero\Facades\Route::url('index.php?option=' . $option . '&controller=licenses'),
            $controllerName == 'licenses'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_RESOURCES_AUTHORS'),
            \Hubzero\Facades\Route::url('index.php?option=' . $option . '&controller=authors'),
            $controllerName == 'authors'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_RESOURCES_ROLES'),
            \Hubzero\Facades\Route::url('index.php?option=' . $option . '&controller=roles'),
            $controllerName == 'roles'
        );

        if (\Components\Plugins\Helpers\Plugins::getActions()->get('core.manage')) {
            \Hubzero\Facades\Submenu::addEntry(
                \Hubzero\Facades\Lang::txt('COM_RESOURCES_PLUGINS'),
                \Hubzero\Facades\Route::url('index.php?option=' . $option . '&controller=plugins'),
                $controllerName == 'plugins'
            );
        }
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_RESOURCES_IMPORT'),
            \Hubzero\Facades\Route::url('index.php?option=' . $option . '&controller=imports'),
            $controllerName == 'imports'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_RESOURCES_IMPORTHOOK'),
            \Hubzero\Facades\Route::url('index.php?option=' . $option . '&controller=importhooks'),
            $controllerName == 'importhooks'
        );

        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
