<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Request;

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

        $controllerName = Request::getCmd('controller', 'jobs');
        if (!is_subclass_of(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)), \Hubzero\Component\ControllerInterface::class)) {
            $controllerName = 'jobs';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
