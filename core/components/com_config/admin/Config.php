<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Config\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Config extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Access checks are done internally because of different requirements for the two controllers.

        // Tell the browser not to cache this page.
        \Hubzero\Facades\App::get('response')->headers->set('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT', true);

        if (strstr(\Hubzero\Facades\Request::getCmd('task'), '.')) {
            @list($ctrl, $task) = explode('.', \Hubzero\Facades\Request::getCmd('task'));
            \Hubzero\Facades\Request::setVar('controller', $ctrl);
            \Hubzero\Facades\Request::setVar('task', $task);
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', \Hubzero\Facades\Request::getCmd('view', 'application'));
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
        }
        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Execute the controller.
        $controller = new $controllerName();
        $controller->execute();
    }
}
