<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Content\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Request;

/**
 * Component entry point
 */
class Content extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Access check.
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_content')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $task = Request::getCmd('task');
        if (strpos($task, '.') !== false) {
            $splitTask = explode('.', $task);
            Request::setVar('task', $splitTask[1]);
        }
        $defaultController = 'articles';
        $controllerName = Request::getCmd('controller', $defaultController);

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_CONTENT_ARTICLES'),
            \Hubzero\Facades\Route::url('index.php?option=com_content&controller=' . $defaultController),
            ($controllerName == $defaultController)
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_CONTENT_SUBMENU_CATEGORIES'),
            \Hubzero\Facades\Route::url('index.php?option=com_categories&extension=com_content')
        );

        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)))) {
            $controllerName = $defaultController;
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        $controller = new $controllerName();
        $controller->execute();
    }
}
