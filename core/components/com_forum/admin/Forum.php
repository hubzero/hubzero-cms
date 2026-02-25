<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Forum extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_forum')) {
            \Hubzero\Facades\App::abort(403, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'sections');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'sections';
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_FORUM_SECTIONS'),
            \Hubzero\Facades\Route::url('index.php?option=com_forum&controller=sections'),
            ($controllerName == 'sections')
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_FORUM_CATEGORIES'),
            \Hubzero\Facades\Route::url('index.php?option=com_forum&controller=categories&section_id=-1'),
            ($controllerName == 'categories')
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_FORUM_THREADS'),
            \Hubzero\Facades\Route::url('index.php?option=com_forum&controller=threads&category_id=-1'),
            ($controllerName == 'threads')
        );

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
