<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Component;
use Hubzero\Facades\Request;

/**
 * Component entry point
 */
class Publications extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        $componentPath = Component::path('com_publications');
        $sitePath = "$componentPath/site";

        $view = Request::getCmd('view', 'publications');
        $controllerName = Request::getCmd('controller', $view);
        $task = Request::getCmd('task', $view);

        if (!file_exists("$sitePath/controllers/$controllerName.php")) {
            $controllerName = 'publications';
            Request::setVar('task', $task);
        }

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        /** @var \Hubzero\Component\ControllerInterface $controller Every controller extends one that is */
        $controller = new $controllerName();
        $controller->execute();
    }
}
