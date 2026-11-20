<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Request;

/**
 * Component entry point
 */
class Wiki extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        $controllerName = Request::getCmd('controller', Request::getCmd('view', 'pages'));
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);
        if (!is_subclass_of($controllerName, \Hubzero\Component\ControllerInterface::class)) {
            $controllerName = Controllers\Pages::class;
        }

        // Instantiate controller
        $controller = new $controllerName(['name' => 'wiki']);
        $controller->execute();
    }
}
