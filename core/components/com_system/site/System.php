<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\App;
use Hubzero\Facades\Request;

/**
 * Component entry point
 */
class System extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        App::abort(404);

        $controllerName = Request::getCmd('controller', Request::getCmd('view', 'info'));
        if (!is_subclass_of(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)), \Hubzero\Component\ControllerInterface::class)) {
            App::abort(404);
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
