<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Members extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_members')) {
            \Hubzero\Facades\App::abort(403, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'members');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'members';
        }

        // Build sub-menu
        \Components\Members\Admin\Helpers\MembersHelper::addSubmenu($controllerName);

        // Instantiate controller
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        $controller = new $controllerName();
        $controller->execute();
    }
}
