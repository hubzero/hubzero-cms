<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Publications extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_publications')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        // get controller name
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'items');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'items';
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_PUBLICATIONS_PUBLICATIONS'),
            \Hubzero\Facades\Route::url('index.php?option=com_publications&controller=items'),
            $controllerName == 'items'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_PUBLICATIONS_LICENSES'),
            \Hubzero\Facades\Route::url('index.php?option=com_publications&controller=licenses'),
            $controllerName == 'licenses'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_PUBLICATIONS_CATEGORIES'),
            \Hubzero\Facades\Route::url('index.php?option=com_publications&controller=categories'),
            $controllerName == 'categories'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_PUBLICATIONS_MASTER_TYPES'),
            \Hubzero\Facades\Route::url('index.php?option=com_publications&controller=types'),
            $controllerName == 'types'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_PUBLICATIONS_BATCH_CREATE'),
            \Hubzero\Facades\Route::url('index.php?option=com_publications&controller=batchcreate'),
            $controllerName == 'batchcreate'
        );
        require_once \Hubzero\Facades\Component::path('com_plugins') . DS . 'helpers' . DS . 'plugins.php';
        if (\Components\Plugins\Helpers\Plugins::getActions()->get('core.manage')) {
            \Hubzero\Facades\Submenu::addEntry(
                \Hubzero\Facades\Lang::txt('COM_PUBLICATIONS_PLUGINS'),
                \Hubzero\Facades\Route::url(
                    'index.php?option=com_plugins&view=plugins&filter_folder=publications&filter_type=publications'
                )
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
