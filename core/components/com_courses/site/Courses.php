<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Site;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Courses extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Build controller path and name
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', \Hubzero\Facades\Request::getCmd('view', 'courses'));
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)))) {
            $controllerName = 'courses';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller and execute
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
