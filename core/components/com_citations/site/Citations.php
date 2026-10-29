<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Request;

/**
 * Component entry point
 */
class Citations extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {

        $controllerName = Request::getCmd('controller', Request::getCmd('view', 'citations'));
        if (!is_subclass_of(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)), \Hubzero\Component\ControllerInterface::class)) {
            $controllerName = 'citations';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
