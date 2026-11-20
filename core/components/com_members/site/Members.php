<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Request;

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
        $controllerName = Request::getCmd('controller', Request::getCmd('view', 'profiles'));

        // com_users hands /login/remind and /login/reset over as view=remind or
        // view=reset (the router names the controller for /members/reset); there
        // is no such controller, so these showed the member directory instead
        if (in_array($controllerName, ['remind', 'reset'])) {
            $controllerName = 'credentials';
        }
        if (
            !is_subclass_of(
                __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)),
                \Hubzero\Component\ControllerInterface::class,
            )
        ) {
            $controllerName = 'profiles';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        /** @var \Hubzero\Component\ControllerInterface $controller Every controller extends one that is */
        $controller = new $controllerName();
        $controller->execute();
    }
}
