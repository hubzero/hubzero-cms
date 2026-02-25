<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Site;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Jobs extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        include_once \Hubzero\Facades\Component::path('com_services') . DS . 'models' . DS . 'service.php';
        include_once \Hubzero\Facades\Component::path('com_services') . DS . 'models' . DS . 'subscription.php';

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'jobs');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'jobs';
        }
        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
