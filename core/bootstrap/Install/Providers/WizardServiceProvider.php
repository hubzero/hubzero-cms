<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Providers;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;
use Hubzero\Base\Traits\ErrorBag;

/**
 * Web Installation Wizard Service Provider
 *
 * Handles the multi-step web-based installation wizard.
 */
class WizardServiceProvider extends Middleware
{
    use ErrorBag;

    /**
     * Handle request in HTTP stack
     *
     * @param   object  $request  HTTP Request
     * @return  mixed
     */
    public function handle(Request $request)
    {
        $response = $this->next($request);

        // Check for Windows - HUBzero does not support Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            http_response_code(500);
            ob_start();
            include dirname(__DIR__) . '/web/views/tmpl/windows-error.php';
            $response->setContent(ob_get_clean());
            return $response;
        }

        // Note: Don't redirect when installed - let the web installer
        // handle showing the completion page instead

        // Start session for wizard state if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_name('hubzero_install');
            session_start();
        }

        // Define constants needed by the installer
        if (!defined('HUBZERO_INSTALL')) {
            define('HUBZERO_INSTALL', 1);
        }
        if (!defined('INSTALL_ROOT')) {
            define('INSTALL_ROOT', dirname(__DIR__));
        }
        // PATH_CORE, PATH_ROOT, PATH_APP are already defined by the framework

        // Load and run the installer
        require_once dirname(__DIR__) . '/web/Installer.php';

        ob_start();
        $installer = new \Bootstrap\Install\Web\Installer();
        $installer->run();
        $contents = ob_get_contents();
        ob_end_clean();

        $response->setContent($contents);

        return $response;
    }
}
