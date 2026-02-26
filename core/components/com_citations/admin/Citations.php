<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Admin;

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
class Citations extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!User::authorise('core.manage', 'com_citations')) {
            App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = Request::getCmd('controller', 'citations');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'citations';
        }

        Submenu::addEntry(
            Lang::txt('CITATIONS'),
            Route::url('index.php?option=com_citations&controller=citations'),
            ($controllerName == 'citations' && Request::getCmd('task', '') != 'stats')
        );
        Submenu::addEntry(
            Lang::txt('CITATION_STATS'),
            Route::url('index.php?option=com_citations&controller=citations&task=stats'),
            ($controllerName == 'citations' && Request::getCmd('task', '') == 'stats')
        );
        Submenu::addEntry(
            Lang::txt('CITATION_TYPES'),
            Route::url('index.php?option=com_citations&controller=types'),
            $controllerName == 'types'
        );
        Submenu::addEntry(
            Lang::txt('CITATION_SPONSORS'),
            Route::url('index.php?option=com_citations&controller=sponsors'),
            $controllerName == 'sponsors'
        );
        Submenu::addEntry(
            Lang::txt('CITATION_FORMAT'),
            Route::url('index.php?option=com_citations&controller=format'),
            $controllerName == 'format'
        );

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
