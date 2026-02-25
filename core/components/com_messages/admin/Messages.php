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
        if (!\Hubzero\Facades\User::authorise('core.manage', \Hubzero\Facades\Request::getCmd('extension'))) {
            \Hubzero\Facades\App::abort(403, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'messages');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'messages';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Execute the task.
        $controller = new $controllerName();
        $controller->execute();
    }
}
