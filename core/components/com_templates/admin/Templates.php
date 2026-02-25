<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Templates\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Lang;

/**
 * Component entry point
 */
class Templates extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Access check.
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_templates')) {
            \Hubzero\Facades\App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        // Include controller
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'styles');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'styles';
        }

        \Components\Templates\Helpers\Utilities::addSubmenu($controllerName);

        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
