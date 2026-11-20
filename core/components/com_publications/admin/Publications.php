<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Submenu;
use Hubzero\Facades\User;

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
        if (!User::authorise('core.manage', 'com_publications')) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        // get controller name
        $controllerName = Request::getCmd('controller', 'items');
        if (
            !is_subclass_of(
                __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName),
                \Hubzero\Component\ControllerInterface::class,
            )
        ) {
            $controllerName = 'items';
        }

        Submenu::addEntry(
            Lang::txt('COM_PUBLICATIONS_PUBLICATIONS'),
            Route::url('index.php?option=com_publications&controller=items'),
            $controllerName == 'items',
        );
        Submenu::addEntry(
            Lang::txt('COM_PUBLICATIONS_LICENSES'),
            Route::url('index.php?option=com_publications&controller=licenses'),
            $controllerName == 'licenses',
        );
        Submenu::addEntry(
            Lang::txt('COM_PUBLICATIONS_CATEGORIES'),
            Route::url('index.php?option=com_publications&controller=categories'),
            $controllerName == 'categories',
        );
        Submenu::addEntry(
            Lang::txt('COM_PUBLICATIONS_MASTER_TYPES'),
            Route::url('index.php?option=com_publications&controller=types'),
            $controllerName == 'types',
        );
        Submenu::addEntry(
            Lang::txt('COM_PUBLICATIONS_BATCH_CREATE'),
            Route::url('index.php?option=com_publications&controller=batchcreate'),
            $controllerName == 'batchcreate',
        );

        if (\Components\Plugins\Helpers\Plugins::getActions()->get('core.manage')) {
            Submenu::addEntry(
                Lang::txt('COM_PUBLICATIONS_PLUGINS'),
                Route::url(
                    'index.php?option=com_plugins&view=plugins&filter_folder=publications&filter_type=publications',
                ),
            );
        }

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        /** @var \Hubzero\Component\SiteController $controller Every controller here extends it */
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
