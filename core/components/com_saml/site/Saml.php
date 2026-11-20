<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\saml\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Request;

/**
 * Component entry point
 */
class Saml extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        $controllerName = Request::getCmd('controller', 'saml');

        if (
            !is_subclass_of(
                __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)),
                \Hubzero\Component\ControllerInterface::class,
            )
        ) {
            throw new \Exception('Specified controller does not exist.', 404);
        }


        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        /** @var \Hubzero\Component\ControllerInterface $controller Every controller extends one that is */
        $controller = new $controllerName();
        $controller->execute();
    }
}
