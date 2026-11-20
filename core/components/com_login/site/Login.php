<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Login\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Request;

/**
 * Component entry point
 */
class Login extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Maintian backwards compatibility
        if ($view = Request::getCmd('view')) {
            if ($view != 'login' && !Request::getCmd('task')) {
                Request::setVar('task', $view);
            }
        }

        $controller = new Controllers\Auth();
        $controller->execute();
    }
}
