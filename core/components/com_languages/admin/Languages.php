<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Languages\Admin;

use Hubzero\Facades\Submenu;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Route;
use Hubzero\Facades\Request;

/**
 * Component entry point
 */
class Languages extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Access check.
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_languages')) {
            App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'installed');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            App::abort(404, Lang::txt('Controller not found.'));
        }

        Submenu::addEntry(
            Lang::txt('COM_LANGUAGES_SUBMENU_INSTALLED_SITE'),
            Route::url('index.php?option=com_languages&controller=installed&client=0'),
            ($controllerName == 'installed')
        );
        Submenu::addEntry(
            Lang::txt('COM_LANGUAGES_SUBMENU_INSTALLED_ADMINISTRATOR'),
            Route::url('index.php?option=com_languages&controller=installed&client=1'),
            ($controllerName == 'installed' && Request::getInt('client'))
        );
        Submenu::addEntry(
            Lang::txt('COM_LANGUAGES_SUBMENU_CONTENT'),
            Route::url('index.php?option=com_languages&controller=languages'),
            ($controllerName == 'languages')
        );
        Submenu::addEntry(
            Lang::txt('COM_LANGUAGES_SUBMENU_OVERRIDES'),
            Route::url('index.php?option=com_languages&controller=overrides'),
            ($controllerName == 'overrides')
        );

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
