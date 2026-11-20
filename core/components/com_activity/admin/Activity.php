<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Activity\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;

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
        if (!User::authorise('core.manage', 'com_activity')) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $controllerName = Request::getCmd('controller', 'entries');

        if (
            !is_subclass_of(
                __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)),
                \Hubzero\Component\ControllerInterface::class,
            )
        ) {
            $controllerName = 'entries';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        /** @var \Hubzero\Component\ControllerInterface $controller Every controller extends one that is */
        $controller = new $controllerName();
        $controller->execute();
    }
}
