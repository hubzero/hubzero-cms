<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Admin;

use Hubzero\Component\AbstractComponent;

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
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_citations')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'citations');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'citations';
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('CITATIONS'),
            \Hubzero\Facades\Route::url('index.php?option=com_citations&controller=citations'),
            ($controllerName == 'citations' && \Hubzero\Facades\Request::getCmd('task', '') != 'stats')
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('CITATION_STATS'),
            \Hubzero\Facades\Route::url('index.php?option=com_citations&controller=citations&task=stats'),
            ($controllerName == 'citations' && \Hubzero\Facades\Request::getCmd('task', '') == 'stats')
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('CITATION_TYPES'),
            \Hubzero\Facades\Route::url('index.php?option=com_citations&controller=types'),
            $controllerName == 'types'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('CITATION_SPONSORS'),
            \Hubzero\Facades\Route::url('index.php?option=com_citations&controller=sponsors'),
            $controllerName == 'sponsors'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('CITATION_FORMAT'),
            \Hubzero\Facades\Route::url('index.php?option=com_citations&controller=format'),
            $controllerName == 'format'
        );

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
