<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Site;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Wiki extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', \Hubzero\Facades\Request::getCmd('view', 'pages'));
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'pages';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName(array('name' => 'wiki'));
        $controller->execute();
    }
}
