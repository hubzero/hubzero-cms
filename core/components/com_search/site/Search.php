<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Site;

use Hubzero\Component\AbstractComponent;

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
        $config = \Hubzero\Facades\Component::params('com_search');

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', \Hubzero\Facades\Request::getCmd('view', $config->get('engine', 'basic')));

        if ($controllerName != 'basic') {
            $controllerName = 'solr';
        }

        // Are we falling back to the default engine?
        $fallback = \Hubzero\Facades\App::get('session')->get('searchfallback');
        if ($fallback && intval($fallback) <= time()) {
            // Don't fallback if the time limit has expired
            $fallback = null;
        }

        // Are we explicitly forcing the engine?
        if ($force = \Hubzero\Facades\Request::getCmd('engine')) {
            $fallback = null;
            $controllerName = $force;
        }

        if ($fallback || !class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)))) {
            $controllerName = 'basic';
        }

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
