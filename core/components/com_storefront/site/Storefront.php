<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Site;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Storefront extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        //build controller path and name
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', '');

        if (empty($controllerName)) {
            // Load default controller if no controller provided
            $controllerName = 'storefront';
        } elseif (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('Page Not Found'));
        }

        $controllerRequested = $controllerName;

        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller and execute
        $controller = new $controllerName();

        // See if user has to be logged in to see the component
        $loginRequired = $controller->config->get('requirelogin', 0);

        if ($loginRequired && $controllerRequested != 'overview') {
            // Check if they're logged in
            if (\Hubzero\Facades\User::isGuest()) {
                $return = base64_encode($_SERVER['REQUEST_URI']);
                // Redirect to the landing page
                if ($controllerRequested == 'storefront') {
                    \Hubzero\Facades\App::redirect(
                        \Hubzero\Facades\Route::url('index.php?option=com_storefront') . 'overview'
                    );
                }
                // Require login
                \Hubzero\Facades\App::redirect(
                    \Hubzero\Facades\Route::url('index.php?option=com_users&view=login&return=' . $return),
                    'Please login to continue',
                    'warning'
                );
            }
        }

        // Update any restrictions that were entered before the account existed
        // @TODO: Move to a plugin that responds after login?
        if (!\Hubzero\Facades\User::isGuest()) {
            \Components\Storefront\Admin\Helpers\RestrictionsHelper::updateUser(
                \Hubzero\Facades\User::get('id'),
                \Hubzero\Facades\User::get('username')
            );
        }

        $controller->execute();
        $controller->redirect();
    }
}
