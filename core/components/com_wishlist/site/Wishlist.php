<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Site;

use Hubzero\Component\AbstractComponent;

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
        require_once dirname(__DIR__) . DS . 'helpers' . DS . 'economy.php';
        require_once dirname(__DIR__) . DS . 'helpers' . DS . 'html.php';
        require_once dirname(__DIR__) . DS . 'models' . DS . 'wishlist.php';

        $controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'wishlists'));
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'wishlists';
        }
        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
