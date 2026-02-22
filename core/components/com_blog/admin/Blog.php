<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Blog extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\User::authorise('core.manage', 'com_blog')) {
            \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $scope = \Request::getCmd('scope', 'site');
        $controllerName = \Request::getCmd('controller', 'entries');

        \Submenu::addEntry(
            \Lang::txt('COM_BLOG_MENU_ENTRIES'),
            \Route::url('index.php?option=com_blog&controller=entries'),
            ($controllerName == 'entries')
        );
        \Submenu::addEntry(
            \Lang::txt('COM_BLOG_MENU_COMMENTS'),
            \Route::url('index.php?option=com_blog&controller=comments'),
            ($controllerName == 'comments')
        );

        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'entries';
        }
        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
