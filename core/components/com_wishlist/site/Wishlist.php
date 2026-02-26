<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Request;

/**
 * Component entry point
 */
class Wishlist extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        $controllerName = Request::getCmd('controller', Request::getCmd('view', 'wishlists'));
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)))) {
            $controllerName = 'wishlists';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
