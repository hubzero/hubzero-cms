<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Feedback\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Request;

/**
 * Component entry point
 */
class Feedback extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        $controllerName = Request::getCmd('controller', Request::getCmd('view', 'feedback'));
        if (
            !is_subclass_of(
                __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName),
                \Hubzero\Component\ControllerInterface::class,
            )
        ) {
            $controllerName = 'feedback';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        /** @var \Hubzero\Component\ControllerInterface $controller Every controller extends one that is */
        $controller = new $controllerName();
        $controller->execute();
    }
}
