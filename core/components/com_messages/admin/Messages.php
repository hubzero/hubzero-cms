<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Messages\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Messages extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Access check.
        if (!\User::authorise('core.manage', \Request::getCmd('extension'))) {
            \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Request::getCmd('controller', 'messages');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'messages';
        }
        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Execute the task.
        $controller = new $controllerName();
        $controller->execute();
    }
}
