<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Feedback\Admin;

use Hubzero\Component\AbstractComponent;

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
        if (!\User::authorise('core.manage', 'com_feedback')) {
            \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = 'quotes';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
