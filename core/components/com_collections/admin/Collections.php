<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Admin;

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
class Collections extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!User::authorise('core.manage', 'com_collections')) {
            App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $controllerName = Request::getCmd('controller', 'collections');
        if (
            !is_subclass_of(
                __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName),
                \Hubzero\Component\ControllerInterface::class,
            )
        ) {
            $controllerName = 'collections';
        }

        Submenu::addEntry(
            Lang::txt('COM_COLLECTIONS_COLLECTIONS'),
            Route::url('index.php?option=com_collections'),
            $controllerName == 'collections',
        );
        Submenu::addEntry(
            Lang::txt('COM_COLLECTIONS_POSTS'),
            Route::url('index.php?option=com_collections&controller=posts&collection_id=0&item_id=0'),
            $controllerName == 'posts',
        );
        Submenu::addEntry(
            Lang::txt('COM_COLLECTIONS_ITEMS'),
            Route::url('index.php?option=com_collections&controller=items'),
            $controllerName == 'items',
        );

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        /** @var \Hubzero\Component\ControllerInterface $controller Every controller extends one that is */
        $controller = new $controllerName();
        $controller->execute();
    }
}
