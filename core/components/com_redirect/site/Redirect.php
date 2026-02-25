<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Redirect\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Route;
use Hubzero\Facades\Request;
use Hubzero\Facades\App;
use Hubzero\Facades\Component;

/**
 * Component entry point
 */
class Redirect extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        $component_name = basename(dirname(__DIR__));

        // Include publication model
        $componentPath = Component::path($component_name);
        $sitePath = "$componentPath/site";

        $view = Request::getCmd('view', 'display');
        $controllerName = Request::getCmd('controller', $view);
        $task = Request::getCmd('task', $view);

        $params = Component::params($component_name);
        /*if (empty($params->toArray())){
            App::redirect(
                Route::url('index.php?option=com_tools' , false)
            );
            exit();
        }*/

        if (!file_exists("$sitePath/controllers/$controllerName.php")) {
            $controllerName = 'redirect';
            Request::setVar('task', $task);
        }

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));
        //print($controllerName); exit();
        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
