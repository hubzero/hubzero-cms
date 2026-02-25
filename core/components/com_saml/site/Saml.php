<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\saml\Site;

use Hubzero\Component\AbstractComponent;

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
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'saml');

        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)))) {
            throw new \Exception('Specified controller does not exist.', 404);
        }


        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
