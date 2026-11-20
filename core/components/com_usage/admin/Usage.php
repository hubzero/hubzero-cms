<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Usage\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;

/**
 * Component entry point
 */
class Usage extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Authorization check
        if (!User::authorise('core.manage', 'com_usage')) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $controllerName = Request::getCmd('controller', 'data');
        if (
            !is_subclass_of(
                __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName),
                \Hubzero\Component\ControllerInterface::class,
            )
        ) {
            $controllerName = 'data';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        /** @var \Hubzero\Component\SiteController $controller Every controller here extends it */
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
