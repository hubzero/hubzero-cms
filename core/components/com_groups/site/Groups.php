<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Request;

/**
 * Component entry point
 */
class Groups extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        //build controller path and name
        $controllerName = Request::getCmd('controller', Request::getCmd('view', 'groups'));
        if (
            !is_subclass_of(
                __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)),
                \Hubzero\Component\ControllerInterface::class,
            )
        ) {
            $controllerName = 'groups';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller and execute
        /** @var \Hubzero\Component\SiteController $controller Every controller here extends it */
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
