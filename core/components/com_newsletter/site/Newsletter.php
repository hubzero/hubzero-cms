<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Site;

use Hubzero\Utility\Arr;
use Hubzero\Facades\Request;
use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Newsletter extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // determine the controller to use:
        $defaultController = 'newsletters';

        // controllers from the reply functionality
        $controllerNameMap = [
            'email-subscriptions' => 'emailsubscriptions',
            'pages' => 'pages',
            'replies' => 'replies'
        ];

        // if we had a controller request, set it, otherwise set 'newsletters':
        $controllerName = Request::getCmd('controller', $defaultController);
        if (in_array($controllerName, array_keys($controllerNameMap))) {
            // from reply component
            $controllerName = Arr::getValue($controllerNameMap, $controllerName);
        }

        //build controller path and require it
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)))) {
            $controllerName = $defaultController;
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller and execute
        $controller = new $controllerName();
        $controller->execute();
    }
}
