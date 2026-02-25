<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Activity\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Activity extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_activity')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'entries');

        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)))) {
            $controllerName = 'entries';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
