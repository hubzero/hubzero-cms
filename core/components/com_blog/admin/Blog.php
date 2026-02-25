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
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_blog')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $scope = \Hubzero\Facades\Request::getCmd('scope', 'site');
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'entries');

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_BLOG_MENU_ENTRIES'),
            \Hubzero\Facades\Route::url('index.php?option=com_blog&controller=entries'),
            ($controllerName == 'entries')
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_BLOG_MENU_COMMENTS'),
            \Hubzero\Facades\Route::url('index.php?option=com_blog&controller=comments'),
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
