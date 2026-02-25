<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Request;

/**
 * Component entry point
 */
class Search extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Authorization check
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_search')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        // Get the preferred search mechanism
        $controller = Request::get('controller', null);
        $engine = Request::getWord('controller', null);

        // Prevent HUBgraph from being configured
        if (strtolower($engine) == 'hubgraph') {
            $engine = 'basic';
        }

        if ($engine != 'basic' && $engine != 'hubgraph') {
            if ($controller == null) {
                $controllerName = \Hubzero\Facades\Component::params('com_search')->get('engine', 'basic');
                $controllerName = ($controllerName == 'hubgraph' ? 'basic' : $controllerName);
            } else {
                $controllerName = $controller;
            }
        }

        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('Controller not found'));
        }
        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
