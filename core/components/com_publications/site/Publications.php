<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Site;

use Hubzero\Component\AbstractComponent;
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
        $componentPath = \Hubzero\Facades\Component::path('com_publications');
        $sitePath = "$componentPath/site";

        $view = Request::getCmd('view', 'publications');
        $controllerName = Request::getCmd('controller', $view);
        $task = Request::getCmd('task', $view);

        if (!file_exists("$sitePath/controllers/$controllerName.php")) {
            $controllerName = 'publications';
            Request::setVar('task', $task);
        }

        require_once "$sitePath/controllers/$controllerName.php";
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
